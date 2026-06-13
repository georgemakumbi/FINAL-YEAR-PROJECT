<?php
/**
 * Bulk Notification Sender
 * Sends email notifications to selected students from admin dashboard.
 * Super admin only.
 */

// Session is started by bootstrap.php (via public/send_notifications.php)
require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

// Super admin only
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    header("Location: admin_dashboard.php?error=Super admin access required.");
    exit();
}

// CSRF validation
verify_csrf_or_die();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_dashboard.php?section=students");
    exit();
}

$student_ids = $_POST['student_ids'] ?? [];
$message = trim($_POST['notification_message'] ?? '');

if (empty($student_ids) || empty($message)) {
    header("Location: admin_dashboard.php?section=students&error=Select students and enter a message.");
    exit();
}

// Limit to prevent abuse/timeouts
if (count($student_ids) > 500) {
    header("Location: admin_dashboard.php?section=students&error=Too many students selected (max 500).");
    exit();
}

// Sanitize student IDs
$student_ids = array_map('trim', $student_ids);
$student_ids = array_filter($student_ids, fn($id) => !empty($id));

// Fetch student details (prepared statement)
$placeholders = str_repeat('?,', count($student_ids) - 1) . '?';
$stmt = $conn->prepare("SELECT student_id, email, first_name, last_name FROM students WHERE student_id IN ($placeholders)");
$stmt->bind_param(str_repeat('s', count($student_ids)), ...$student_ids);
$stmt->execute();
$students = $stmt->get_result();

$sent = 0;
$failed = 0;

while ($student = $students->fetch_assoc()) {
    // Build personalized HTML email
    $subject = 'Kyambogo University Voting System Notification';
    $full_name = $student['first_name'] . ' ' . $student['last_name'];
    $html_message = "
    <html>
    <head><title>Voting Notification</title></head>
    <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
        <h2 style='color: #3498db;'>📧 Kyambogo University Voting System</h2>
        <p>Dear <strong>$full_name</strong>,</p>
        <div style='background: #f8f9fa; padding: 20px; border-left: 4px solid #3498db; margin: 20px 0;'>
            " . nl2br(htmlspecialchars($message)) . "
        </div>
        <p>Best regards,<br><strong>Admin Team</strong></p>
        <hr style='margin: 30px 0;'>
        <p style='color: #666; font-size: 12px;'>This is an automated message. Please do not reply.</p>
    </body>
    </html>";

    // Send email
    if (send_smtp_email($student['email'], $subject, $html_message, $full_name)) {
        $sent++;
        
        // Audit log
        log_audit_event($conn, $student['student_id'], 'NOTIFICATION_SENT', "Notification sent to {$full_name} ({$student['email']})");
    } else {
        $failed++;
        error_log("Notification failed for {$student['student_id']} ({$student['email']})");
    }
}

$stmt->close();

// Success redirect with counts
$status = $sent > 0 ? "success=Succeeded: $sent sent" : "error=No notifications sent";
if ($failed > 0) {
    $status .= ", Failed: $failed";
}
header("Location: admin_dashboard.php?section=students&$status");
exit();
?>

