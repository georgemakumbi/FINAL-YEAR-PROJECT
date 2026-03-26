<?php
include 'db_connection.php';
session_start();
require 'admin_security.php';
require_once 'includes/audit_logger.php';

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html?error=Please login first");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: voting.php?error=Invalid request");
    exit();
}

verify_csrf_or_die();

// Check if election deadline has passed (using database election end date)
date_default_timezone_set('africa/kampala');
$election_check = $conn->query("SELECT end_date FROM elections WHERE status = 'active' ORDER BY end_date DESC LIMIT 1");
if ($election_check && $election_row = $election_check->fetch_assoc()) {
    $election_end = strtotime($election_row['end_date']);
    if (time() > $election_end) {
        header("Location: voting.php?error=The election has ended. Voting is no longer allowed.");
        exit();
    }
}

$student_id = $_SESSION['student_id'];
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn->begin_transaction();

    // Lock student vote status to prevent race conditions and double-voting.
    $check_voted = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ? FOR UPDATE");
    $check_voted->bind_param("s", $student_id);
    $check_voted->execute();
    $check_voted->bind_result($has_voted);
    $student_found = $check_voted->fetch();
    $check_voted->close();

    if (!$student_found) {
        throw new Exception("Student record not found.");
    }

    if ($has_voted) {
        $conn->rollback();
        header("Location: results.php");
        exit();
    }

    $submitted_votes = [];
    foreach ($_POST as $position => $candidate_id) {
        if ($position === 'submit' || $position === 'csrf_token') {
            continue;
        }
        $submitted_votes[$position] = $candidate_id;
    }

    if (empty($submitted_votes)) {
        throw new Exception("No votes submitted.");
    }

    $validate_candidate = $conn->prepare("SELECT position, department, is_university_wide FROM candidates WHERE candidate_id = ?");
    $insert_vote = $conn->prepare("INSERT INTO votes (student_id, candidate_id, position) VALUES (?, ?, ?)");
    $update_candidate = $conn->prepare("UPDATE candidates SET votes = votes + 1 WHERE candidate_id = ?");
    $seen_positions = [];

    // Get voter's department from session
    $voter_department = isset($_SESSION['department']) ? $_SESSION['department'] : '';

    foreach ($submitted_votes as $position_from_form => $candidate_id_raw) {
        $position_from_form = (string)$position_from_form;
        // Convert underscores to spaces (PHP automatically converts spaces to underscores in POST data)
        $position_from_form = str_replace('_', ' ', $position_from_form);
        $position_key = strtolower(trim($position_from_form));
        $candidate_id = filter_var($candidate_id_raw, FILTER_VALIDATE_INT);

        if ($position_key === '' || $candidate_id === false || $candidate_id <= 0) {
            throw new Exception("Invalid vote payload.");
        }
        if (isset($seen_positions[$position_key])) {
            throw new Exception("Duplicate vote position submitted.");
        }
        $seen_positions[$position_key] = true;

        $validate_candidate->bind_param("i", $candidate_id);
        $validate_candidate->execute();
        $validate_candidate->store_result();
        if ($validate_candidate->num_rows !== 1) {
            throw new Exception("Selected candidate was not found.");
        }
        $validate_candidate->bind_result($candidate_position, $candidate_department, $is_university_wide);
        $validate_candidate->fetch();
        $validate_candidate->free_result();

        // Normalize both positions for comparison - trim whitespace and convert to lowercase
        $candidate_position_normalized = strtolower(trim((string)$candidate_position));
        
        if ($candidate_position_normalized === '' || $candidate_position_normalized !== $position_key) {
            // More descriptive error for debugging - show original values before normalization
            throw new Exception("Candidate does not match selected position. Form: '$position_from_form' vs DB: '$candidate_position'");
        }

        // Department validation: Check if voter is eligible to vote for this candidate
        // University-wide positions (is_university_wide = 1) are open to all students
        // Department positions require the voter to be in the same department
        if ($is_university_wide != 1) {
            // This is a department-specific position
            if ($candidate_department !== $voter_department) {
                throw new Exception("You can only vote for candidates in your department ($voter_department) for this position.");
            }
        }

        $insert_vote->bind_param("sis", $student_id, $candidate_id, $candidate_position);
        $insert_vote->execute();

        $update_candidate->bind_param("i", $candidate_id);
        $update_candidate->execute();
        if ($update_candidate->affected_rows !== 1) {
            throw new Exception("Failed to update candidate vote count.");
        }
    }

    $validate_candidate->close();
    $insert_vote->close();
    $update_candidate->close();

    $mark_voted = $conn->prepare("UPDATE students SET has_voted = TRUE WHERE student_id = ?");
    $mark_voted->bind_param("s", $student_id);
    $mark_voted->execute();
    $mark_voted->close();
    
    $conn->commit();
    log_audit_event(
        $conn,
        (string)$student_id,
        'VOTE_CAST',
        'Student submitted votes for ' . count($submitted_votes) . ' position(s)'
    );
    $_SESSION['has_voted'] = 1;
    header("Location: results.php");
    exit();
} catch (Throwable $e) {
    $conn->rollback();
    error_log("Vote processing error for student {$student_id}: " . $e->getMessage());
    $reason = rawurlencode($e->getMessage());
    header("Location: voting.php?error=Error processing your vote. " . $reason);
    exit();
}
?>
