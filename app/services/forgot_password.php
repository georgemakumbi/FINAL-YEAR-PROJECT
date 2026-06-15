<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/resend_mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit();
}

verify_csrf_or_die();

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    header('Location: forgot_password.php?error=Email+is+required');
    exit();
}

$stmt = $conn->prepare('SELECT student_id, email FROM students WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: forgot_password.php?success=If+that+email+is+registered%2C+an+OTP+has+been+sent.');
    exit();
}

$row = $result->fetch_assoc();
$student_id = $row['student_id'];
$to = $row['email'];

$otp = random_int(100000, 999999);
$expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

$update = $conn->prepare('UPDATE students SET otp = ?, otp_expiry = ? WHERE student_id = ?');
$update->bind_param('sss', $otp, $expiry, $student_id);
$update->execute();

$subject = 'Your Voting System Password Reset OTP';
$message = "
<html>
<head>
    <title>Kyambogo University Voting System</title>
</head>
<body>
    <h3>Kyambogo University Voting System</h3>
    <p>Your password reset OTP code is:</p>
    <h2>$otp</h2>
    <p>This code expires in 5 minutes.</p>
    <p>If you did not request this, please ignore this email.</p>
</body>
</html>";

$mailSent = send_resend_email($to, $subject, $message);

if ($mailSent) {
    $_SESSION['otp_student'] = $student_id;
    $_SESSION['otp_sent'] = true;
    header('Location: login.php?success=OTP+sent+successfully.+Check+your+inbox.&show=verify');
    exit();
}

error_log("Password reset email failed for student: $student_id, email: $to");
header('Location: forgot_password.php?error=Failed+to+send+OTP.+Please+try+again+later.');
exit();

?>
