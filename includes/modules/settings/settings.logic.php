<?php
/**
 * =============================================================================
 * SETTINGS MODULE - LOGIC
 * =============================================================================
 * Handles settings actions for super admins.
 *
 * Responsibilities:
 * - Reset voting status
 * - Full election reset
 *
 * Dependencies:
 * - $conn (database connection)
 * - $is_super_admin (permission check)
 * - verify_csrf_or_die() (security)
 * - log_audit_event() (audit logging)
 * 
 * =============================================================================
 */

$settings_message = '';
$settings_message_type = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_voting_action'])) {
    verify_csrf_or_die();

    if (!$is_super_admin) {
        $settings_message = "Only super admins can reset voting status.";
        $settings_message_type = 'error';
    } else {
        $action = $_POST['reset_voting_action'];

        if ($action === 'reset_has_voted') {
            if ($conn->query("UPDATE students SET has_voted = 0") === true) {
                $settings_message = "All students' voting status has been reset.";
                $settings_message_type = 'success';
                log_audit_event(
                    $conn,
                    isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                    'VOTING_STATUS_RESET',
                    'Reset students.has_voted for all students'
                );
            } else {
                $settings_message = "Failed to reset voting status.";
                $settings_message_type = 'error';
            }
        } elseif ($action === 'full_reset') {
            $conn->begin_transaction();
            $votes_deleted = $conn->query("DELETE FROM votes");
            $candidates_reset = $conn->query("UPDATE candidates SET votes = 0");
            $students_reset = $conn->query("UPDATE students SET has_voted = 0");

            if ($votes_deleted && $candidates_reset && $students_reset) {
                $conn->commit();
                $settings_message = "Full election reset complete (votes cleared, candidate totals reset, and voting status reset).";
                $settings_message_type = 'success';
                log_audit_event(
                    $conn,
                    isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                    'ELECTION_RESET',
                    'Cleared votes, reset candidate totals, and reset students.has_voted'
                );
            } else {
                $conn->rollback();
                $settings_message = "Failed to perform a full election reset.";
                $settings_message_type = 'error';
            }
        } else {
            $settings_message = "Invalid reset action.";
            $settings_message_type = 'error';
        }
    }
}

