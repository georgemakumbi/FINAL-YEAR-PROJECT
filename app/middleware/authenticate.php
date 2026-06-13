<?php
/**
 * =============================================================================
 * STUDENT AUTHENTICATION — With Rate Limiting & CSRF (Phase 3)
 * =============================================================================
 * 
 * SECURITY LAYERS IN THIS FILE:
 *   1. CSRF token verification → Prevents cross-site form forgery
 *   2. Rate limiting → Blocks brute force attacks (5 attempts → 15 min lock)
 *   3. Input validation → Validates student ID format
 *   4. Prepared statements → Prevents SQL injection (via Student model)
 *   5. bcrypt verification → Timing-safe password comparison
 *   6. Session regeneration → Prevents session fixation
 *   7. Audit logging → Records all login attempts
 *
 * =============================================================================
 */

if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// ─── Security Layer 1: CSRF Verification ─────────────────────────────────────
// Verify that this form submission came from OUR login page,
// not from a malicious website.
verify_csrf_or_die();

// ─── Extract & Validate Input ────────────────────────────────────────────────
$student_id = trim($_POST['student_id'] ?? '');
$password   = $_POST['password'] ?? '';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if (empty($student_id) || empty($password)) {
    header("Location: login.php?error=" . urlencode("Student ID and password are required"));
    exit();
}

// ─── Security Layer 2: Rate Limiting ─────────────────────────────────────────
// Check if this IP or student ID has been locked out due to too many failures.
if (RateLimiter::isLocked($conn, $ip_address, $student_id)) {
    $remaining = RateLimiter::getLockoutRemaining($conn, $ip_address, $student_id);
    
    log_audit_event(
        $conn, $student_id, 'LOGIN_BLOCKED',
        "Login blocked — too many failed attempts. Lockout: {$remaining} min remaining. IP: {$ip_address}"
    );
    
    header("Location: login.php?error=" . urlencode(
        "Too many failed login attempts. Please try again in {$remaining} minute(s)."
    ));
    exit();
}

// ─── Security Layer 3: Authenticate via Student Model ────────────────────────
$student = Student::authenticate($conn, $student_id, $password);

if ($student) {
    // ─── SUCCESS: Create Session ─────────────────────────────────────────────
    
    // Reset rate limiter (they got the password right)
    RateLimiter::resetAttempts($conn, $ip_address, $student_id);
    
    session_regenerate_id(true);
    
    $_SESSION['student_id']  = $student['student_id'];
    $_SESSION['first_name']  = $student['first_name'];
    $_SESSION['last_name']   = $student['last_name'];
    $_SESSION['email']       = $student['email'];
    $_SESSION['faculty']     = $student['faculty'];
    $_SESSION['department']  = $student['department'];
    $_SESSION['has_voted']   = $student['has_voted'];
    
    log_audit_event(
        $conn, (string)$student['student_id'],
        'STUDENT_LOGIN',
        'Student ' . $student['student_id'] . ' logged in from IP: ' . $ip_address
    );
    
    if ($student['has_voted']) {
        header("Location: results.php");
    } else {
        header("Location: voting.php");
    }
    exit();
    
} else {
    // ─── FAILURE: Record Failed Attempt ──────────────────────────────────────
    RateLimiter::recordFailedAttempt($conn, $ip_address, $student_id);
    
    $attempts = RateLimiter::getAttemptCount($conn, $ip_address, $student_id);
    $remaining_attempts = RateLimiter::MAX_ATTEMPTS - $attempts;
    
    log_audit_event(
        $conn, $student_id, 'LOGIN_FAILED',
        "Failed login attempt #{$attempts} for '{$student_id}' from IP: {$ip_address}"
    );
    
    // Show remaining attempts warning when getting close to lockout
    if ($remaining_attempts > 0 && $remaining_attempts <= 2) {
        $msg = "Invalid credentials. {$remaining_attempts} attempt(s) remaining before lockout.";
    } else {
        $msg = "Invalid student ID or password";
    }
    
    header("Location: login.php?error=" . urlencode($msg));
    exit();
}
?>
