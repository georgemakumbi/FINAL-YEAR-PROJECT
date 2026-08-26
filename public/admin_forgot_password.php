<?php
require_once '../bootstrap.php';
ensure_csrf_token();

$error   = htmlspecialchars($_GET['error']   ?? '');
$success = htmlspecialchars($_GET['success'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Forgot Password | Kyambogo Voting</title>
    <link rel="icon" href="<?= get_system_logo($conn, '../') ?>">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: var(--app-bg, #f0f4ff);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            padding: 24px 16px;
        }
        .card {
            width: 100%; max-width: 440px;
            background: var(--surface, #fff);
            border-radius: 20px;
            padding: 40px 36px 32px;
            box-shadow: 0 16px 48px rgba(0,51,102,0.12);
            position: relative; overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 5px;
            background: linear-gradient(90deg, #003366, #ffc107);
        }
        .icon { text-align: center; font-size: 2.8rem; margin-bottom: 10px; }
        h1 { text-align: center; font-size: 1.4rem; font-weight: 800;
             color: var(--text, #1a1d23); margin-bottom: 6px; }
        .subtitle { text-align: center; font-size: 0.85rem; color: var(--text-muted, #6b7280);
                    margin-bottom: 28px; line-height: 1.6; }
        .alert {
            padding: 12px 14px; border-radius: 10px; font-size: 0.88rem; font-weight: 500;
            margin-bottom: 20px; display: flex; gap: 8px; align-items: flex-start;
        }
        .alert-error   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.85rem; font-weight: 700;
                color: var(--text, #374151); margin-bottom: 7px; }
        input[type="text"] {
            width: 100%; padding: 13px 15px; border-radius: 10px;
            border: 1.5px solid var(--input-border, #d1d5db);
            background: var(--input-bg, #fafafa);
            color: var(--text, #111827); font-size: 0.95rem; font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s; outline: none;
        }
        input:focus {
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0,51,102,0.10);
            background: var(--surface, #fff);
        }
        .btn {
            width: 100%; padding: 13px; border: none; border-radius: 10px;
            background: linear-gradient(135deg, #003366, #004d99);
            color: #fff; font-size: 0.95rem; font-weight: 800;
            font-family: inherit; cursor: pointer; margin-top: 6px;
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,51,102,0.25); }
        .footer-link { text-align: center; margin-top: 20px; font-size: 0.84rem;
                       color: var(--text-muted, #6b7280); }
        .footer-link a { color: #003366; font-weight: 700; text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🔑</div>
    <h1>Forgot Password</h1>
    <p class="subtitle">
        Enter your admin username. We'll send a one-time reset code to your registered email address.
    </p>

    <?php if ($error): ?>
    <div class="alert alert-error">⚠️ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success">✅ <?= $success ?>
        <br><br>
        <strong><a href="admin_reset_password.php" style="color:#15803d;">Enter the code here →</a></strong>
    </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form action="submit_admin_forgot_password.php" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <label for="username">Admin Username</label>
            <input type="text" id="username" name="username"
                   placeholder="e.g., admin"
                   required autofocus autocomplete="username">
        </div>

        <button type="submit" class="btn">Send Reset Code</button>
    </form>
    <?php endif; ?>

    <div class="footer-link">
        Remember your password? <a href="admin_login.php">Back to Login</a>
    </div>
</div>
<script src="../assets/js/theme.js" defer></script>
</body>
</html>
