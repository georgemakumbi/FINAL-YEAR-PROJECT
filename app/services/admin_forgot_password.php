<?php
/**
 * =============================================================================
 * ADMIN FORGOT PASSWORD — Step 1: Send OTP
 * =============================================================================
 * Admin enters their username. If found, an OTP is sent to the email address
 * registered for that admin account.
 *
 * Security notes:
 *  - Always shows a generic "if found, OTP was sent" message to prevent
 *    username enumeration.
 *  - OTP is sent ONLY to the email already stored in the admin table.
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

$username = trim($_POST['username'] ?? '');

// Generic response — don't reveal whether the username exists
$generic_redirect = 'admin_forgot_password.php?success=' . urlencode(
    'If that username is registered, a reset code has been sent to the associated email.'
);

if ($username === '') {
    header('Location: admin_forgot_password.php?error=' . urlencode('Please enter your username.'));
    exit();
}

// Look up the admin
$admin = Admin::findByUsername($conn, $username);

if ($admin === null) {
    // Don't reveal the username doesn't exist
    header("Location: $generic_redirect");
    exit();
}

// Check the admin has an email address on file
if (empty($admin['email'])) {
    error_log("[admin_forgot_password] Admin '{$username}' has no email — cannot send OTP.");
    header("Location: $generic_redirect");
    exit();
}

// Generate OTP
$otp    = (string) random_int(100000, 999999);
$stored = Admin::storeOtp($conn, (int)$admin['admin_id'], $otp);

if (!$stored) {
    error_log("[admin_forgot_password] Failed to store OTP for admin_id: {$admin['admin_id']}");
    header('Location: admin_forgot_password.php?error=' . urlencode(
        'Could not generate a reset code. Please try again.'
    ));
    exit();
}

// Send OTP email
$to      = $admin['email'];
$subject = 'Kyambogo Voting System — Admin Password Reset Code';
$html    = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
    .card { background: #fff; border-radius: 12px; max-width: 480px; margin: 0 auto;
            padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #003366, #004d99); border-radius: 8px;
              padding: 20px; text-align: center; color: #fff; margin-bottom: 24px; }
    .otp-box { background: #f0f4ff; border: 2px dashed #003366; border-radius: 10px;
               text-align: center; padding: 20px; margin: 20px 0; }
    .otp-code { font-size: 2.5rem; font-weight: 900; letter-spacing: 0.4rem;
                color: #003366; font-family: monospace; }
    .warning { color: #666; font-size: 0.85rem; margin-top: 16px; }
    .footer { text-align: center; color: #999; font-size: 0.8rem; margin-top: 24px; }
  </style>
</head>
<body>
  <div class='card'>
    <div class='header'><h2>🏛️ Kyambogo University Voting System</h2></div>
    <p>Hi <strong>" . htmlspecialchars($admin['username']) . "</strong>,</p>
    <p>A password reset was requested for your admin account. Use the code below:</p>
    <div class='otp-box'>
      <div class='otp-code'>" . htmlspecialchars($otp) . "</div>
    </div>
    <p>This code expires in <strong>5 minutes</strong>.</p>
    <p class='warning'>⚠️ If you did not request this, your account may be at risk. Contact your system administrator immediately.</p>
    <div class='footer'>Kyambogo University Electoral Commission</div>
  </div>
</body>
</html>
";

$sent = send_smtp_email($to, $subject, $html, $admin['username']);

if (!$sent) {
    error_log("[admin_forgot_password] Email failed for admin_id: {$admin['admin_id']}, email: {$to}");
    // Clear the OTP so it doesn't sit unused
    Admin::clearOtp($conn, (int)$admin['admin_id']);
    header('Location: admin_forgot_password.php?error=' . urlencode(
        'Failed to send reset code. Please try again or contact support.'
    ));
    exit();
}

// Store admin_id in session for the OTP verification step
$_SESSION['admin_reset_id']       = (int)$admin['admin_id'];
$_SESSION['admin_reset_attempts'] = 0;

// Log the attempt
if (function_exists('log_audit_event')) {
    log_audit_event($conn, (string)$admin['admin_id'], 'ADMIN_PASSWORD_RESET_REQUESTED',
        'Password reset OTP sent to ' . $to);
}

header("Location: $generic_redirect");
exit();
?>
