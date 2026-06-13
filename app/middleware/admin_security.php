<?php
/**
 * =============================================================================
 * ADMIN SECURITY MIDDLEWARE — Guards & Security Functions
 * =============================================================================
 * 
 * WHAT IS MIDDLEWARE?
 *   Middleware is code that runs BETWEEN the user's request and your page.
 *   Think of it as a security guard at the door of a building:
 *   
 *   User Request → [MIDDLEWARE: "Show your badge!"] → Page Content
 *                         ↓ No badge?
 *                   Redirect to login
 *
 * WHAT THIS FILE PROVIDES:
 *   1. require_admin_login()  → Check if user is an admin
 *   2. require_super_admin()  → Check if user is a super admin
 *   3. ensure_csrf_token()    → Generate CSRF tokens for forms
 *   4. verify_csrf_or_die()   → Validate CSRF tokens on form submission
 *
 * HOW IT'S USED:
 *   // In any admin page:
 *   require_admin_login(); // If not admin → redirected to login
 *   // Only admins see the code below this line
 *
 * =============================================================================
 */

// ─── Session Initialization ──────────────────────────────────────────────────
// Start a session if one isn't already running.
// This is a safety check — bootstrap.php usually starts the session,
// but this file might be loaded directly (without bootstrap) in some cases.
//
// session_status() returns:
//   PHP_SESSION_DISABLED → Sessions are turned off in php.ini
//   PHP_SESSION_NONE     → Sessions are enabled but not started yet
//   PHP_SESSION_ACTIVE   → A session is already running
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Only set cookie params if headers haven't been sent yet.
    // headers_sent() returns true if ANY output has been sent to the browser.
    // Once output is sent, you can't modify headers (including cookies).
    if (!headers_sent()) {
        session_set_cookie_params([
            'httponly' => true,   // JavaScript can't access the cookie
            'samesite' => 'Lax', // Protects against cross-site requests
        ]);
    }
    session_start();
}

/**
 * ─── Admin Login Check ───────────────────────────────────────────────────────
 * 
 * Checks if the current user is logged in as an admin.
 * If not, redirects them to the admin login page.
 * 
 * HOW IT WORKS:
 *   When an admin logs in (admin_authenticate.php), we store their ID:
 *     $_SESSION['admin_id'] = 5;
 *   
 *   This function checks if that session variable exists.
 *   If it doesn't → the user is NOT an admin → redirect to login.
 *
 * USAGE:
 *   // At the top of admin_dashboard.php:
 *   require_admin_login(); // ← This single line protects the entire page!
 * 
 * THE "void" RETURN TYPE:
 *   ": void" means this function doesn't return any value.
 *   It either does nothing (user is admin) or redirects (user is not admin).
 *
 * @return void
 */
function require_admin_login(): void
{
    if (!isset($_SESSION['admin_id'])) {
        // Not logged in as admin → send to login page
        header("Location: admin_login.php");
        exit(); // Stop execution — don't show the page content!
    }
}

/**
 * ─── Super Admin Check ──────────────────────────────────────────────────────
 * 
 * Checks if the current user is a SUPER ADMIN (highest privilege level).
 * 
 * ROLE HIERARCHY:
 *   super_admin → Can do everything (delete elections, manage admins)
 *   admin       → Can manage candidates, view reports, etc.
 *   student     → Can vote and view results
 *
 * This function first checks if you're an admin at all,
 * then checks if your role is 'super_admin'.
 *
 * USAGE:
 *   // Before a dangerous action:
 *   require_super_admin(); // Only super admins can delete elections
 *   $conn->query("DELETE FROM elections WHERE election_id = 5");
 *
 * @return void
 */
function require_super_admin(): void
{
    // First, check if they're an admin at all
    require_admin_login();
    
    // Then check if they're a SUPER admin
    if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
        // They're an admin, but not a super admin → deny access
        header("Location: admin_dashboard.php?error=Unauthorized action");
        exit();
    }
}

/**
 * ─── CSRF Token Generator ───────────────────────────────────────────────────
 * 
 * WHAT IS CSRF?
 *   CSRF = Cross-Site Request Forgery
 *   An attack where a malicious website submits a form to YOUR site 
 *   while the user is logged in.
 *
 * EXAMPLE ATTACK (without CSRF protection):
 *   1. Admin is logged into the voting system
 *   2. Admin visits evil-site.com (in another tab)
 *   3. Evil site has a hidden form:
 *      <form action="http://localhost/finalyearproject/public/processvote.php" method="POST">
 *          <input name="Guild President" value="99"> <!-- Attacker's candidate -->
 *      </form>
 *      <script>document.forms[0].submit();</script>
 *   4. The browser automatically includes the session cookie!
 *   5. The server thinks it's a legitimate request! ❌
 *
 * HOW CSRF TOKENS PREVENT THIS:
 *   1. Server generates a random token: "a8f2b4c6d8e0..."
 *   2. Token is stored in SESSION and embedded in the form as a hidden field
 *   3. When the form is submitted, the server compares:
 *      - Token from the form (POST data)
 *      - Token from the session (server memory)
 *   4. Evil site can't know the random token → their form is rejected! ✅
 *
 * HOW THIS FUNCTION WORKS:
 *   - If a token already exists in the session, return it
 *   - If not, generate a new one using random_bytes() (cryptographically secure)
 *   - bin2hex() converts the random bytes to a hex string: "a8f2b4c6..."
 *
 * @return string The CSRF token (64 hex characters)
 */
function ensure_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // random_bytes(32) generates 32 random bytes (256 bits of entropy)
        // bin2hex() converts to a 64-character hexadecimal string
        // This is cryptographically secure — virtually impossible to guess
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * ─── CSRF Token Validator ────────────────────────────────────────────────────
 * 
 * Verifies that the CSRF token submitted with a form matches the one
 * stored in the session. If they don't match, the request is REJECTED.
 *
 * WHERE TO USE:
 *   At the top of any file that processes form submissions:
 *   
 *   // In processvote.php:
 *   verify_csrf_or_die(); // ← Stops fake form submissions
 *
 * IMPORTANT: hash_equals() is used instead of === for comparison.
 *   Why? Regular string comparison (===) can leak information through
 *   "timing attacks" — the comparison takes longer if more characters
 *   match. An attacker could figure out the token one character at a time!
 *   hash_equals() always takes the same amount of time regardless of
 *   how many characters match. This is called "constant-time comparison".
 *
 * @return void Dies with 403 Forbidden if token is invalid
 */
function verify_csrf_or_die(): void
{
    $session_token   = $_SESSION['csrf_token'] ?? '';  // Token from server
    $submitted_token = $_POST['csrf_token'] ?? '';      // Token from form

    // Check that both tokens exist AND match
    if ($session_token === '' || $submitted_token === '' || !hash_equals($session_token, $submitted_token)) {
        // Token mismatch → this is a forged request!
        http_response_code(403); // 403 Forbidden
        die("Invalid CSRF token. This request has been blocked for security.");
    }
}
?>
