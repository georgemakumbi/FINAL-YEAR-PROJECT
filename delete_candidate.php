<?php
include 'db_connection.php';
require 'admin_security.php';
require_once 'includes/audit_logger.php';
require_super_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_dashboard.php?error=Invalid request method");
    exit();
}

verify_csrf_or_die();

$candidate_id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
if ($candidate_id > 0) {

    // First get candidate data and image path
    $stmt = $conn->prepare("SELECT image_path, student_id, first_name, last_name, position FROM candidates WHERE candidate_id = ?");
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $candidate_details = null;
    if ($result->num_rows === 1) {
        $candidate = $result->fetch_assoc();
        $candidate_details = $candidate;
        
        // Delete the image file if it exists
        if ($candidate['image_path'] && strpos($candidate['image_path'], 'candidates/') === 0 && file_exists($candidate['image_path'])) {
            unlink($candidate['image_path']);
        }
    }
    $stmt->close();
    
    // Delete the candidate
    $stmt = $conn->prepare("DELETE FROM candidates WHERE candidate_id = ?");
    $stmt->bind_param("i", $candidate_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $details = 'Candidate ID ' . $candidate_id . ' deleted';
            if ($candidate_details !== null) {
                $details = 'Candidate ' . $candidate_details['student_id'] . ' (' . $candidate_details['first_name'] . ' ' . $candidate_details['last_name'] . ') deleted from ' . $candidate_details['position'];
            }
            log_audit_event(
                $conn,
                isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                'CANDIDATE_DELETED',
                $details
            );
        }
        header("Location: admin_dashboard.php?success=Candidate deleted successfully");
    } else {
        header("Location: admin_dashboard.php?error=Error deleting candidate");
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    header("Location: admin_dashboard.php?error=Invalid candidate");
    exit();
}
?>
