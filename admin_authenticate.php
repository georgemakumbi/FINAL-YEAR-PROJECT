<?php
include 'db_connection.php';
require_once 'includes/audit_logger.php';
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT admin_id, username, password_hash, role FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            log_audit_event(
                $conn,
                (string)$admin['admin_id'],
                'ADMIN_LOGIN',
                'Admin ' . $admin['username'] . ' logged in'
            );
            header("Location: adminDashboard.php");
            exit();
        }
    }
    
    header("Location: admin_login.html?error=Invalid credentials");
    exit();
} else {
    header("Location: admin_login.html");
    exit();
}
?>
