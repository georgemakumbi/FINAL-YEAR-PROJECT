<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';


// 1. Validate input
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

verify_csrf_or_die();

$student_id = trim($_POST['student_id'] ?? '');

if (empty($student_id)) {
    die("Invalid request.");
}

// 2. Check if student exists
$stmt = $conn->prepare("SELECT email FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Do NOT reveal student doesn't exist
    header("Location: login.php?success=If+registered%2C+OTP+has+been+sent.&show=verify");
    exit();
}

$row = $result->fetch_assoc();
$db_email = $row['email'];

// 2b. Always use the registered database email for security
// Custom email input is not allowed to prevent OTP being sent to unauthorized emails
$email = $db_email;

// 3. Generate secure OTP
$otp = random_int(100000, 999999);

// 4. Set expiry time (5 minutes)
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// 5. Store OTP in database
$update = $conn->prepare("UPDATE students SET otp = ?, otp_expiry = ? WHERE student_id = ?");
$update->bind_param("sss", $otp, $expiry, $student_id);
$update->execute();

// 6. Send OTP via Email using Resend
$to = $email;
$subject = 'Your Voting System OTP Code';
$message = "
<html>
<head>
    <title>Kyambogo University Voting System</title>
</head>
<body>
    <h3>Kyambogo University Voting System</h3>
    <p>Your OTP code is:</p>
    <h2>$otp</h2>
    <p>This code expires in 5 minutes.</p>
    <p>If you did not request this, please ignore this email.</p>
</body>
</html>
";

require_once APP_UTILS . '/resend_mailer.php';

// Send email using Resend
$mailSent = send_resend_email($to, $subject, $message);

if ($mailSent) {
    // 7. Store student ID in session for verification step
    $_SESSION['otp_student'] = $student_id;
    $_SESSION['otp_sent'] = true;

    // 8. Redirect back to login and reveal OTP verification section
    header("Location: login.php?success=OTP+sent+successfully.+Check+inbox.&show=verify");
    
    exit();
} else {
    // Log error internally
    error_log("Email sending failed for student: $student_id, email: $email");
    
    // Redirect with error message
    header("Location: login.php?error=Failed+to+send+OTP.+Please+try+again+later.&show=otp");
    exit();
}
?>

