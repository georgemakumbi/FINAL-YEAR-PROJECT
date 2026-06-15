<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_UTILS . '/resend_mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/register.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');

// Validate email format: 230000000@std.kyu.ac.ug
if (!preg_match('/^\d{9}@std\.kyu\.ac\.ug$/', $email)) {
    header('Location: ../../public/register.php?error=Email+must+be+in+the+format+230000000%40std.kyu.ac.ug');
    exit();
}

if (empty($password) || empty($confirm)) {
    header('Location: register.php?error=Password+is+required');
    exit();
}

if ($password !== $confirm) {
    header('Location: register.php?error=Passwords+do+not+match');
    exit();
}

if (strlen($password) < 8) {
    header('Location: register.php?error=Password+must+be+at+least+8+characters');
    exit();
}

// Derive student_id from email's local part
$student_id_prefix = explode('@', $email, 2)[0];

// NOTE: Schema in this project uses student_id as stored value.
// We expect student_id matches the 9-digit number; if not, adjust this mapping.
$stmt = $conn->prepare('SELECT student_id, email, first_name, last_name FROM students WHERE email = ? OR student_id = ?');
$stmt->bind_param('ss', $email, $student_id_prefix);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Do not leak exact existence
    header('Location: register.php?error=Invalid+email+or+student+record+not+found');
    exit();
}

$student = $result->fetch_assoc();
$student_id = $student['student_id'];

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Update student password hash (and optional names)
$update = $conn->prepare('UPDATE students SET password_hash = ?, first_name = COALESCE(NULLIF(?, ""), first_name), last_name = COALESCE(NULLIF(?, ""), last_name) WHERE student_id = ?');
$update->bind_param('ssss', $hashed_password, $first_name, $last_name, $student_id);
$ok = $update->execute();

if (!$ok) {
    header('Location: register.php?error=Failed+to+create+account');
    exit();
}

// Send email confirmation
$to = $student['email'];
$subject = 'Kyambogo University Voting System - Registration Successful';

// Try to use updated name if user provided; otherwise fallback.
$full_name = trim(($first_name ?: ($student['first_name'] ?? '')) . ' ' . ($last_name ?: ($student['last_name'] ?? '')));
if ($full_name === '') {
    $full_name = $to;
}

$html = "
<html>
<body>
    <h3>Kyambogo University Voting System</h3>
    <p>Hi <strong>" . htmlspecialchars($full_name) . "</strong>,</p>
    <p>Your account has been registered successfully.</p>
    <p>You can now log in to the system using your student ID and the password you set.</p>
</body>
</html>";

send_resend_email($to, $subject, $html, $full_name);

// Auto-login not implemented; redirect to login.
header('Location: login.php?success=Account+created+successfully');
exit();
?>

