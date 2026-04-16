<?php
/**
 * =============================================================================
 * CANDIDATES MODULE - LOGIC
 * =============================================================================
 * Handles all database operations and business logic for candidate management.
 * 
 * Responsibilities:
 * - Fetch candidates from database
 * - Prepare candidate data for display
 * - Calculate candidate statistics
 * 
 * Dependencies:
 * - $conn (database connection)
 * 
 * =============================================================================
 */

// Initialize candidates variables
$candidates_message = '';
$candidates_message_type = '';

// =============================================================================
// PROCESS ACTIONS
// =============================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_action']) && $is_super_admin) {
    verify_csrf_or_die();
    
    $action = $_POST['candidate_action'];
    $candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;
    
    if ($candidate_id > 0) {
        if ($action === 'verify_candidate') {
            $stmt = $conn->prepare("UPDATE candidates SET status = 'verified' WHERE candidate_id = ?");
            $stmt->bind_param("i", $candidate_id);
            if ($stmt->execute()) {
                $candidates_message = "Candidate verified successfully.";
                $candidates_message_type = "success";
            }
            $stmt->close();
        } elseif ($action === 'reject_candidate') {
            $stmt = $conn->prepare("UPDATE candidates SET status = 'rejected' WHERE candidate_id = ?");
            $stmt->bind_param("i", $candidate_id);
            if ($stmt->execute()) {
                $candidates_message = "Candidate rejected successfully.";
                $candidates_message_type = "success";
            }
            $stmt->close();
        }
    }
}

// =============================================================================
// FETCH DATA - CANDIDATES
// =============================================================================

// Fetch all candidates ordered by vote count (highest first)
$candidates = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");

// Total number of candidates
$total_candidates = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];

// Total votes for all candidates
$total_candidates_votes = $conn->query("SELECT SUM(votes) as total FROM candidates")->fetch_assoc()['total'] ?: 0;

// Get positions with candidates
$positions_with_candidates = $conn->query("SELECT DISTINCT position FROM candidates ORDER BY position ASC");

?>
