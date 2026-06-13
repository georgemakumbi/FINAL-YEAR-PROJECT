<?php
/**
 * Admin Login Page — Converted from .html to .php for CSRF protection
 */
require_once '../bootstrap.php';
ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Kyambogo Voting</title>
    <meta name="description" content="Administrator login for Kyambogo University Online Voting System">
    <link rel="icon" href="../assets/images/image.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../assets/images/image.png" alt="Kyambogo University Logo">
        </div>
        <div class="university-name">
            <h1>KYAMBOGO UNIVERSITY</h1>
        </div>
        <h2>Online Voting System - Admin Access</h2>
        
        <div id="error-message" class="error-message"></div>
        
        <div id="error" class="error"></div>
        <form id="adminLoginForm" action="admin_authenticate.php" method="post">
            <!-- CSRF Token — prevents cross-site request forgery -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p style="text-align: center;">Authorized staff only</p>
    </div>
    <script src="../assets/js/theme.js" defer></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const error = params.get("error");
        if (error) {
            document.getElementById("error").textContent = error;
        }
    </script>
</body>
</html>
