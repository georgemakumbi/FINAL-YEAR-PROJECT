# 🌐 Phase 1: How the Web Works — A Complete Backend Guide

## Welcome, George!

This guide teaches you backend fundamentals using YOUR actual voting system code.
Every concept is explained with real examples from your project.

---

## Table of Contents

1. [What Happens When You Visit a Website?](#1-what-happens-when-you-visit-a-website)
2. [What is a Server? (WAMP Explained)](#2-what-is-a-server-wamp-explained)
3. [HTTP Requests & Responses](#3-http-requests--responses)
4. [PHP: Your Server-Side Language](#4-php-your-server-side-language)
5. [Sessions & Cookies: Remembering Users](#5-sessions--cookies-remembering-users)
6. [Your Login Flow: Step by Step](#6-your-login-flow-step-by-step)
7. [Your Vote Flow: Step by Step](#7-your-vote-flow-step-by-step)
8. [Database Basics: MySQL](#8-database-basics-mysql)
9. [MVC Architecture: Why We Organize Code](#9-mvc-architecture-why-we-organize-code)
10. [Security Fundamentals](#10-security-fundamentals)

---

## 1. What Happens When You Visit a Website?

When you type `http://localhost/finalyearproject/public/index.php` in your browser, here's what happens:

```
┌──────────┐         ┌──────────────┐         ┌──────────┐
│  Browser │ ──1──►  │  WAMP Server │ ──2──►  │  MySQL   │
│ (Chrome) │         │  (Apache+PHP)│         │ Database │
│          │ ◄──4──  │              │ ◄──3──  │          │
└──────────┘         └──────────────┘         └──────────┘

Step 1: Browser sends HTTP REQUEST to WAMP server (Apache)
Step 2: Apache sees it's a .php file, hands it to PHP to execute
        PHP may need data, so it asks MySQL database
Step 3: MySQL returns the data to PHP
Step 4: PHP generates HTML and sends HTTP RESPONSE back to browser
```

### Think of it like a restaurant:
- **Browser** = The customer placing an order
- **Apache** = The waiter who takes the order
- **PHP** = The chef who cooks the food
- **MySQL** = The pantry where ingredients (data) are stored
- **HTML Response** = The finished meal served to the customer

---

## 2. What is a Server? (WAMP Explained)

**WAMP** stands for:
- **W**indows — Your operating system
- **A**pache — The web server software (listens for HTTP requests)
- **M**ySQL — The database management system
- **P**HP — The programming language

**Where is your project?**
```
c:\wamp64\www\finalyearproject\   ← Apache serves files from www/
```

Apache is configured to serve files from `c:\wamp64\www\`. When you visit
`http://localhost/finalyearproject/public/index.php`, Apache:
1. Looks in `c:\wamp64\www\finalyearproject\public\` for `index.php`
2. Passes it to PHP for processing
3. Returns the result to your browser

---

## 3. HTTP Requests & Responses

### What is HTTP?
HTTP (HyperText Transfer Protocol) is the "language" browsers and servers speak.
Every interaction between browser and server is a Request → Response cycle.

### HTTP Methods (The Two You Use Most)

#### GET Request — "I want to SEE something"
When you click a link or type a URL:
```
Browser → Server: "GET /finalyearproject/public/voting.php"
                   "Please give me the voting page"
```

**In your project:**
```php
// In voting.php, line 79-82:
<?php if (isset($_GET['error']) && $_GET['error'] !== ''): ?>
    <div class="instructions" style="background-color:#fdecea;">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>
```
Here, `$_GET['error']` reads the error message from the URL:
`voting.php?error=Please login first`
              ↑
              This is called a "query parameter"

#### POST Request — "I want to SEND data"
When you submit a form:
```
Browser → Server: "POST /finalyearproject/public/authenticate.php"
                   Body: { student_id: "23/U/001", password: "mypass123" }
```

**In your project (login.html, line 17):**
```html
<form id="loginSection" action="authenticate.php" method="POST">
    <input type="text" name="student_id" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
```

The `method="POST"` tells the browser: "Send this data in the body, not the URL."
The `action="authenticate.php"` tells the browser: "Send it to this file."

**Why POST instead of GET for login?**
- GET puts data IN THE URL: `authenticate.php?password=mypass123` ← VISIBLE! DANGEROUS!
- POST puts data IN THE BODY: hidden from the URL bar

### HTTP Status Codes
The server responds with a status code:
| Code | Meaning | Example in your project |
|------|---------|------------------------|
| 200 | OK, here's the page | Normal page load |
| 302 | Redirect to another URL | `header("Location: results.php")` |
| 403 | Forbidden | CSRF token invalid: `http_response_code(403)` |
| 404 | Not Found | Trying to access a file that doesn't exist |
| 500 | Server Error | PHP crashes (syntax error, database down) |

---

## 4. PHP: Your Server-Side Language

### PHP runs on the SERVER, not the browser!

```php
<?php
// Everything between <?php and ?> runs on the SERVER.
// The browser NEVER sees PHP code — only the HTML output.

echo "Hello World"; // This sends "Hello World" to the browser
?>
```

### Key PHP Concepts Used in Your Project:

#### A) Variables and Superglobals
```php
// Regular variable
$student_id = "23/U/001";

// SUPERGLOBALS — special arrays PHP fills automatically:
$_GET['error']       // Data from URL query parameters
$_POST['student_id'] // Data from form submissions (POST method)
$_SESSION['student_id'] // Data stored in server-side session
$_SERVER['REQUEST_METHOD'] // "GET" or "POST"
$_SERVER['REMOTE_ADDR']    // User's IP address
$_ENV['DB_HOST']           // Environment variables from .env file
```

#### B) Include/Require — Reusing Code
```php
// In your bootstrap.php:
require_once APP_UTILS . '/db_connection.php';
// This INSERTS the contents of db_connection.php right here
// Like copy-pasting the file content at this point

// require_once = include file, but only once (prevents duplicates)
// require = include file (fatal error if missing)
// include = include file (warning if missing, continues execution)
```

#### C) Define Constants
```php
// In your bootstrap.php:
define('PROJECT_ROOT', __DIR__);  // __DIR__ = current file's directory
define('APP_PATH', PROJECT_ROOT . '/app');

// Now APP_PATH = "c:\wamp64\www\finalyearproject\app"
// You can use it everywhere instead of typing the full path
```

#### D) Header Redirects
```php
// This tells the browser: "Go to this URL instead"
header("Location: results.php");
exit(); // IMPORTANT: Always exit() after redirect!
        // Otherwise PHP keeps executing the rest of the code
```

---

## 5. Sessions & Cookies: Remembering Users

### The Problem:
HTTP is **stateless** — the server forgets you between requests.
When you load `voting.php`, the server has NO IDEA you just logged in on `authenticate.php`.

### The Solution: Sessions

```
┌──────────┐                     ┌──────────┐
│  Browser │  ── Request 1 ──►   │  Server  │
│          │  Login form POST     │          │
│          │                      │ Creates  │
│          │  ◄── Response ──    │ Session  │
│          │  Set-Cookie:         │ File:    │
│          │  PHPSESSID=abc123    │ abc123   │
│          │                      │          │
│ Stores   │  ── Request 2 ──►   │          │
│ cookie   │  Cookie: abc123      │ Looks up │
│ abc123   │  voting.php          │ abc123   │
│          │                      │ "Oh it's │
│          │  ◄── Response ──    │  George!"│
└──────────┘                     └──────────┘
```

**In your bootstrap.php:**
```php
// This creates or resumes a session
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,   // JavaScript can't read the session cookie (security!)
        'samesite' => 'Lax', // Protects against CSRF attacks
    ]);
    session_start(); // PHP creates a session file on the server
                     // and sends a PHPSESSID cookie to the browser
}
```

**In your authenticate.php — STORING data in session:**
```php
// After successful login, store user data in session
$_SESSION['student_id'] = $student['student_id'];   // "23/U/001"
$_SESSION['first_name'] = $student['first_name'];    // "George"
$_SESSION['faculty'] = $student['faculty'];           // "Engineering"
$_SESSION['has_voted'] = $student['has_voted'];       // 0 or 1

// This data is saved on the SERVER in a file like:
// c:\wamp64\tmp\sess_abc123def456...
```

**In your voting.php — READING data from session:**
```php
// Check if user is logged in by checking if session has student_id
if (!isset($_SESSION['student_id'])) {
    // No student_id in session = not logged in = redirect to login
    header("Location: login.html?error=Please login first");
    exit();
}

// If we get here, the user IS logged in
echo "Welcome, " . $_SESSION['first_name']; // "Welcome, George"
```

**In your logout.php — DESTROYING the session:**
```php
session_destroy(); // Deletes the session file from server
// Now $_SESSION is empty — user is "forgotten"
header("Location: login.html");
exit();
```

---

## 6. Your Login Flow: Step by Step

Let's trace exactly what happens when a student logs in:

### Step 1: Student Opens Login Page
```
Browser: GET /finalyearproject/public/login.html
Server:  Returns the HTML file (no PHP processing needed for .html)
```

### Step 2: Student Fills Form and Clicks "Login"
```html
<!-- login.html sends this form: -->
<form action="authenticate.php" method="POST">
    <input name="student_id" value="23/U/001">  <!-- User typed this -->
    <input name="password" value="mypass123">    <!-- User typed this -->
</form>
```
```
Browser: POST /finalyearproject/public/authenticate.php
         Body: student_id=23/U/001&password=mypass123
```

### Step 3: PHP Processes the Login (authenticate.php)
```php
// 1. Load the framework
require_once dirname(__DIR__, 2) . '/bootstrap.php';
// This loads: db_connection.php (creates $conn to MySQL)
//             admin_security.php (session functions)
//             audit_logger.php (logging functions)

// 2. Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 3. Get the form data
    $student_id = $conn->real_escape_string($_POST['student_id']); // "23/U/001"
    $password = $_POST['password']; // "mypass123"

    // 4. Query the database using a PREPARED STATEMENT (safe from SQL injection!)
    $stmt = $conn->prepare(
        "SELECT student_id, first_name, last_name, email, faculty,
                department, password_hash, has_voted
         FROM students WHERE student_id = ?"
    );
    //                                    ↑
    //    The "?" is a placeholder — PHP fills it in safely
    
    $stmt->bind_param("s", $student_id);  // "s" = string type
    $stmt->execute();                      // Run the query
    $result = $stmt->get_result();         // Get the results

    // 5. Check if student exists
    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc(); // Get student data as array

        // 6. Verify password
        // password_verify() checks if "mypass123" matches the stored hash
        // Hash example: "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/..."
        if (password_verify($password, $student['password_hash'])) {

            // 7. Regenerate session ID (prevents session fixation attacks)
            session_regenerate_id(true);

            // 8. Store user data in session
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['first_name'] = $student['first_name'];
            // ... more session data ...

            // 9. Log the event for audit trail
            log_audit_event($conn, $student['student_id'], 'STUDENT_LOGIN', '...');

            // 10. Redirect based on voting status
            if ($student['has_voted']) {
                header("Location: results.php");  // Already voted → show results
            } else {
                header("Location: voting.php");    // Haven't voted → go vote
            }
            exit();
        }
    }
}
```

### Step 4: Browser Follows Redirect
```
Server:  302 Redirect to voting.php
Browser: GET /finalyearproject/public/voting.php
         Cookie: PHPSESSID=abc123 (automatically sent)
```

### Step 5: Voting Page Loads with Session Data
```php
// voting.php checks the session
if (!isset($_SESSION['student_id'])) { /* redirect to login */ }
// Session exists! Show the voting page with "Welcome, George"
```

---

## 7. Your Vote Flow: Step by Step

### Step 1: Student Selects Candidates and Clicks "Submit Votes"
```html
<!-- voting.php form: -->
<form action="processvote.php" method="POST">
    <!-- Hidden CSRF token for security -->
    <input type="hidden" name="csrf_token" value="a1b2c3d4...">
    
    <!-- Radio buttons for each position -->
    <input type="radio" name="Guild President" value="5">  <!-- Candidate ID 5 -->
    <input type="radio" name="Guild Secretary" value="12"> <!-- Candidate ID 12 -->
</form>
```

### Step 2: processvote.php Handles the Vote
```php
// 1. Verify user is logged in
if (!isset($_SESSION['student_id'])) { /* redirect */ }

// 2. Only accept POST requests (not GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { /* redirect */ }

// 3. Verify CSRF token (prevents cross-site attacks)
verify_csrf_or_die();

// 4. Check election hasn't ended
// Queries database for active election end date

// 5. BEGIN TRANSACTION — This is crucial!
$conn->begin_transaction();

// A TRANSACTION means: "Do ALL of these steps, or NONE of them"
// If step 3 fails, steps 1 and 2 are UNDONE (rolled back)

try {
    // 6. Lock the student row (prevents race conditions)
    // "FOR UPDATE" means no one else can modify this row until we're done
    $check_voted = $conn->prepare(
        "SELECT has_voted FROM students WHERE student_id = ? FOR UPDATE"
    );
    
    // 7. Verify student hasn't already voted
    if ($has_voted) {
        $conn->rollback(); // Undo everything
        header("Location: results.php");
        exit();
    }

    // 8. Process each vote
    foreach ($_POST as $position => $candidate_id) {
        // Validate the candidate exists and matches the position
        // Insert vote into votes table
        // Increment candidate's vote count
    }

    // 9. Mark student as "has_voted"
    $conn->prepare("UPDATE students SET has_voted = TRUE WHERE student_id = ?");

    // 10. COMMIT — Make all changes permanent
    $conn->commit();
    
    // 11. Log the vote event
    log_audit_event($conn, $student_id, 'VOTE_CAST', '...');
    
    // 12. Redirect to results
    header("Location: results.php");
    
} catch (Throwable $e) {
    // Something went wrong — UNDO everything
    $conn->rollback();
    header("Location: voting.php?error=...");
}
```

### Why Transactions Are Critical:
Imagine this without a transaction:
1. ✅ Insert vote for Guild President
2. ✅ Update candidate vote count  
3. ❌ Insert vote for Secretary FAILS (network error!)
4. 😱 Student has voted for President but not Secretary!
5. 😱 Student is marked as "has_voted" — can never vote again!

With a transaction:
1. Insert vote for President (pending...)
2. Update vote count (pending...)
3. Insert vote for Secretary FAILS
4. ✅ ROLLBACK — Everything is undone, as if nothing happened
5. ✅ Student can try again

---

## 8. Database Basics: MySQL

### Your Database Tables (How They Relate):

```
┌─────────────────┐       ┌──────────────────┐       ┌──────────────┐
│    students     │       │     votes        │       │  candidates  │
│─────────────────│       │──────────────────│       │──────────────│
│ student_id (PK) │←──┐   │ vote_id (PK)     │   ┌──→│ candidate_id │
│ first_name      │   ├───│ student_id (FK)   │   │   │ student_id   │
│ last_name       │   │   │ candidate_id (FK)─│───┘   │ first_name   │
│ email           │   │   │ position          │       │ last_name    │
│ password_hash   │   │   │ vote_date         │       │ position     │
│ faculty         │   │   └──────────────────┘       │ votes        │
│ department      │   │                               │ status       │
│ has_voted       │   │   ┌──────────────────┐       └──────────────┘
└─────────────────┘   │   │   elections      │
                      │   │──────────────────│       ┌──────────────┐
                      │   │ election_id (PK) │       │    admin     │
                      │   │ election_title   │       │──────────────│
                      │   │ position         │       │ admin_id (PK)│
                      │   │ start_date       │       │ username     │
                      │   │ end_date         │       │ password_hash│
                      │   │ status           │       │ role         │
                      │   └──────────────────┘       └──────────────┘
                      │
                      │   ┌──────────────────┐       ┌──────────────┐
                      │   │   feedback       │       │  audit_log   │
                      └───│ student_id (FK)  │       │──────────────│
                          │ feedback         │       │ user_id      │
                          │ feedback_date    │       │ action       │
                          └──────────────────┘       │ details      │
                                                     │ ip_address   │
                                                     └──────────────┘
```

### Key Database Concepts:

#### Primary Key (PK)
A unique identifier for each row. Like your student ID — no two students have the same one.
```sql
student_id VARCHAR(20) PRIMARY KEY  -- "23/U/001" is unique
```

#### Foreign Key (FK)
A reference to another table's primary key. Creates a RELATIONSHIP.
```sql
FOREIGN KEY (student_id) REFERENCES students(student_id)
-- This means: votes.student_id MUST exist in students table
-- You can't vote if you're not a registered student
```

#### Prepared Statements (Preventing SQL Injection)
```php
// ❌ DANGEROUS — SQL Injection vulnerable:
$query = "SELECT * FROM students WHERE student_id = '$student_id'";
// If someone enters: ' OR 1=1 --
// Query becomes: SELECT * FROM students WHERE student_id = '' OR 1=1 --'
// This returns ALL students! The attacker is now "logged in"!

// ✅ SAFE — Prepared Statement (what your project uses):
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
// The "?" placeholder is replaced safely — special characters are escaped
// The attacker's input is treated as literal text, not SQL code
```

---

## 9. MVC Architecture: Why We Organize Code

### What is MVC?
MVC stands for **Model-View-Controller**. It's a way to organize code:

```
┌─────────────┐      ┌──────────────┐      ┌────────────┐
│   MODEL     │      │  CONTROLLER  │      │    VIEW    │
│ (Database)  │      │ (Logic)      │      │ (HTML/UI)  │
│             │      │              │      │            │
│ "WHERE is   │◄────►│ "WHAT to do" │◄────►│ "HOW it    │
│  the data?" │      │              │      │  looks"    │
└─────────────┘      └──────────────┘      └────────────┘
```

### Your Project's MVC Structure:
```
finalyearproject/
├── app/
│   ├── utils/db_connection.php     ← MODEL (database connection)
│   ├── controllers/                ← CONTROLLER (business logic)
│   │   ├── add_candidate.php       (adds a candidate)
│   │   ├── add_student.php         (adds a student)
│   │   └── ...
│   ├── middleware/                  ← MIDDLEWARE (runs before controllers)
│   │   ├── authenticate.php        (handles login)
│   │   └── admin_security.php      (checks admin permissions)
│   └── services/                   ← SERVICES (reusable operations)
│       ├── processvote.php         (processes voting)
│       └── send_notifications.php  (sends emails)
├── views/                          ← VIEW (HTML templates)
│   ├── admin/admin_dashboard.php
│   └── components/includes/        (reusable UI parts)
├── public/                         ← ENTRY POINTS (what users access)
│   ├── index.php                   (homepage)
│   ├── voting.php                  (voting page)
│   └── login.html                  (login page)
└── assets/                         ← STATIC FILES
    ├── css/                        (stylesheets)
    ├── js/                         (JavaScript)
    └── images/                     (images)
```

### Why This Matters:
- **Separation of Concerns**: Each file does ONE thing well
- **Reusability**: `audit_logger.php` is used by many files
- **Maintainability**: To change the login UI, you only edit `login.html`
- **Security**: Only `public/` folder is web-accessible

---

## 10. Security Fundamentals

Your project already implements several security measures. Let's understand each:

### A) Password Hashing (bcrypt)
```php
// NEVER store passwords as plain text!
// Your project uses bcrypt hashing:

// When a student registers:
$password_hash = password_hash("mypass123", PASSWORD_DEFAULT);
// Result: "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"
// This is a ONE-WAY hash — you CANNOT reverse it to get "mypass123"

// When a student logs in:
password_verify("mypass123", $stored_hash); // Returns true or false
// PHP internally hashes "mypass123" and compares it to the stored hash
```

### B) CSRF Protection (Cross-Site Request Forgery)
```php
// In admin_security.php:
function ensure_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        // Generate a random 64-character hex string
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// In voting.php form:
<input type="hidden" name="csrf_token" 
       value="<?php echo $_SESSION['csrf_token']; ?>">

// In processvote.php:
verify_csrf_or_die(); // Checks that the form token matches session token
```

**Why CSRF protection?**
Without it, a malicious website could submit a vote on your behalf:
```html
<!-- Evil website: evil.com -->
<form action="http://localhost/finalyearproject/public/processvote.php" method="POST">
    <input name="Guild President" value="99"> <!-- Attacker's candidate! -->
</form>
<script>document.forms[0].submit();</script>
<!-- If you're logged in, this would cast a vote WITHOUT you knowing! -->
```

The CSRF token prevents this because evil.com doesn't know your random token.

### C) Session Security
```php
// In your bootstrap.php:
session_set_cookie_params([
    'httponly' => true,   // JavaScript can't read the cookie
                          // Prevents XSS attacks from stealing sessions
    'samesite' => 'Lax', // Cookie only sent from same site
                          // Prevents CSRF attacks
]);

// After login:
session_regenerate_id(true); // Creates a new session ID
                              // Prevents "session fixation" attacks
```

### D) SQL Injection Prevention (Prepared Statements)
Already covered in Section 8. Your project uses this correctly in most places!

### E) XSS Prevention (Cross-Site Scripting)
```php
// In voting.php:
echo htmlspecialchars($candidate['first_name']);
// htmlspecialchars() converts special characters to HTML entities:
// < becomes &lt;
// > becomes &gt;
// " becomes &quot;
// This prevents someone from injecting JavaScript:
// Without: <script>alert('hacked')</script>  ← RUNS as JavaScript!
// With:    &lt;script&gt;alert('hacked')&lt;/script&gt;  ← Displayed as text
```

---

## 🎯 Summary: What You've Learned

| Concept | What It Is | Where It Is in Your Project |
|---------|------------|----------------------------|
| HTTP GET | Request to see a page | Visiting `index.php` |
| HTTP POST | Request to send data | Submitting login form |
| PHP Superglobals | Special arrays PHP fills | `$_POST`, `$_SESSION`, `$_GET` |
| Sessions | Server-side user memory | `bootstrap.php`, `authenticate.php` |
| Prepared Statements | Safe database queries | `authenticate.php` line 22 |
| Password Hashing | Secure password storage | `password_verify()` in login |
| CSRF Tokens | Prevent forged requests | `admin_security.php` |
| Transactions | All-or-nothing DB operations | `processvote.php` |
| MVC Architecture | Code organization pattern | `app/`, `views/`, `public/` |
| Redirects | Sending user to another page | `header("Location: ...")` |

---

## ✏️ Quick Quiz (Test Yourself!)

1. Why do we use `POST` instead of `GET` for the login form?
2. What does `session_regenerate_id(true)` do and why is it important?
3. What would happen if `processvote.php` didn't use a database transaction?
4. Why is `htmlspecialchars()` important when displaying user data?
5. What does the `?` placeholder do in a prepared statement?

> **Answers:** Try to answer these yourself first! If you're stuck, re-read the relevant
> section above. Understanding these concepts is CRUCIAL for your final year defense.

---

## Next Steps

Now that you understand how the backend works, let's **improve the foundation** of your project:
1. ✅ Create a professional README.md
2. ✅ Clean up the database schema
3. ✅ Add proper .htaccess security
4. ✅ Fix HTML structure issues
