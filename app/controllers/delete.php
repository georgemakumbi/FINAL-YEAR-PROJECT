<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_MIDDLEWARE . '/admin_security.php';
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';
require_super_admin();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_dashboard.php?error=Invalid request method");
    exit();
}

verify_csrf_or_die();

$id = trim($_POST["id"] ?? '');
if ($id === '') {
    header("Location: admin_dashboard.php?error=Invalid student ID");
    exit();
}

$student_details = null;
$lookup_stmt = $conn->prepare("SELECT first_name, last_name FROM students WHERE student_id = ?");
$lookup_stmt->bind_param("s", $id);
$lookup_stmt->execute();
$lookup_result = $lookup_stmt->get_result();
if ($lookup_result && $lookup_result->num_rows === 1) {
    $student_details = $lookup_result->fetch_assoc();
}
$lookup_stmt->close();

$stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $details = 'Student ' . $id . ' deleted';
        if ($student_details !== null) {
            $details = 'Student ' . $id . ' (' . $student_details['first_name'] . ' ' . $student_details['last_name'] . ') deleted';
        }
        log_audit_event(
            $conn,
            isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
            'STUDENT_DELETED',
            $details
        );
    }
    header("Location: admin_dashboard.php?success=Student deleted successfully");
} else {
    header("Location: admin_dashboard.php?error=Failed to delete student");
}

$stmt->close();
$conn->close();
exit();
?>
