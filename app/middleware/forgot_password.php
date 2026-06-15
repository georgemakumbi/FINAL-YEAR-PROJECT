<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}


require_once APP_UTILS . '/db_connection.php';


$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>PASSWORD RESET</title>
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        body { font-family: Arial, sans-serif; background-color: var(--app-bg); color: var(--text); }
        .reset-container { max-width: 500px; margin: 50px auto; padding: 20px; background: var(--surface); border-radius: 5px; box-shadow: var(--shadow); }
        .logo { text-align: center; margin-bottom: 20px; }
        .logo img { height: 80px; }
        h2 { color: var(--brand-primary); text-align: center; }
        .form-group { margin-bottom: 15px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid var(--input-border); border-radius: 4px; background: var(--input-bg); color: var(--text); }
        button { background: var(--brand-primary); color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; }
        .error { color: #dc3545; margin-bottom: 15px; text-align: center; }
        .success { color: #28a745; margin-bottom: 15px; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo">
            <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="University Logo">
        </div>
        <h2>PASSWORD RESET</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="submit_forgot_password.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label>University Email Address:</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>

        <div class="text-center" style="margin-top: 15px;">
            <a href="login.php">LOGIN</a>
        </div>
    </div>
    <script src="../views/components/includes/theme.js" defer></script>
</body>
</html>
