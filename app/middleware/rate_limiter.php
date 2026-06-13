<?php
/**
 * =============================================================================
 * RATE LIMITER — Preventing Brute Force Attacks
 * =============================================================================
 * 
 * WHAT IS BRUTE FORCE?
 *   An attacker tries thousands of passwords until one works.
 *   Without rate limiting, they can try unlimited passwords per second.
 *
 * HOW THIS WORKS:
 *   We track failed login attempts in the database:
 *   1. Each failed login → increment counter for that IP + identifier
 *   2. After MAX_ATTEMPTS (5) → block for LOCKOUT_DURATION (15 min)
 *   3. Successful login → reset the counter
 *   4. Old attempts automatically cleaned up
 *
 * WHY TRACK BY IP + IDENTIFIER?
 *   - By IP: Stops one computer from attacking multiple accounts
 *   - By identifier: Stops attacks from multiple IPs on one account
 *   - Both together: Defense in depth!
 *
 * DATABASE TABLE (auto-created):
 *   login_attempts: ip_address, identifier, attempts, last_attempt
 *
 * USAGE:
 *   // Before processing login:
 *   if (RateLimiter::isLocked($conn, $_SERVER['REMOTE_ADDR'], $student_id)) {
 *       $minutes = RateLimiter::getLockoutRemaining($conn, ...);
 *       die("Too many attempts. Try again in $minutes minutes.");
 *   }
 *
 *   // On failed login:
 *   RateLimiter::recordFailedAttempt($conn, $_SERVER['REMOTE_ADDR'], $student_id);
 *
 *   // On successful login:
 *   RateLimiter::resetAttempts($conn, $_SERVER['REMOTE_ADDR'], $student_id);
 *
 * =============================================================================
 */

class RateLimiter
{
    // ─── Configuration ───────────────────────────────────────────────────────
    // How many failed attempts before locking
    const MAX_ATTEMPTS = 5;
    
    // How long to lock out (in minutes)
    const LOCKOUT_DURATION = 15;
    
    // How old attempts need to be before cleanup (in hours)
    const CLEANUP_AFTER_HOURS = 24;

    /**
     * Ensure the login_attempts table exists.
     * 
     * Uses the same pattern as audit_logger.php — create if not exists,
     * with a static flag to avoid running the query on every page load.
     *
     * @param mysqli $conn Database connection
     */
    private static function ensureTable(mysqli $conn): void
    {
        static $initialized = false;
        if ($initialized) return;

        $conn->query("CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            identifier VARCHAR(100) NOT NULL,
            attempts INT DEFAULT 1,
            last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            UNIQUE KEY uniq_ip_identifier (ip_address, identifier),
            INDEX idx_last_attempt (last_attempt)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $initialized = true;
    }

    /**
     * Check if a login is currently locked out.
     * 
     * Returns true if there have been too many failed attempts
     * within the lockout duration.
     *
     * THE SQL EXPLAINED:
     *   WHERE ip_address = ? OR identifier = ?
     *     → Match by IP address OR by the student/admin ID
     *     → This catches attacks from multiple IPs on one account
     *     → AND attacks from one IP on multiple accounts
     *   
     *   AND last_attempt > NOW() - INTERVAL 15 MINUTE
     *     → Only count recent attempts (within lockout window)
     *   
     *   AND attempts >= 5
     *     → Only lock if they've hit the max
     *
     * @param mysqli $conn       Database connection
     * @param string $ip         User's IP address
     * @param string $identifier Student ID or admin username
     * @return bool              true if locked out
     */
    public static function isLocked(mysqli $conn, string $ip, string $identifier): bool
    {
        self::ensureTable($conn);

        $lockout = self::LOCKOUT_DURATION;
        $max = self::MAX_ATTEMPTS;

        $stmt = $conn->prepare(
            "SELECT attempts, last_attempt FROM login_attempts 
             WHERE (ip_address = ? OR identifier = ?)
             AND last_attempt > NOW() - INTERVAL ? MINUTE
             AND attempts >= ?
             LIMIT 1"
        );
        $stmt->bind_param("ssii", $ip, $identifier, $lockout, $max);
        $stmt->execute();
        $result = $stmt->get_result();
        $locked = $result->num_rows > 0;
        $stmt->close();

        return $locked;
    }

    /**
     * Get remaining lockout time in minutes.
     *
     * @param mysqli $conn       Database connection
     * @param string $ip         User's IP address
     * @param string $identifier Student ID or admin username
     * @return int               Minutes remaining (0 if not locked)
     */
    public static function getLockoutRemaining(mysqli $conn, string $ip, string $identifier): int
    {
        self::ensureTable($conn);

        $lockout = self::LOCKOUT_DURATION;
        $max = self::MAX_ATTEMPTS;

        $stmt = $conn->prepare(
            "SELECT last_attempt FROM login_attempts 
             WHERE (ip_address = ? OR identifier = ?)
             AND attempts >= ?
             ORDER BY last_attempt DESC LIMIT 1"
        );
        $stmt->bind_param("ssi", $ip, $identifier, $max);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $last = strtotime($row['last_attempt']);
            $unlock_at = $last + ($lockout * 60);
            $remaining = $unlock_at - time();
            $stmt->close();
            return $remaining > 0 ? (int)ceil($remaining / 60) : 0;
        }
        
        $stmt->close();
        return 0;
    }

