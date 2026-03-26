<?php
session_start();
include 'db_connection.php';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Password Reset - Kyambogo University</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .reset-container { max-width: 500px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .logo { text-align: center; margin-bottom: 20px; }
        .logo img { height: 80px; }
        h2 { color: #003366; text-align: center; }
        .form-group { margin-bottom: 15px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #003366; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; }
        .error { color: #dc3545; margin-bottom: 15px; text-align: center; }
        .success { color: #28a745; margin-bottom: 15px; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo">
            <img src="kyu_logo.png" alt="University Logo">
        </div>
        <h2>Password Reset</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="send_reset_email.php" method="POST">
            <div class="form-group">
                <label>University Email Address:</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>

        <div class="text-center" style="margin-top: 15px;">
            <a href="login.html">Back to Login</a>
        </div>
    </div>
</body>
</html>