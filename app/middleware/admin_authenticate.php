<?php
/**
 * =============================================================================
 * ADMIN AUTHENTICATION — With Rate Limiting & CSRF (Phase 3)
 * =============================================================================
 * 
 * SECURITY LAYERS (same as student auth):
 *   1. CSRF verification
 *   2. Rate limiting (5 attempts = 15 min lock)
 *   3. Input validation
 *   4. bcrypt password comparison (via Admin model)
 *   5. Session regeneration
 *   6. Audit logging with IP tracking
 *
 * =============================================================================
 */

if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_login.php");
    exit();
}

// ─── CSRF Verification ──────────────────────────────────────────────────────
verify_csrf_or_die();

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if (empty($username) || empty($password)) {
    header("Location: admin_login.php?error=Username and password are required");
    exit();
}

// ─── Rate Limiting ───────────────────────────────────────────────────────────
if (RateLimiter::isLocked($conn, $ip_address, $username)) {
    $remaining = RateLimiter::getLockoutRemaining($conn, $ip_address, $username);
    
    log_audit_event(
        $conn, $username, 'ADMIN_LOGIN_BLOCKED',
        "Admin login blocked — too many failures. IP: {$ip_address}"
    );
    
    header("Location: admin_login.php?error=" . urlencode(
        "Too many failed attempts. Try again in {$remaining} minute(s)."
    ));
    exit();
}

// ─── Authenticate via Admin Model ────────────────────────────────────────────
$admin = Admin::authenticate($conn, $username, $password);

if ($admin) {
    RateLimiter::resetAttempts($conn, $ip_address, $username);
    session_regenerate_id(true);
    
    $_SESSION['admin_id']       = $admin['admin_id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_role']     = $admin['role'];
    
    log_audit_event(
        $conn, (string)$admin['admin_id'],
        'ADMIN_LOGIN',
        'Admin ' . $admin['username'] . ' logged in from IP: ' . $ip_address
    );
    
    // Clean up old login attempts periodically
    RateLimiter::cleanup($conn);
    
    header("Location: admin_dashboard.php");
    exit();
} else {
    RateLimiter::recordFailedAttempt($conn, $ip_address, $username);
    
    $attempts = RateLimiter::getAttemptCount($conn, $ip_address, $username);
    $remaining = RateLimiter::MAX_ATTEMPTS - $attempts;
    
    log_audit_event(
        $conn, $username, 'ADMIN_LOGIN_FAILED',
        "Failed admin login attempt #{$attempts} for '{$username}' from IP: {$ip_address}"
    );
    
    if ($remaining > 0 && $remaining <= 2) {
        $msg = "Invalid credentials. {$remaining} attempt(s) remaining.";
    } else {
        $msg = "Invalid credentials";
    }
    
    header("Location: admin_login.php?error=" . urlencode($msg));
    exit();
}
?>