    /**
     * Record a failed login attempt.
     * 
     * Uses MySQL's INSERT ... ON DUPLICATE KEY UPDATE syntax:
     *   - First failure: INSERT new row with attempts = 1
     *   - Subsequent failures: UPDATE existing row, increment attempts
     * 
     * This is an "upsert" — insert or update in one query.
     *
     * @param mysqli $conn       Database connection
     * @param string $ip         User's IP address
     * @param string $identifier Student ID or admin username
     */
    public static function recordFailedAttempt(mysqli $conn, string $ip, string $identifier): void
    {
        self::ensureTable($conn);

        $stmt = $conn->prepare(
            "INSERT INTO login_attempts (ip_address, identifier, attempts, last_attempt)
             VALUES (?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE 
                attempts = attempts + 1,
                last_attempt = NOW()"
        );
        $stmt->bind_param("ss", $ip, $identifier);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Get the current number of failed attempts.
     *
     * @param mysqli $conn       Database connection
     * @param string $ip         User's IP address
     * @param string $identifier Student ID or admin username
     * @return int               Number of failed attempts
     */
    public static function getAttemptCount(mysqli $conn, string $ip, string $identifier): int
    {
        self::ensureTable($conn);

        $stmt = $conn->prepare(
            "SELECT attempts FROM login_attempts 
             WHERE (ip_address = ? OR identifier = ?)
             ORDER BY attempts DESC LIMIT 1"
        );
        $stmt->bind_param("ss", $ip, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            return (int)$row['attempts'];
        }
        
        $stmt->close();
        return 0;
    }

    /**
     * Reset failed attempts after successful login.
     * 
     * When someone logs in successfully, clear their failure count.
     * This prevents legitimate users from being permanently locked out
     * after they eventually remember their password.
     *
     * @param mysqli $conn       Database connection
     * @param string $ip         User's IP address
     * @param string $identifier Student ID or admin username
     */
    public static function resetAttempts(mysqli $conn, string $ip, string $identifier): void
    {
        self::ensureTable($conn);

        $stmt = $conn->prepare(
            "DELETE FROM login_attempts 
             WHERE ip_address = ? OR identifier = ?"
        );
        $stmt->bind_param("ss", $ip, $identifier);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Clean up old login attempt records.
     * 
     * Call this periodically (e.g., on admin dashboard load) to prevent
     * the login_attempts table from growing forever.
     *
     * @param mysqli $conn Database connection
     */
    public static function cleanup(mysqli $conn): void
    {
        self::ensureTable($conn);
        
        $hours = self::CLEANUP_AFTER_HOURS;
        $conn->query(
            "DELETE FROM login_attempts 
             WHERE last_attempt < NOW() - INTERVAL {$hours} HOUR"
        );
    }
}
