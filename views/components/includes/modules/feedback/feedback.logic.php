<?php
/**
 * =============================================================================
 * FEEDBACK MODULE - LOGIC
 * =============================================================================
 * Handles all database operations and business logic for feedback management.
 * 
 * Responsibilities:
 * - Check if feedback table exists
 * - Fetch feedback entries from database
 * - Prepare data for feedback view
 * - Statistics calculations
 * 
 * Dependencies:
 * - $conn (database connection)
 * 
 * =============================================================================
 */

// Initialize feedback variables
$feedback_entries = null;
$feedback_table_exists = false;
$total_feedback = 0;

// =============================================================================
// FETCH DATA - FEEDBACK
// =============================================================================

// Check if feedback table exists before querying
$feedback_table_exists = $conn->query("SHOW TABLES LIKE 'feedback'")->num_rows > 0;

if ($feedback_table_exists) {
    // Fetch all feedback entries with student names
    $feedback_entries = $conn->query("SELECT f.student_id, f.feedback, f.feedback_date, s.first_name, s.last_name
        FROM feedback f
        LEFT JOIN students s ON f.student_id = s.student_id
        ORDER BY f.feedback_date DESC LIMIT 100");
    
    // Get total feedback count
    $total_feedback = $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'];
}

?>
