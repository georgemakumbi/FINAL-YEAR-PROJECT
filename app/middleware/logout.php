<?php
/**
 * =============================================================================
 * STUDENT LOGOUT — Ending a User's Session
 * =============================================================================
 * 
 * WHAT THIS FILE DOES:
 *   When a student clicks "Logout", this file:
 *   1. Logs the event to the audit trail (for accountability)
 *   2. Destroys the session (server forgets the user)
 *   3. Redirects to the login page
 *
 * HOW SESSION DESTRUCTION WORKS:
 *   Remember: a session is just a file on the server (e.g., sess_abc123)
 *   that stores data like $_SESSION['student_id'] = "23/U/001".
 *   
 *   When we "destroy" the session:
 *   1. $_SESSION = array()  → Empties all data from the session variable
 *   2. session_destroy()    → Deletes the session file from the server
 *   3. The PHPSESSID cookie in the browser becomes useless
 *      (it points to a session file that no longer exists)
 *
 * CALLED FROM:
 *   The logout button in voting.php and results.php:
 *   <form action="logout.php" method="POST">
 *       <button type="submit" class="logout-btn">Logout</button>
 *   </form>
 *
 *   NOTE: We use a POST form instead of a simple link (<a href="logout.php">)
 *   because logout is an ACTION that changes state. Links should be for
 *   navigation only. This is a REST principle.
 *
 * =============================================================================
 */

// Load the bootstrap (which starts the session for us)
// We need the session to be active so we can read the student_id before destroying it
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method for logout.");
}
verify_csrf_or_die();

// ─── Step 1: Log the Logout Event ────────────────────────────────────────────
// Before destroying the session, we need to read WHO is logging out.
// After session_destroy(), we can't access $_SESSION anymore!
if (isset($_SESSION['student_id'])) {
    $student_id = (string)$_SESSION['student_id'];
    log_audit_event(
        $conn,
        $student_id,
        'STUDENT_LOGOUT',
        'Student ' . $student_id . ' logged out'
    );
}

// ─── Step 2: Clear All Session Data ──────────────────────────────────────────
// Setting $_SESSION to an empty array removes all stored data:
//   Before: $_SESSION = ['student_id' => '23/U/001', 'first_name' => 'George', ...]
//   After:  $_SESSION = []
$_SESSION = array();

// ─── Step 3: Delete the Session Cookie (Optional but Thorough) ───────────────
// The session cookie (PHPSESSID) is still in the browser.
// We can tell the browser to delete it by setting its expiry to the past.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),           // Cookie name (usually "PHPSESSID")
        '',                       // Empty value
        time() - 42000,           // Expiry in the past = browser deletes it
        $params["path"],          // Same path as the original cookie
        $params["domain"],        // Same domain
        $params["secure"],        // Same secure flag
        $params["httponly"]       // Same httponly flag
    );
}

// ─── Step 4: Destroy the Server-Side Session File ────────────────────────────
// This deletes the sess_abc123... file from the server's temp directory.
// Even if someone has the old PHPSESSID cookie, it's now useless.
session_destroy();

// ─── Step 5: Redirect to Login Page ──────────────────────────────────────────
// Send the user back to the login page with a success message.
// The "success" parameter is read by JavaScript in login.php to show a green message.
header("Location: login.php?success=You have been logged out successfully");
exit(); // ALWAYS exit after a redirect to prevent further code execution
?>
