<?php
/**
 * =============================================================================
 * REGISTER — Registration Step 3 (Final Step: Set Password)
 * =============================================================================
 * Handles the final step of registration: setting the student's password.
 * 
 * SECURITY GATE: Requires $_SESSION['reg_verified_student'] which is only
 * set after the student has successfully verified their OTP in Step 2.
 * Without this session key, this endpoint is completely inaccessible.
 *
 * What this does:
 *   1. Validates the session gate
 *   2. Validates the password strength
 *   3. Calls Student::activate() to set the password and mark is_registered=TRUE
 *   4. Sends a welcome confirmation email
 *   5. Redirects to login
 *
 * OLD BEHAVIOR (REMOVED):
 *   The old register.php allowed any student to self-register by providing
 *   any name, email, and password. This was a security hole — anyone could
 *   register using another student's ID. That path is now gone.
 * =============================================================================
 */

if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

verify_csrf_or_die();

// ── 1. SECURITY GATE: Require OTP verification from Step 2 ───────────────────
if (empty($_SESSION['reg_verified_student'])) {
    header('Location: register.php?error=' . urlencode(
        'Access denied. Please complete identity verification first.'
    ));
    exit();
}

$student_id = $_SESSION['reg_verified_student'];

// ── 2. Validate password ──────────────────────────────────────────────────────
$password = $_POST['password']         ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($password === '' || $confirm === '') {
    header('Location: register.php?step=set_password&error=' . urlencode('Password is required.'));
    exit();
}

if ($password !== $confirm) {
    header('Location: register.php?step=set_password&error=' . urlencode('Passwords do not match.'));
    exit();
}

if (strlen($password) < 8) {
    header('Location: register.php?step=set_password&error=' . urlencode(
        'Password must be at least 8 characters long.'
    ));
    exit();
}

// ── 3. Activate the student account ──────────────────────────────────────────
$success = Student::activate($conn, $student_id, $password);

if (!$success) {
    // This can happen if the student somehow already got activated (race condition
    // or double-submit). Redirect to login gracefully.
    header('Location: login.php?info=' . urlencode(
        'Your account may already be active. Try logging in.'
    ));
    exit();
}

// ── 4. Send welcome email ─────────────────────────────────────────────────────
// Fetch the student's name and email for the confirmation email
$student = Student::findById($conn, $student_id);
$to        = $student['email'] ?? '';
$full_name = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
if ($full_name === '') {
    $full_name = $student_id;
}

if (!empty($to)) {
    $subject = 'Welcome to Kyambogo University Voting System 🎉';
    $html    = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
    .card { background: #fff; border-radius: 12px; max-width: 480px; margin: 0 auto; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #003366, #004d99); border-radius: 8px; padding: 20px; text-align: center; color: #fff; margin-bottom: 24px; }
    .header h2 { margin: 0; }
    .badge { display: inline-block; background: #e8f5e9; color: #2e7d32; border-radius: 20px; padding: 6px 16px; font-weight: 700; margin: 12px 0; }
    .info { background: #f0f4ff; border-radius: 8px; padding: 16px; margin: 16px 0; }
    .info p { margin: 4px 0; color: #333; }
    .footer { text-align: center; color: #999; font-size: 0.8rem; margin-top: 24px; }
  </style>
</head>
<body>
  <div class='card'>
    <div class='header'>
      <h2>🏛️ Kyambogo University Voting System</h2>
    </div>
    <p>Hi <strong>" . htmlspecialchars($full_name) . "</strong>,</p>
    <p>Your account has been successfully created and verified.</p>
    <span class='badge'>✅ Account Activated</span>
    <div class='info'>
      <p><strong>Student ID:</strong> " . htmlspecialchars($student_id) . "</p>
      <p><strong>Faculty:</strong> " . htmlspecialchars($student['faculty'] ?? 'N/A') . "</p>
      <p><strong>Department:</strong> " . htmlspecialchars($student['department'] ?? 'N/A') . "</p>
    </div>
    <p>You can now log in using your student ID and the password you just set.</p>
    <p>Cast your vote during the election period to make your voice heard!</p>
    <div class='footer'>Kyambogo University Electoral Commission</div>
  </div>
</body>
</html>
";
    send_smtp_email($to, $subject, $html, $full_name);
}

// ── 5. Clean up registration session data ────────────────────────────────────
unset(
    $_SESSION['reg_verified_student'],
    $_SESSION['reg_masked_email'],
    $_SESSION['reg_student_name'],
    $_SESSION['reg_student_faculty'],
    $_SESSION['reg_student_dept']
);

// ── 6. Log the registration event ────────────────────────────────────────────
if (function_exists('log_audit_event')) {
    log_audit_event($conn, $student_id, 'STUDENT_REGISTERED', 'Student completed OTP-verified registration');
}

header('Location: login.php?success=' . urlencode(
    'Account created successfully! You can now log in.'
));
exit();
?>
