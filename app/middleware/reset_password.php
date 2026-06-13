<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';


if (!isset($_SESSION['verified_student'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['verified_student'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--app-bg);
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border-radius: 10px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        h2 {
            margin: 0 0 16px;
            color: var(--brand-primary);
        }

        .field {
            margin-bottom: 14px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--text);
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            box-sizing: border-box;
            background: var(--input-bg);
            color: var(--text);
        }

        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            color: #fff;
            background: var(--brand-primary);
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Create New Password</h2>
        <form action="reset_password.php" method="POST">
            <div class="field">
                <label for="new_password">New Password</label>
                <input id="new_password" type="password" name="new_password" required minlength="8">
            </div>

            <div class="field">
                <label for="confirm_password">Confirm Password</label>
                <input id="confirm_password" type="password" name="confirm_password" required minlength="8">
            </div>

            <button type="submit">Save Password</button>
        </form>
    </div>
    <script src="../views/components/includes/theme.js" defer></script>
</body>
</html>
<?php
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($new_password) || empty($confirm_password)) {
    die("All fields required.");
}

if ($new_password !== $confirm_password) {
    die("Passwords do not match.");
}

// Strong password validation
if (strlen($new_password) < 8) {
    die("Password must be at least 8 characters.");
}

// Hash password securely
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Update password
$stmt = $conn->prepare("UPDATE students SET password_hash = ? WHERE student_id = ?");
$stmt->bind_param("ss", $hashed_password, $student_id);
if (!$stmt->execute()) {
    die("Failed to update password.");
}

// Destroy session after success
unset($_SESSION['verified_student'], $_SESSION['otp_student'], $_SESSION['otp_sent']);
session_destroy();

header("Location: login.php?success=Password created successfully");
exit();
?>
