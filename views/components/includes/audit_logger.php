<?php
/**
 * =============================================================================
 * AUDIT LOGGER — Recording Everything That Happens
 * =============================================================================
 * 
 * WHAT IS AN AUDIT TRAIL?
 *   An audit trail is a chronological record of ALL important actions
 *   in the system. Like a security camera for your code.
 *
 * WHY IS IT IMPORTANT?
 *   1. TRANSPARENCY: Proves the election was fair
 *      "Student 23/U/001 voted at 2:30 PM from IP 192.168.1.5"
 *   
 *   2. SECURITY: Detects suspicious activity
 *      "Admin tried to delete votes at 3 AM" → Red flag!
 *   
 *   3. DEBUGGING: Helps find bugs
 *      "Error occurred after student 23/U/045 voted" → Trace the issue
 *   
 *   4. COMPLIANCE: Required by most election regulations
 *      Your university's IT department will want to see this!
 *
 * WHAT GETS LOGGED:
 *   | Action            | When It Happens                        |
 *   |-------------------|----------------------------------------|
 *   | STUDENT_LOGIN     | Student successfully logs in           |
 *   | STUDENT_LOGOUT    | Student clicks logout                  |
 *   | VOTE_CAST         | Student submits their ballot            |
 *   | ADMIN_LOGIN       | Admin logs into the dashboard          |
 *   | ADMIN_LOGOUT      | Admin logs out                         |
 *   | CANDIDATE_ADDED   | Admin adds a new candidate             |
 *   | ELECTION_CREATED  | Admin creates a new election           |
 *   | RESULTS_PUBLISHED | Admin publishes election results       |
 *
 * HOW IT'S USED:
 *   // Log a student login:
 *   log_audit_event($conn, '23/U/001', 'STUDENT_LOGIN', 'Student logged in');
 *
 *   // Log a vote (without revealing WHO they voted for):
 *   log_audit_event($conn, '23/U/001', 'VOTE_CAST', 'Voted for 3 positions');
 *
 * =============================================================================
 */

/**
 * Ensure the audit_log table exists in the database.
 * 
 * This function creates the table if it doesn't already exist.
 * "CREATE TABLE IF NOT EXISTS" means:
 *   - If the table already exists → do nothing
 *   - If it doesn't exist → create it
 * 
 * The "static $initialized" variable is a clever optimization:
 *   - Static variables keep their value between function calls
 *   - First call: $initialized = false → create table → set to true
 *   - Second call: $initialized = true → skip (table already exists)
 *   - This avoids running the CREATE TABLE query on every single page load
 *
 * @param mysqli $conn The database connection object
 * @return void
 */
function ensure_audit_log_table(mysqli $conn): void
{
    // Static variable retains its value between function calls
    // First call: $initialized = false (default)
    // After first call: $initialized = true (persists!)
    static $initialized = false;
    if ($initialized) {
        return; // Table already confirmed to exist — skip the query
    }

    // SQL to create the table (only if it doesn't exist yet)
    $sql = "CREATE TABLE IF NOT EXISTS audit_log (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(50),
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    $conn->query($sql);
    $initialized = true; // Remember: don't check again this request
}

/**
 * Log an audit event to the database.
 * 
 * This is the main function you call throughout the application
 * to record important events.
 *
 * DESIGN DECISIONS:
 *   - Wrapped in try-catch: Audit logging should NEVER crash your app.
 *     If logging fails, the main action (login, vote, etc.) should
 *     still succeed. We just log the failure to PHP's error log.
 *   
 *   - Uses prepared statements: Even audit logging must be safe from
 *     SQL injection. What if an attacker's student ID contains SQL?
 *   
 *   - IP address captured: $_SERVER['REMOTE_ADDR'] gives us the 
 *     user's IP address. Useful for detecting fraud.
 *     VARCHAR(45) supports both IPv4 ("192.168.1.1") and 
 *     IPv6 ("2001:0db8:85a3:0000:0000:8a2e:0370:7334")
 *
 * @param mysqli $conn     The database connection
 * @param string|null $user_id  Who performed the action (student ID or admin ID)
 * @param string $action   What happened (e.g., 'VOTE_CAST', 'STUDENT_LOGIN')
 * @param string $details  Human-readable description of what happened
 * @return void
 */
function log_audit_event(mysqli $conn, ?string $user_id, string $action, string $details = ''): void
{
    try {
        // Make sure the audit_log table exists
        ensure_audit_log_table($conn);

        // Get the user's IP address
        // $_SERVER['REMOTE_ADDR'] is set by Apache for every request
        // The ?? '' provides an empty string fallback (e.g., CLI scripts)
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Prepare and execute the INSERT query
        $stmt = $conn->prepare(
            "INSERT INTO audit_log (user_id, action, details, ip_address) 
             VALUES (?, ?, ?, ?)"
        );
        
        // If prepare fails (e.g., table doesn't exist), don't crash
        if ($stmt === false) {
            return;
        }

        // Normalize user_id: null → '', " 23/U/001 " → "23/U/001"
        $normalized_user_id = $user_id !== null ? trim($user_id) : '';
        
        // "ssss" = all 4 parameters are strings
        $stmt->bind_param("ssss", $normalized_user_id, $action, $details, $ip_address);
        $stmt->execute();
        $stmt->close();
        
    } catch (Throwable $e) {
        // If audit logging fails, DON'T crash the application!
        // Just write to PHP's error log for later investigation.
        // The main application flow continues unaffected.
        error_log("Audit log write failed: " . $e->getMessage());
    }
}
