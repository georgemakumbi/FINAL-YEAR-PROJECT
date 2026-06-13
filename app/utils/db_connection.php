<?php
/**
 * =============================================================================
 * DATABASE CONNECTION — Connecting PHP to MySQL
 * =============================================================================
 * 
 * WHAT THIS FILE DOES:
 *   Creates a connection between your PHP code and the MySQL database.
 *   After this file runs, you have a $conn variable that lets you
 *   run SQL queries against your database.
 *
 * HOW DATABASES WORK:
 *   Think of MySQL as a separate program running on your computer.
 *   PHP needs to "call" MySQL to ask for data or save data.
 *   This file establishes that phone line ($conn).
 *
 * ENVIRONMENT VARIABLES:
 *   Instead of hardcoding passwords in your code (dangerous!), we read
 *   them from a .env file. This way:
 *   - Your code can be shared on GitHub without exposing passwords
 *   - You can have different passwords for development vs production
 *   - Changing a password only requires editing .env, not your code
 *
 * USAGE:
 *   This file is loaded by bootstrap.php, so $conn is available everywhere.
 *   
 *   // Example: Query the database
 *   $result = $conn->query("SELECT * FROM students");
 *   while ($row = $result->fetch_assoc()) {
 *       echo $row['first_name']; // Print each student's name
 *   }
 *
 * =============================================================================
 */

use Dotenv\Dotenv;

// ─── Load Environment Variables ──────────────────────────────────────────────
// phpdotenv is a library that reads .env files and makes the values available
// in PHP via the $_ENV superglobal array.
//
// file_exists() checks if .env file is present (it might not be on Vercel)
// __DIR__ . '/../../' goes up 2 directories: utils/ → app/ → project root
if (file_exists(__DIR__ . '/../../.env')) {
    // Load Composer's autoloader (manages all installed libraries)
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    // Create a Dotenv instance pointing to the project root
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    
    // Read the .env file and load values into $_ENV
    $dotenv->load();
}

// ─── Read Database Configuration ─────────────────────────────────────────────
// The ?? operator is the "null coalescing operator" (PHP 7+):
//   $_ENV['DB_HOST'] ?? 'localhost'
//   means: "Use $_ENV['DB_HOST'] if it exists, otherwise use 'localhost'"
//
// This provides fallback values for local development:
//   - WAMP defaults: root user, no password, localhost
//   - Production values come from .env file or server environment
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$username   = $_ENV['DB_USER'] ?? 'root';
$password   = $_ENV['DB_PASS'] ?? '';
$dbname     = $_ENV['DB_NAME'] ?? 'final-year-project';
$port       = $_ENV['DB_PORT'] ?? 3306;

// ─── Create the Database Connection ──────────────────────────────────────────
// mysqli = "MySQL Improved" — PHP's built-in class for MySQL connections
//
// new mysqli() creates a connection object. Parameters:
//   $servername → Where is MySQL running? (localhost = same computer)
//   $username   → MySQL user (root = admin user, has full access)
//   $password   → MySQL password (empty string for WAMP default)
//   $dbname     → Which database to use
//   $port       → MySQL port (3306 is the default)
//
// After this line, $conn is your "phone line" to the database.
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// ─── Check if Connection Succeeded ───────────────────────────────────────────
// If MySQL is not running or credentials are wrong, connect_error will be set.
// die() stops the script immediately and shows an error message.
//
// In production, you'd want a nicer error page instead of die().
// But for development, this is fine — it tells you exactly what went wrong.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ─── Set Character Encoding ──────────────────────────────────────────────────
// utf8mb4 supports ALL Unicode characters, including emojis 🗳️
// Without this, special characters might display as "?????"
// 
// set_charset() configures the PHP-to-MySQL connection encoding
// SET NAMES configures MySQL's internal handling of text
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
?>
