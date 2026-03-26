<?php
include 'db_connection.php';
require_once 'includes/audit_logger.php';
session_start();

if (isset($_SESSION['student_id'])) {
    $student_id = (string)$_SESSION['student_id'];
    log_audit_event($conn, $student_id, 'STUDENT_LOGOUT', 'Student ' . $student_id . ' logged out');
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.html?message=You have been logged out successfully");
exit();
?>
