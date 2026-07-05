<?php
require_once '../bootstrap.php';
ensure_csrf_token();

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kyambogo Voting System</title>
    <link rel="icon" href="../assets/images/image.png" type="image/png">
    <!-- PWA -->
    <link rel="manifest" href="/finalyearproject/public/manifest.json">
    <meta name="theme-color" content="#1a237e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KU Votes">
    <link rel="apple-touch-icon" href="/finalyearproject/assets/images/icons/icon-180.png">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--app-bg); padding: 20px; }
        .container { width: 100%; max-width: 480px; background: var(--surface); border-radius: 16px; padding: 28px; box-shadow: var(--shadow); position: relative; overflow: hidden; }
        .container::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--brand-accent, #ffc107), var(--brand-primary, #003366)); }
        h2 { text-align: center; color: var(--brand-primary); margin: 0 0 18px; font-size: 1.6rem; }
        .error { color: #dc3545; margin-bottom: 12px; text-align: center; }
        .success { color: #28a745; margin-bottom: 12px; text-align: center; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 700; color: var(--text); font-size: 0.9rem; }
        input { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1.5px solid var(--input-border, #d1d5db); background: var(--input-bg, #fff); color: var(--text); }
        button { width: 100%; padding: 13px; border: none; border-radius: 10px; background: var(--gradient-primary, linear-gradient(135deg, #003366, #004d99)); color: #fff; font-weight: 800; cursor: pointer; margin-top: 8px; }
        .link-row { text-align: center; margin-top: 14px; text-decoration: none; }
        a { color: var(--brand-primary); text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
<div class="container">
    <h2>📝 Register</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="submit_register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div class="form-group">
            <label>Student Number</label>
            <input type="text" name="student_id" placeholder="e.g., 23/U/12345" required>
        </div>

        <div class="form-group">
            <label>University Email</label>
            <input type="email" name="email" placeholder="230000000@std.kyu.ac.ug" required>
        </div>

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" placeholder="e.g., Joy">
        </div>

        <div class="form-group">
            <label>Last Name </label>
            <input type="text" name="last_name" placeholder="e.g., Kizza">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required minlength="8">
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required minlength="8">
        </div>

        <button type="submit">Create Account</button>

        <div class="link-row">
            <a href="login.php">Back to Login</a>
        </div>
    </form>
</div>

<script src="../assets/js/theme.js" defer></script>
    <script src="/finalyearproject/assets/js/pwa.js" defer></script>
</body>
</html>

