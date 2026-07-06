<?php
/**
 * =============================================================================
 * LOOKUP STUDENT — Registration Step 1
 * =============================================================================
 * Accepts a student ID, looks it up against the pre-loaded university records,
 * and initiates the OTP verification flow if found.
 *
 * Flow:
 *   POST student_id → lookup in DB
 *     ✓ Found, not registered → generate OTP, email it, go to Step 2
 *     ✓ Found, already registered → redirect to login
 *     ✗ Not found → error: "Record not found, contact Electoral Commission"
 *
 * Security:
 *   - CSRF-protected
 *   - OTP sent ONLY to the verified email on file — no custom email input
 *   - Student ID stored in session for subsequent steps
 * =============================================================================
 */

if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

verify_csrf_or_die();

// ── 1. Validate input ─────────────────────────────────────────────────────────
$student_id = trim($_POST['student_id'] ?? '');

if (empty($student_id)) {
    header('Location: register.php?error=' . urlencode('Please enter your student number.'));
    exit();
}

// ── 2. Look up student in the pre-loaded university records ───────────────────
$student = Student::findByIdForRegistration($conn, $student_id);

if ($student === null) {
    // Student not in the system at all
    header('Location: register.php?error=' . urlencode(
        'Your student record was not found in the system. ' .
        'Please contact the Electoral Commission to be added.'
    ));
    exit();
}

// ── 3. Check if already registered ───────────────────────────────────────────
if ($student['is_registered']) {
    header('Location: login.php?info=' . urlencode(
        'You already have an account. Please log in.'
    ));
    exit();
}

// ── 4. Generate a secure 6-digit OTP ─────────────────────────────────────────
$otp    = (string) random_int(100000, 999999);
$expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

// Store OTP in DB
$update = $conn->prepare(
    'UPDATE students SET otp = ?, otp_expiry = ? WHERE student_id = ?'
);
$update->bind_param('sss', $otp, $expiry, $student_id);
$update->execute();
$update->close();

// ── 5. Send OTP email to the verified university address on file ──────────────
$to        = $student['email'];
$full_name = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
if ($full_name === '') {
    $full_name = $student_id;
}

$subject = 'Kyambogo University Voting System — Registration OTP';
$message = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
    .card { background: #fff; border-radius: 12px; max-width: 480px; margin: 0 auto; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #003366, #004d99); border-radius: 8px; padding: 20px; text-align: center; color: #fff; margin-bottom: 24px; }
    .header h2 { margin: 0; font-size: 1.2rem; }
    .otp-box { background: #f0f4ff; border: 2px dashed #003366; border-radius: 10px; text-align: center; padding: 20px; margin: 20px 0; }
    .otp-code { font-size: 2.5rem; font-weight: 900; letter-spacing: 0.4rem; color: #003366; font-family: monospace; }
    .warning { color: #666; font-size: 0.85rem; margin-top: 16px; }
    .footer { text-align: center; color: #999; font-size: 0.8rem; margin-top: 24px; }
  </style>
</head>
<body>
  <div class='card'>
    <div class='header'>
      <h2>🏛️ Kyambogo University Voting System</h2>
    </div>
    <p>Hi <strong>" . htmlspecialchars($full_name) . "</strong>,</p>
    <p>You are registering your account on the Kyambogo University Online Voting System. Use the code below to verify your identity:</p>
    <div class='otp-box'>
      <div class='otp-code'>" . htmlspecialchars($otp) . "</div>
    </div>
    <p>This code expires in <strong>10 minutes</strong>.</p>
    <p class='warning'>⚠️ If you did not attempt to register, please ignore this email. No action is needed — your account is safe.</p>
    <div class='footer'>Kyambogo University Electoral Commission</div>
  </div>
</body>
</html>
";

$mailSent = send_smtp_email($to, $subject, $message, $full_name);

if (!$mailSent) {
    error_log("[lookup_student] Failed to send registration OTP to: {$to} for student: {$student_id}");
    header('Location: register.php?error=' . urlencode(
        'Failed to send verification code. Please try again or contact support.'
    ));
    exit();
}

// ── 6. Store student ID in session for subsequent steps ──────────────────────
$_SESSION['reg_otp_student']    = $student_id;
$_SESSION['reg_otp_sent']       = true;
$_SESSION['reg_otp_attempts']   = 0;

// Pass the masked email so the UI can show it (e.g. "2*******0@std.kyu.ac.ug")
$_SESSION['reg_masked_email']   = Student::maskEmail($to);

// Cache student info so Step 2/3 can display it without extra DB calls
$_SESSION['reg_student_name']   = $full_name;
$_SESSION['reg_student_faculty'] = $student['faculty'];
$_SESSION['reg_student_dept']   = $student['department'];

header('Location: register.php?step=verify');
exit();
?>
