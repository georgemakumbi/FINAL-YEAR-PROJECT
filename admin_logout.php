<?php
include 'db_connection.php';
require_once 'includes/audit_logger.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    $admin_id = (string)$_SESSION['admin_id'];
    $admin_username = $_SESSION['admin_username'] ?? $admin_id;
    log_audit_event($conn, $admin_id, 'ADMIN_LOGOUT', 'Admin ' . $admin_username . ' logged out');
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: admin_login.html");
exit();
?>
