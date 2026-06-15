<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';


// 1. Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php?show=verify");
    exit();
}

verify_csrf_or_die();

if (!isset($_SESSION['otp_student'])) {
    header("Location: login.php?error=OTP+session+expired.+Request+a+new+OTP.&show=otp");
    exit();
}

$student_id = $_SESSION['otp_student'];
$entered_otp = trim($_POST['otp']);

if (empty($entered_otp)) {
    header("Location: login.php?error=Please+enter+the+OTP.&show=verify");
    exit();
}

// Fetch OTP data
$stmt = $conn->prepare("SELECT otp, otp_expiry FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: login.php?error=Invalid+session.+Request+a+new+OTP.&show=otp");
    exit();
}

$row = $result->fetch_assoc();
$db_otp = $row['otp'];
$db_expiry = $row['otp_expiry'];

// Check OTP match
if ($entered_otp !== $db_otp) {
    header("Location: login.php?error=Incorrect+OTP.&show=verify");
    exit();
}

// Check expiry
if (strtotime($db_expiry) < time()) {
    header("Location: login.php?error=OTP+expired.+Request+a+new+one.&show=otp");
    exit();
}

// OTP valid → allow password reset
$_SESSION['verified_student'] = $student_id;

// Clear OTP immediately
$clear = $conn->prepare("UPDATE students SET otp = NULL, otp_expiry = NULL WHERE student_id = ?");
$clear->bind_param("s", $student_id);
$clear->execute();

header("Location: reset_password.php");
exit();
?>
