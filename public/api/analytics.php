<?php
/**
 * =============================================================================
 * ANALYTICS API — Returns Admin Dashboard Statistics as JSON
 * =============================================================================
 * 
 * Provides real-time analytics for the admin dashboard:
 *   - Voter turnout
 *   - Votes per position
 *   - Voting timeline (votes per hour)
 *   - Candidate statistics
 *
 * SECURITY:
 *   - Requires admin login
 *   - Returns 401 if not authenticated
 */

require_once '../../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

// Admin Authentication Required
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Admin authentication required']);
    exit();
}

// Build Analytics Response
$response = [
    'success'   => true,
    'timestamp' => date('Y-m-d H:i:s'),
    
    // Overview statistics
    'overview' => [
        'total_students'    => Student::countAll($conn),
        'total_voted'       => Student::countVoted($conn),
        'voter_turnout'     => Student::getVoterTurnout($conn),
        'total_votes_cast'  => Vote::countAll($conn),
        'total_candidates'  => Candidate::countByStatus($conn, 'verified'),
        'pending_candidates'=> Candidate::countByStatus($conn, 'pending'),
    ],
    
    // Votes broken down by position
    'votes_by_position' => Vote::getStatsByPosition($conn),
    
    // Voting activity over time (for chart)
    'voting_timeline' => Vote::getTimeline($conn),
    
    // Election status counts
    'elections' => Election::countByStatus($conn),
    
    // Results (if any exist)
    'results' => Candidate::getResults($conn),
];

echo json_encode($response, JSON_PRETTY_PRINT);
