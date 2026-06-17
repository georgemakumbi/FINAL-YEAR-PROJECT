<?php
/**
 * =============================================================================
 * STUDENT LOGIN PAGE — Now with CSRF Protection! (Converted from .html to .php)
 * =============================================================================
 * 
 * WHY DID WE CONVERT FROM .html TO .php?
 *   login.html was a plain HTML file — it couldn't run PHP code.
 *   But we NEED PHP to:
 *     1. Generate CSRF tokens (security)
 *     2. Start/resume sessions
 *     3. Generate dynamic content
 *   
 *   By renaming to .php, Apache passes it through the PHP engine,
 *   allowing us to mix PHP and HTML.
 *
 * WHAT CHANGED:
 *   - File extension: .html → .php
 *   - Added: <?php bootstrap + CSRF token generation at the top
 *   - Added: Hidden CSRF token field in ALL three forms
 *   - All links pointing to login.html now need to point to login.php
 *
 * =============================================================================
 */
require_once '../bootstrap.php';
ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System Login — Kyambogo University</title>
    <meta name="description" content="Student login portal for Kyambogo University Online Voting System">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--app-bg);
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border-radius: var(--radius-xl, 24px);
            padding: 40px 32px 32px;
            box-shadow: var(--shadow-lg, 0 12px 40px rgba(0,0,0,0.12));
            position: relative;
            overflow: hidden;
        }

        /* Gold accent bar at top */
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-accent, #ffc107), var(--brand-primary, #003366));
        }

        .container h2 {
            font-size: 1.6rem;
            font-weight: 800;
            text-align: center;
            color: var(--text, #1a1d23);
            margin-bottom: 24px;
            letter-spacing: -0.5px;
        }

        /* ── Form Groups ───────────────────────────────────────────────────────── */
        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text, #1a1d23);
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            background: var(--input-bg, #fff);
            color: var(--text, #1a1d23);
            border: 1.5px solid var(--input-border, #d1d5db);
            border-radius: var(--radius, 10px);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--brand-primary, #003366);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }

        /* ── Submit Button ─────────────────────────────────────────────────────── */
        button[type="submit"],
        .container form button {
            width: 100%;
            padding: 13px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            background: var(--gradient-primary, linear-gradient(135deg, #003366, #004d99));
            border: none;
            border-radius: var(--radius, 10px);
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 8px;
        }

        button[type="submit"]:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: var(--shadow, 0 4px 12px rgba(0,0,0,0.08));
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        /* ── Link Buttons ──────────────────────────────────────────────────────── */
        .link-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--brand-primary, #003366);
            background: transparent;
            border: 1.5px solid var(--border, #e5e7eb);
            border-radius: var(--radius, 10px);
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .link-btn:hover {
            color: #00010c;
            background: var(--brand-primary-light, #e8f0fe);
            border-color: var(--brand-primary, #003366);
        }

        /* ── Messages ──────────────────────────────────────────────────────────── */
        .message {
            padding: 12px 16px;
            border-radius: var(--radius, 10px);
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            margin-bottom: 16px;
            animation: fadeSlide 0.3s ease;
        }

        .message.error {
            background: var(--brand-danger-light, #fee2e2);
            color: var(--brand-danger, #dc2626);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .message.success {
            background: var(--brand-success-light, #d1fae5);
            color: var(--brand-success, #059669);
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Hidden ────────────────────────────────────────────────────────────── */
        .hidden {
            display: none !important;
        }

        /* ── Responsive ────────────────────────────────────────────────────────── */
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px 24px;
                border-radius: var(--radius-lg, 16px);
            }
            .container h2 { font-size: 1.3rem; }
        }
    </style>
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <link rel="stylesheet" href="../assets/css/login.css">
    <script>
        window.va = window.va || function () { (window.vaq = window.vaq || []).push(arguments); };
    </script>
    <script defer src="/_vercel/insights/script.js"></script>
</head>

<body>
<div class="container">
    <h2>🗳️ KYAMBOGO DECIDES</h2>
    <div id="messageBox" class="hidden"></div>

    <!-- LOGIN FORM -->
    <form id="loginSection" action="authenticate.php" method="POST">
        <!-- CSRF token — prevents cross-site request forgery -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        
        <div class="form-group">
            <label>Student ID</label>
            <input type="text" name="student_id" id="student_id" placeholder="e.g., 23/U/001" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">Login</button>
        <p style="text-align: center; padding-top: 20px;"><a href="forgot_password.php" style="color: var(--primary);"><i>Forgot password?</i></a></p>
<button type="button" class="link-btn" onclick="window.location.href='register.php'">Register here</button>
    </form>

    <!-- OTP REQUEST FORM -->
    <form id="otpRequestSection" class="hidden" action="send_otp.php" method="POST">
        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        
        <div class="form-group">
            <label>Enter Student ID</label>
            <input type="text" name="student_id" placeholder="e.g., 23/U/001" required>
        </div>

        <button type="submit">Send OTP</button>
        <button type="button" class="link-btn" onclick="showLogin()">Back to Login</button>
    </form>

    <!-- OTP VERIFY FORM -->
    <form id="otpVerifySection" class="hidden" action="verify_otp.php" method="POST">
        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        
        <div class="form-group">
            <label>Enter OTP</label>
            <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" placeholder="6-digit code" required>
        </div>

        <button type="submit">Verify OTP</button>
        <button type="button" class="link-btn" onclick="showOTP()">Request New OTP</button>
    </form>
</div>

<script src="../assets/js/theme.js" defer></script>
<script>
    const params = new URLSearchParams(window.location.search);
    const show = params.get("show");
    const error = params.get("error");
    const success = params.get("success");
    const messageBox = document.getElementById("messageBox");

    if (error) {
        messageBox.className = "message error";
        messageBox.textContent = error;
        messageBox.classList.remove("hidden");
    } else if (success) {
        messageBox.className = "message success";
        messageBox.textContent = success;
        messageBox.classList.remove("hidden");
    }

    function showOTP() {
        document.getElementById("loginSection").classList.add("hidden");
        document.getElementById("otpVerifySection").classList.add("hidden");
        document.getElementById("otpRequestSection").classList.remove("hidden");
    }

    function showLogin() {
        document.getElementById("otpRequestSection").classList.add("hidden");
        document.getElementById("otpVerifySection").classList.add("hidden");
        document.getElementById("loginSection").classList.remove("hidden");
    }

    function showVerify() {
        document.getElementById("loginSection").classList.add("hidden");
        document.getElementById("otpRequestSection").classList.add("hidden");
        document.getElementById("otpVerifySection").classList.remove("hidden");
    }

    if (show === "otp") {
        showOTP();
    } else if (show === "verify") {
        showVerify();
    }
</script>

</body>
</html>
