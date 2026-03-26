<?php
/**
 * =============================================================================
 * ELECTIONS MODULE - LOGIC
 * =============================================================================
 * Handles all database operations and business logic for elections management.
 * 
 * Responsibilities:
 * - Fetch elections from database
 * - Handle election creation form submission
 * - Handle election status updates
 * - Prepare data for elections view
 * 
 * Dependencies:
 * - $conn (database connection)
 * - $is_super_admin (permission check)
 * - verify_csrf_or_die() (security)
 * - log_audit_event() (audit logging)
 * 
 * =============================================================================
 */

// Initialize elections variables
$elections_message = '';
$elections_message_type = '';

// =============================================================================
// FORM PROCESSING - ELECTIONS
// =============================================================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_election'])) {
    verify_csrf_or_die();
    
    $election_title = $conn->real_escape_string($_POST['election_title']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $position = $conn->real_escape_string($_POST['position']);

    $stmt = $conn->prepare("INSERT INTO elections (election_title, start_date, end_date, position, status) VALUES (?, ?, ?, ?, 'scheduled')");
    $stmt->bind_param("ssss", $election_title, $start_date, $end_date, $position);

    if ($stmt->execute()) {
        $elections_message = "Election created successfully!";
        $elections_message_type = 'success';
        log_audit_event(
            $conn,
            isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
            'ELECTION_CREATED',
            'Election "' . $election_title . '" created for position "' . $position . '"'
        );
    } else {
        $elections_message = "Error creating election.";
        $elections_message_type = 'error';
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_election']) && $is_super_admin) {
    verify_csrf_or_die();
    
    $election_id = (int)($_POST['election_id'] ?? 0);
    $new_status = $_POST['status'] ?? '';
    
    if ($election_id > 0 && in_array($new_status, ['scheduled', 'active', 'closed'], true)) {
        $stmt = $conn->prepare("UPDATE elections SET status = ? WHERE election_id = ?");
        $stmt->bind_param("si", $new_status, $election_id);
        $stmt->execute();
        $stmt->close();
        
        if ($conn->affected_rows > 0) {
            $elections_message = "Election status updated.";
            $elections_message_type = 'success';
            log_audit_event(
                $conn,
                isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                'ELECTION_STATUS_UPDATED',
                'Election ID ' . $election_id . ' status changed to ' . $new_status
            );
        }
    }
}

// =============================================================================
// FETCH DATA - ELECTIONS
// =============================================================================

// Fetch all elections ordered by start date (newest first)
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date DESC");

// Get count of active elections
$active_elections_count = $conn->query("SELECT COUNT(*) as count FROM elections WHERE status = 'active'")->fetch_assoc()['count'];

// Get count of scheduled elections
$scheduled_elections_count = $conn->query("SELECT COUNT(*) as count FROM elections WHERE status = 'scheduled'")->fetch_assoc()['count'];

// Get count of closed elections
$closed_elections_count = $conn->query("SELECT COUNT(*) as count FROM elections WHERE status = 'closed'")->fetch_assoc()['count'];

?>
