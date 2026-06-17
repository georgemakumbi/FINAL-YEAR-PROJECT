<?php
/**
 * Processes the admin setup form to change the default password.
 */
require_once '../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_setup.php");
    exit();
}

// ─── CSRF Verification ──────────────────────────────────────────────────────
verify_csrf_or_die();

if (!isset($_SESSION['setup_admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = (int)$_SESSION['setup_admin_id'];
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($new_password) || empty($confirm_password)) {
    header("Location: admin_setup.php?error=" . urlencode("All fields are required."));
    exit();
}

if ($new_password !== $confirm_password) {
    header("Location: admin_setup.php?error=" . urlencode("Passwords do not match."));
    exit();
}

if (strlen($new_password) < 8) {
    header("Location: admin_setup.php?error=" . urlencode("Password must be at least 8 characters long."));
    exit();
}

if ($new_password === 'password') {
    header("Location: admin_setup.php?error=" . urlencode("New password cannot be the default password."));
    exit();
}

// Update the password
if (Admin::updatePassword($conn, $admin_id, $new_password)) {
    // Fetch admin details to log them in
    $admin = Admin::findById($conn, $admin_id);
    if ($admin) {
        log_audit_event(
            $conn, (string)$admin['admin_id'], 'ADMIN_SETUP_COMPLETE',
            "Admin '{$admin['username']}' completed initial setup and changed password."
        );
        
        // Log them in
        session_regenerate_id(true);
        unset($_SESSION['setup_admin_id']);
        
        $_SESSION['admin_id']       = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role']     = $admin['role'];
        
        header("Location: admin_dashboard.php");
        exit();
    }
}

header("Location: admin_setup.php?error=" . urlencode("Failed to update password. Please try again."));
exit();
