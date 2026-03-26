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
