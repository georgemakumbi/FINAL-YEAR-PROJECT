<?php
/**
 * Database Migration Script
 * Adds department and is_university_wide columns to candidates table
 * Run this file once to update the database structure
 */

include 'db_connection.php';

echo "<h1>Database Migration - Department Based Voting</h1>";

$messages = [];
$errors = [];

// 1. Add department column to candidates table
$check_dept = $conn->query("SHOW COLUMNS FROM candidates LIKE 'department'");
if ($check_dept->num_rows == 0) {
    $result = $conn->query("ALTER TABLE candidates ADD COLUMN department VARCHAR(100) DEFAULT NULL");
    if ($result) {
        $messages[] = "✓ Added 'department' column to candidates table";
    } else {
        $errors[] = "✗ Failed to add department column: " . $conn->error;
    }
} else {
    $messages[] = "✓ 'department' column already exists in candidates table";
}

// 2. Add is_university_wide column to candidates table
$check_uni = $conn->query("SHOW COLUMNS FROM candidates LIKE 'is_university_wide'");
if ($check_uni->num_rows == 0) {
    $result = $conn->query("ALTER TABLE candidates ADD COLUMN is_university_wide TINYINT(1) DEFAULT 0");
    if ($result) {
        $messages[] = "✓ Added 'is_university_wide' column to candidates table";
    } else {
        $errors[] = "✗ Failed to add is_university_wide column: " . $conn->error;
    }
} else {
    $messages[] = "✓ 'is_university_wide' column already exists in candidates table";
}

// 3. Add index for faster department-based queries
$check_index = $conn->query("SHOW INDEX FROM candidates WHERE Key_name = 'idx_candidates_department'");
if ($check_index->num_rows == 0) {
    $result = $conn->query("ALTER TABLE candidates ADD INDEX idx_candidates_department (department)");
    if ($result) {
        $messages[] = "✓ Added index on department column";
    } else {
        $errors[] = "Note: Could not add index (non-critical): " . $conn->error;
    }
} else {
    $messages[] = "✓ Index on department already exists";
}

// Display results
echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;'>";

if (!empty($messages)) {
    echo "<h3 style='color: green;'>Success Messages:</h3>";
    echo "<ul>";
    foreach ($messages as $msg) {
        echo "<li>" . htmlspecialchars($msg) . "</li>";
    }
    echo "</ul>";
}

if (!empty($errors)) {
    echo "<h3 style='color: red;'>Errors:</h3>";
    echo "<ul>";
    foreach ($errors as $err) {
        echo "<li>" . htmlspecialchars($err) . "</li>";
    }
    echo "</ul>";
}

if (empty($messages) && empty($errors)) {
    echo "<p>No changes needed - database is already up to date.</p>";
}

echo "<p><strong>Migration completed!</strong></p>";
echo "<p><a href='admin_dashboard.php'>Return to Admin Dashboard</a></p>";
echo "</div>";
?>

