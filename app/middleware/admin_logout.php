<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method for logout.");
}
verify_csrf_or_die();

require_once APP_UTILS . '/db_connection.php';
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';


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
header("Location: admin_login.php");
exit();
?>
