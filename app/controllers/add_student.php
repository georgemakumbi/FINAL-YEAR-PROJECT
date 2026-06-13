<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_MIDDLEWARE . '/admin_security.php';
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';
require_super_admin();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_or_die();
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $faculty = $conn->real_escape_string($_POST['faculty']);
    $department = $conn->real_escape_string($_POST['department']);
    
    $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name, email, password_hash, faculty, department) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $student_id, $first_name, $last_name, $email, $password, $faculty, $department);
    
    if ($stmt->execute()) {
        log_audit_event(
            $conn,
            isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
            'STUDENT_ADDED',
            'Student ' . $student_id . ' (' . $first_name . ' ' . $last_name . ') added'
        );
        header("Location: admin_dashboard.php?success=Student added successfully");
    } else {
        if ((int)$stmt->errno === 1062) {
            $duplicate_error = strtolower($stmt->error);
            if (strpos($duplicate_error, 'student_id') !== false) {
                header("Location: admin_dashboard.php?error=Duplicate student ID. This student is already registered.");
            } elseif (strpos($duplicate_error, 'email') !== false) {
                header("Location: admin_dashboard.php?error=Duplicate email address. This email is already registered.");
            } else {
                header("Location: admin_dashboard.php?error=Duplicate student record. Check student ID or email.");
            }
        } else {
            header("Location: admin_dashboard.php?error=Error adding student");
        }
    }
    exit();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
