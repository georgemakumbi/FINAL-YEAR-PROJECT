<?php
/**
 * =============================================================================
 * ADMIN RESET PASSWORD — Step 2: Verify OTP + Set New Password
 * =============================================================================
 * Handles the combined OTP-verify + new-password form submission.
 *
 * Security:
 *  - Requires $_SESSION['admin_reset_id'] (set in step 1)
 *  - Rate-limited: max 5 OTP attempts before lockout
 *  - OTP cleared immediately on success (one-time use)
 *  - Password strength enforced server-side
 * =============================================================================
 */

if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_forgot_password.php');
    exit();
}

verify_csrf_or_die();

// ── Gate: must have a pending reset session ───────────────────────────────────
if (empty($_SESSION['admin_reset_id'])) {
    header('Location: admin_forgot_password.php?error=' . urlencode(
        'Session expired. Please request a new reset code.'
    ));
    exit();
}

$admin_id = (int)$_SESSION['admin_reset_id'];

// ── Rate limiting ─────────────────────────────────────────────────────────────
$_SESSION['admin_reset_attempts'] = ($_SESSION['admin_reset_attempts'] ?? 0) + 1;

if ($_SESSION['admin_reset_attempts'] > 5) {
    Admin::clearOtp($conn, $admin_id);
    unset($_SESSION['admin_reset_id'], $_SESSION['admin_reset_attempts']);
    header('Location: admin_forgot_password.php?error=' . urlencode(
        'Too many failed attempts. Please request a new reset code.'
    ));
    exit();
}

// ── Collect inputs ────────────────────────────────────────────────────────────
$otp             = trim($_POST['otp']              ?? '');
$new_password    = $_POST['new_password']           ?? '';
$confirm_password = $_POST['confirm_password']      ?? '';

// ── Validate OTP ──────────────────────────────────────────────────────────────
if (empty($otp)) {
    header('Location: admin_reset_password.php?error=' . urlencode('Please enter the 6-digit code.'));
    exit();
}

$otp_valid = Admin::verifyOtp($conn, $admin_id, $otp);

if (!$otp_valid) {
    $remaining = max(0, 5 - $_SESSION['admin_reset_attempts']);
    header('Location: admin_reset_password.php?error=' . urlencode(
        "Invalid or expired code. {$remaining} attempt(s) remaining."
    ));
    exit();
}

// ── Validate new password ─────────────────────────────────────────────────────
if (strlen($new_password) < 8) {
    header('Location: admin_reset_password.php?error=' . urlencode(
        'Password must be at least 8 characters.'
    ));
    exit();
}

if ($new_password !== $confirm_password) {
    header('Location: admin_reset_password.php?error=' . urlencode('Passwords do not match.'));
    exit();
}

// ── Update password ───────────────────────────────────────────────────────────
$updated = Admin::updatePassword($conn, $admin_id, $new_password);

if (!$updated) {
    header('Location: admin_reset_password.php?error=' . urlencode(
        'Failed to update password. Please try again.'
    ));
    exit();
}

// ── Clean up session ──────────────────────────────────────────────────────────
unset($_SESSION['admin_reset_id'], $_SESSION['admin_reset_attempts']);

// ── Audit log ─────────────────────────────────────────────────────────────────
if (function_exists('log_audit_event')) {
    log_audit_event($conn, (string)$admin_id, 'ADMIN_PASSWORD_RESET',
        'Admin password successfully reset via OTP');
}

header('Location: admin_login.php?success=' . urlencode(
    'Password reset successfully. You can now log in with your new password.'
));
exit();
?>
