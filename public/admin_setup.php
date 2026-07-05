<?php
/**
 * Admin Setup Page — Force password change on default login
 */
require_once '../bootstrap.php';
ensure_csrf_token();

if (!isset($_SESSION['setup_admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - Kyambogo Voting</title>
    <meta name="description" content="Administrator setup for Kyambogo University Online Voting System">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--app-bg);
            padding: 20px;
            margin: 0;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            background: var(--surface);
            border-radius: var(--radius-xl, 24px);
            padding: 48px 36px 40px;
            box-shadow: var(--shadow-lg, 0 12px 40px rgba(0,0,0,0.12));
            position: relative;
            overflow: hidden;
        }

        /* Premium accent bar at top */
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--brand-primary, #003366), var(--brand-accent, #ffc107));
        }

        .logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo img {
            height: 80px;
            width: auto;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1));
        }

        .university-name {
            text-align: center;
            margin-bottom: 4px;
        }

        .university-name h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--brand-primary, #003366);
            margin: 0;
            letter-spacing: -0.3px;
        }

        h2 {
            font-size: 1.3rem;
            font-weight: 700;
            text-align: center;
            color: var(--text, #1a1d23);
            margin: 0 0 32px 0;
            letter-spacing: -0.2px;
        }

        #adminSetupForm {
            width: 100%;
        }

        /* ── Form Groups ───────────────────────────────────────────────────────── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text, #1a1d23);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 15px;
            font-size: 0.95rem;
            font-family: inherit;
            background: var(--input-bg, #fff);
            color: var(--text, #1a1d23);
            border: 1.5px solid var(--input-border, #d1d5db);
            border-radius: var(--radius, 10px);
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-group input::placeholder {
            color: var(--text-light, #9ca3af);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--brand-primary, #003366);
            background: var(--surface-2, #f8f9fb);
            box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.1);
        }

        /* ── Submit Button ─────────────────────────────────────────────────────── */
        button[type="submit"] {
            width: 100%;
            padding: 14px 16px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            background: var(--gradient-primary, linear-gradient(135deg, #003366, #004d99));
            border: none;
            border-radius: var(--radius, 10px);
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        button[type="submit"]:hover {
            opacity: 0.92;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 51, 102, 0.2);
        }

        button[type="submit"]:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
        }

        /* ── Error Messages ────────────────────────────────────────────────────── */
        #error-message,
        #error {
            display: none;
            padding: 14px 16px;
            border-radius: var(--radius, 10px);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid;
            animation: slideDown 0.3s ease;
        }

        #error {
            background: var(--brand-danger-light, #fee2e2);
            color: var(--brand-danger, #dc2626);
            border-left-color: var(--brand-danger, #dc2626);
        }

        #error-message {
            background: var(--brand-success-light, #d1fae5);
            color: var(--brand-success, #059669);
            border-left-color: var(--brand-success, #059669);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Footer Text ────────────────────────────────────────────────────────── */
        .login-container p {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted, #6b7280);
            margin-top: 20px;
            letter-spacing: 0.2px;
        }

        /* ── Responsive ────────────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .login-container {
                padding: 40px 28px 32px;
                border-radius: var(--radius-lg, 16px);
            }

            .logo img {
                height: 70px;
            }

            .university-name h1 {
                font-size: 1.2rem;
            }

            h2 {
                font-size: 1.1rem;
                margin-bottom: 24px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-group input {
                padding: 12px 13px;
                font-size: 16px;
            }

            button[type="submit"] {
                padding: 13px 14px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .login-container {
                padding: 32px 20px 24px;
                max-width: 100%;
            }

            .logo img {
                height: 60px;
            }

            .university-name h1 {
                font-size: 1.1rem;
            }

            h2 {
                font-size: 1rem;
                margin-bottom: 20px;
            }
        }

        /* ── Dark Theme Support ────────────────────────────────────────────────── */
        body.dark {
            background: var(--app-bg);
        }

        body.dark .login-container {
            background: var(--surface);
        }

        body.dark .form-group input {
            background: var(--input-bg);
            border-color: var(--input-border);
        }

        body.dark .form-group input:focus {
            background: var(--surface-2);
        }
    </style>
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>
    <!-- PWA -->
    <link rel="manifest" href="/finalyearproject/public/manifest.json">
    <meta name="theme-color" content="#1a237e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KU Votes">
    <link rel="apple-touch-icon" href="/finalyearproject/assets/images/icons/icon-180.png">" type="image/png">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <link rel="stylesheet" href="../assets/css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="Kyambogo University Logo">
        </div>
        <div class="university-name">
            <h1>KYAMBOGO UNIVERSITY</h1>
        </div>
        <h2>Admin Setup - Change Default Password</h2>
        <p style="text-align: center; color: var(--brand-danger); margin-bottom: 20px;">For security reasons, you must change the default password to continue.</p>
        
        <div id="error-message" class="error-message"></div>
        <div id="error" class="error"></div>
        
        <form id="adminSetupForm" action="submit_admin_setup.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            </div>
            <button type="submit">Update Password</button>
        </form>
    </div>
    <script src="../assets/js/theme.js" defer></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const error = params.get("error");
        if (error) {
            const errorEl = document.getElementById("error");
            errorEl.textContent = error;
            errorEl.style.display = "block";
        }
    </script>
    <script src="/finalyearproject/assets/js/pwa.js" defer></script>
</body>
</html>
