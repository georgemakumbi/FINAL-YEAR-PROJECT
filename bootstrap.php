<?php
/**
 * =============================================================================
 * BOOTSTRAP — Application Entry Point
 * =============================================================================
 * 
 * WHAT IS A BOOTSTRAP FILE?
 *   This is the FIRST file loaded by every page in your application.
 *   It sets up everything your app needs to work:
 *   
 *   1. Defines PATH CONSTANTS → So files can find each other
 *   2. Loads CORE UTILITIES   → Database connection, email, etc.
 *   3. Loads COMPONENTS       → Reusable functions (audit logger, etc.)
 *   4. Loads MIDDLEWARE        → Security functions (CSRF, admin checks)
 *   5. Starts SESSIONS        → So the server remembers logged-in users
 * 
 * WHY DO WE NEED THIS?
 *   Without bootstrap.php, every PHP file would need to:
 *     require_once '../../app/utils/db_connection.php';
 *     require_once '../../app/middleware/admin_security.php';
 *     require_once '../../views/components/includes/audit_logger.php';
 *     // ... repeat for every single file!
 *   
 *   With bootstrap.php, every file just does:
 *     require_once '../bootstrap.php';
 *   
 *   This is the DRY principle: Don't Repeat Yourself!
 *
 * HOW IT'S USED:
 *   <!-- In public/voting.php: -->
 *   <?php require_once '../bootstrap.php'; ?>
 *   
 *   <!-- In app/controllers/add_candidate.php: -->
 *   <?php require_once dirname(__DIR__, 2) . '/bootstrap.php'; ?>
 *   <!-- dirname(__DIR__, 2) means "go up 2 directories from current file" -->
 *
 * =============================================================================
 */

// ─── STEP 1: Define the Project Root Path ────────────────────────────────────
// __DIR__ is a PHP "magic constant" that equals this file's directory.
// Since bootstrap.php is in the project root, __DIR__ = project root path.
//
// Example: __DIR__ = "c:\wamp64\www\finalyearproject"
//
// The if (!defined(...)) check prevents errors if bootstrap.php is 
// accidentally loaded twice. define() would crash on duplicate definition.
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', __DIR__);
}

// ─── STEP 2: Define All Directory Paths ──────────────────────────────────────
// Constants are like variables that NEVER change. We use UPPER_CASE by convention.
// The "." operator concatenates (joins) strings in PHP.
//
// After these definitions:
//   APP_PATH      = "c:\wamp64\www\finalyearproject\app"
//   VIEWS_PATH    = "c:\wamp64\www\finalyearproject\views"
//   ASSETS_PATH   = "c:\wamp64\www\finalyearproject\assets"
//   etc.
define('APP_PATH', PROJECT_ROOT . '/app');
define('VIEWS_PATH', PROJECT_ROOT . '/views');
define('ASSETS_PATH', PROJECT_ROOT . '/assets');
define('STORAGE_PATH', PROJECT_ROOT . '/storage');
define('DATABASE_PATH', PROJECT_ROOT . '/database');

// Sub-directory paths for quick access
define('APP_CONFIG', APP_PATH . '/config');
define('APP_CONTROLLERS', APP_PATH . '/controllers');
define('APP_MIDDLEWARE', APP_PATH . '/middleware');
define('APP_SERVICES', APP_PATH . '/services');
define('APP_UTILS', APP_PATH . '/utils');
define('VIEWS_ADMIN', VIEWS_PATH . '/admin');
define('VIEWS_STUDENT', VIEWS_PATH . '/student');
define('VIEWS_COMPONENTS', VIEWS_PATH . '/components');
define('ASSETS_CSS', ASSETS_PATH . '/css');
define('ASSETS_JS', ASSETS_PATH . '/js');
define('ASSETS_IMAGES', ASSETS_PATH . '/images');

// ─── STEP 3: Load Core Utilities ─────────────────────────────────────────────
// require_once = "Load this file, but only once"
// These files provide essential functionality used by EVERYTHING else.
//
// db_connection.php → Creates the $conn variable (MySQL connection)
// smtp_mailer.php   → Provides email sending functions
require_once APP_UTILS . '/db_connection.php';
require_once APP_UTILS . '/smtp_mailer.php';

// ─── STEP 3.5: Load Model Classes ────────────────────────────────────────────
// Models are PHP classes that handle ALL database operations for each table.
// Instead of writing SQL queries scattered across many files, each Model
// provides clean, reusable methods:
//
//   Student::findById($conn, '23/U/001')     → Find a student
//   Candidate::getResults($conn)              → Get election results
//   Election::isVotingOpen($conn)             → Check if voting is active
//   Vote::cast($conn, $id, $candidateId, $pos) → Record a vote
//   Admin::authenticate($conn, $user, $pass)  → Admin login
//
// See docs/PHASE2_DATABASE_AND_MODELS.md for the full explanation.
define('APP_MODELS', APP_PATH . '/models');
require_once APP_MODELS . '/Student.php';
require_once APP_MODELS . '/Candidate.php';
require_once APP_MODELS . '/Vote.php';
require_once APP_MODELS . '/Election.php';
require_once APP_MODELS . '/Admin.php';

// ─── STEP 4: Load Reusable Components ────────────────────────────────────────
// These provide helper functions used across the application.
//
// audit_logger.php    → log_audit_event() — records actions for audit trail
// results_publish.php → results_are_published() — checks if results are visible
// common.php          → render_status_badge(), safe_output(), format_datetime(), etc.
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';
require_once VIEWS_COMPONENTS . '/includes/results_publish.php';
require_once VIEWS_COMPONENTS . '/includes/modules/common.php';

// ─── STEP 5: Load Security Middleware ────────────────────────────────────────
// Middleware = code that runs BETWEEN the request and your page logic.
// Like a security guard checking IDs before letting someone into a building.
//
// admin_security.php → CSRF tokens, admin login checks, role verification
// rate_limiter.php   → Brute force protection (blocks after 5 failed logins)
// input_validator.php → Input sanitization and validation helpers
require_once APP_MIDDLEWARE . '/admin_security.php';
require_once APP_MIDDLEWARE . '/rate_limiter.php';
require_once APP_MIDDLEWARE . '/input_validator.php';

// ─── STEP 6: Start Session (Remembering Users) ──────────────────────────────
// HTTP is stateless — the server forgets you between page loads.
// Sessions solve this by storing data on the server tied to a cookie.
//
// session_status() checks:
//   PHP_SESSION_NONE    = No session exists yet → we need to start one
//   PHP_SESSION_ACTIVE  = Session already running → don't start again
//
// session_set_cookie_params() configures the session cookie:
//   'httponly' => true   → JavaScript CANNOT read the cookie (prevents XSS theft)
//   'samesite' => 'Lax' → Cookie only sent on same-site requests (prevents CSRF)
//
// After session_start(), you can use $_SESSION['key'] to store/retrieve data.
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ─── STEP 7: Set Default Timezone ────────────────────────────────────────────
// All date/time functions will use Kampala time (UTC+3).
// Without this, PHP might use the server's timezone, which could be wrong.
date_default_timezone_set('Africa/Kampala');

?>
