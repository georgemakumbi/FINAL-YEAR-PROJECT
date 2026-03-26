<?php
/**
 * =============================================================================
 * STUDENTS MODULE - LOGIC
 * =============================================================================
 * Handles all database operations and business logic for student management.
 * 
 * Responsibilities:
 * - Fetch students from database
 * - Handle student search
 * - Prepare data for students view
 * - Statistics calculations
 * 
 * Dependencies:
 * - $conn (database connection)
 * - $is_super_admin (permission check)
 * 
 * =============================================================================
 */

// Initialize students variables
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$search_results = null;
$students_message = '';
$students_message_type = '';

// =============================================================================
// SEARCH FUNCTIONALITY - STUDENTS
// =============================================================================

if ($search_term) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ?");
    $search_param = "%$search_term%";
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $search_results = $stmt->get_result();
}

// =============================================================================
// FETCH DATA - STUDENTS
// =============================================================================

// Total registered students
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];

// Students who have voted
$students_voted = $conn->query("SELECT COUNT(*) as count FROM students WHERE has_voted = 1")->fetch_assoc()['count'];

// Students who haven't voted
$students_not_voted = $total_students - $students_voted;

// Get all students or search results (limited to 100)
if ($search_term && $search_results) {
    $students = $search_results;
} else {
    $students = $conn->query("SELECT * FROM students ORDER BY registration_date DESC LIMIT 100");
}

?>
