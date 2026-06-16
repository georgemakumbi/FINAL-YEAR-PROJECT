<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/resend_mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

verify_csrf_or_die();

$student_id = trim($_POST['student_id'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');

if ($student_id === '') {
    header('Location: register.php?error=Student+number+is+required');
    exit();
}

if (!preg_match('/^\d{7,15}@std\.kyu\.ac\.ug$/', $email)) {
    header('Location: register.php?error=Email+must+be+in+the+format+230000000%40std.kyu.ac.ug');
    exit();
}

if ($password === '' || $confirm === '') {
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

$stmt = $conn->prepare('SELECT student_id, email, first_name, last_name FROM students WHERE email = ? OR student_id = ?');
$stmt->bind_param('ss', $email, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

if ($result->num_rows === 0) {
    $stmt = $conn->prepare('INSERT INTO students (student_id, email, first_name, last_name, password_hash) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $student_id, $email, $first_name, $last_name, $hashed_password);
    if (!$stmt->execute()) {
        $err = $conn->errno;
        if ((int)$err === 1062) {
            header('Location: register.php?error=Student+number+or+email+already+registered');
            exit();
        }
        header('Location: register.php?error=Failed+to+create+account');
        exit();
    }
    $student_first_name = $first_name;
    $student_last_name = $last_name;
    $to = $email;
} else {
    $student = $result->fetch_assoc();
    $student_id = $student['student_id'];
    $update = $conn->prepare(
        'UPDATE students SET password_hash = ?, first_name = COALESCE(NULLIF(?, ""), first_name), last_name = COALESCE(NULLIF(?, ""), last_name) WHERE student_id = ?'
    );
    $update->bind_param('ssss', $hashed_password, $first_name, $last_name, $student_id);
    if (!$update->execute()) {
        header('Location: register.php?error=Failed+to+create+account');
        exit();
    }
    $to = $student['email'];
    $student_first_name = $student['first_name'];
    $student_last_name = $student['last_name'];
}
$subject = 'Kyambogo University Voting System - Registration Successful';
$full_name = trim(($student_first_name ?? $first_name) . ' ' . ($student_last_name ?? $last_name));
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

$mailSent = send_resend_email($to, $subject, $html, $full_name);

if ($mailSent) {
    header('Location: login.php?success=Account+created+successfully');
    exit();
}

error_log("Registration confirmation email failed for student: $student_id, email: $to");
header('Location: login.php?success=Account+created.+Confirmation+email+could+not+be+sent.');
exit();

?>
