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
 * - Update deadline
 * - Update results status
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deadline'])) {
    verify_csrf_or_die();
    
    $new_deadline = trim($_POST['deadline']);
    if (file_put_contents("deadline.txt", $new_deadline) !== false) {
        $settings_message = "Deadline updated successfully.";
        $settings_message_type = 'success';
        log_audit_event(
            $conn,
            isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
            'DEADLINE_UPDATED',
            'Voting deadline set to ' . $new_deadline
        );
    } else {
        $settings_message = "Failed to update deadline.";
        $settings_message_type = 'error';
    }
}

// Current deadline
$deadline_file_content = @file_get_contents("deadline.txt");
$current_deadline = $deadline_file_content ? $deadline_file_content : '';

// Results Publishing
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['results_publish_action'])) {
    verify_csrf_or_die();

    if (!$is_super_admin) {
        $settings_message = "Only super admins can publish or unpublish results.";
        $settings_message_type = 'error';
    } else {
        $action = $_POST['results_publish_action'];
        $new_status = ($action === 'publish') ? 'published' : 'unpublished';

        if (set_results_publish_status($new_status)) {
            $results_status = $new_status;
            $results_published = $results_status === 'published';
            $settings_message = $results_published ? "Results published successfully." : "Results unpublished successfully.";
            $settings_message_type = 'success';

            log_audit_event(
                $conn,
                isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                $results_published ? 'RESULTS_PUBLISHED' : 'RESULTS_UNPUBLISHED',
                $results_published ? 'Election results published' : 'Election results unpublished'
            );
        } else {
            $settings_message = "Failed to update results publishing status.";
            $settings_message_type = 'error';
        }
    }
}

// Logo Upload Handling
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['upload_logo_action'])) {
    verify_csrf_or_die();

    if (!$is_super_admin) {
        $settings_message = "Only super admins can change system logo.";
        $settings_message_type = 'error';
    } else {
        $logo_error = null;
        $logo_path = upload_system_logo($_FILES['system_logo'] ?? null, $logo_error);

        if ($logo_path) {
            // Update or Insert in settings table
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('system_logo', ?) 
                                    ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP");
            $stmt->bind_param("ss", $logo_path, $logo_path);
            
            if ($stmt->execute()) {
                $settings_message = "System logo updated successfully.";
                $settings_message_type = 'success';
                log_audit_event(
                    $conn,
                    isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                    'SYSTEM_LOGO_UPDATED',
                    'System logo updated to: ' . $logo_path
                );
            } else {
                $settings_message = "Failed to save logo to database.";
                $settings_message_type = 'error';
            }
            $stmt->close();
        } else {
            $settings_message = $logo_error;
            $settings_message_type = 'error';
        }
    }
}

