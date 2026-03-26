<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    if (!headers_sent()) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    session_start();
}

function require_admin_login(): void
{
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.html");
        exit();
    }
}

function require_super_admin(): void
{
    require_admin_login();
    if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
        header("Location: adminDashboard.php?error=Unauthorized action");
        exit();
    }
}

function ensure_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_or_die(): void
{
    $session_token = $_SESSION['csrf_token'] ?? '';
    $submitted_token = $_POST['csrf_token'] ?? '';

    if ($session_token === '' || $submitted_token === '' || !hash_equals($session_token, $submitted_token)) {
        http_response_code(403);
        die("Invalid CSRF token.");
    }
}
?>
