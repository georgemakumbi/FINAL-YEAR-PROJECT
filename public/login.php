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
    <link rel="icon" href="../assets/images/image.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/login.css">
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
        <button type="button" class="link-btn" onclick="showOTP()">Register here</button>
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
