<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_MIDDLEWARE . '/admin_security.php';
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';
require_super_admin();


// Enter candidate details if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_or_die();
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $position = $conn->real_escape_string($_POST['position']);
    $manifesto = $conn->real_escape_string($_POST['manifesto']);
    
    // Get department from form (for department-specific candidates)
    $department = isset($_POST['department']) ? $conn->real_escape_string($_POST['department']) : null;
    
    // Check if this is a university-wide position
    $is_university_wide = isset($_POST['is_university_wide']) ? 1 : 0;
    
    // If university-wide, clear the department
    if ($is_university_wide) {
        $department = null;
    }
    
    // Get student details of the candidate entered
    $stmt = $conn->prepare("SELECT first_name, last_name, faculty, department FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    //incase the candidate is not a student
    if ($result->num_rows == 0) {
        header("Location: admin_dashboard.php?error=Student not found");
        exit();
    }
    
    $student = $result->fetch_assoc();
    
    // If no department specified, use student's department
    if ($department === null || $department === '') {
        $department = $student['department'];
    }
    
    // Handle file upload
    $image_path = null;
    if (isset($_FILES['candidate_image']) && $_FILES['candidate_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = PROJECT_ROOT . '/public/candidates/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $max_size = 2 * 1024 * 1024; // 2MB
        if ($_FILES['candidate_image']['size'] > $max_size) {
            header("Location: admin_dashboard.php?error=Image too large. Max 2MB");
            exit();
        }

        $allowed_mime = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['candidate_image']['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowed_mime[$mime])) {
            header("Location: admin_dashboard.php?error=Invalid image format");
            exit();
        }

        $file_name = 'candidate_' . bin2hex(random_bytes(8)) . '.' . $allowed_mime[$mime];
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['candidate_image']['tmp_name'], $target_file)) {
            // Store RELATIVE path in DB (relative to public)
            $image_path = 'candidates/' . $file_name;
        } else {
            header("Location: admin_dashboard.php?error=Failed to upload candidate image");
            exit();
        }
    }
    
    // Insert candidate with department and university-wide flag
    $stmt = $conn->prepare("INSERT INTO candidates (student_id, first_name, last_name, position, faculty, department, manifesto, image_path, is_university_wide) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", $student_id, $student['first_name'], $student['last_name'], $position, $student['faculty'], $department, $manifesto, $image_path, $is_university_wide);
    
    if ($stmt->execute()) {
        log_audit_event(
            $conn,
            isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
            'CANDIDATE_ADDED',
            'Candidate ' . $student_id . ' (' . $student['first_name'] . ' ' . $student['last_name'] . ') added for ' . $position
        );
        header("Location: admin_dashboard.php?success=Candidate added successfully");
    } else {
        header("Location: admin_dashboard.php?error=Error adding candidate");
    }
    exit();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
