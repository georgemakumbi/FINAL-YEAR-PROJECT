<?php
include 'db_connection.php';
require_once 'includes/audit_logger.php';
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Initialize error message variable
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    if (empty($_POST['student_id']) || empty($_POST['password'])) {
        $error_message = "Student ID and password are required";
    } else {
        $student_id = $conn->real_escape_string($_POST['student_id']);
        $password = $_POST['password'];
        
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT student_id, first_name, last_name, email, faculty, department, password_hash, has_voted FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $student['password_hash'])) {
                session_regenerate_id(true);
                // Store user data in session
                $_SESSION['student_id'] = $student['student_id'];
                $_SESSION['first_name'] = $student['first_name'];
                $_SESSION['last_name'] = $student['last_name'];
                $_SESSION['email'] = $student['email'];
                $_SESSION['faculty'] = $student['faculty'];
                $_SESSION['department'] = $student['department']; // Store department for filtering
                $_SESSION['has_voted'] = $student['has_voted']; // Store voting status
                log_audit_event(
                    $conn,
                    (string)$student['student_id'],
                    'STUDENT_LOGIN',
                    'Student ' . $student['student_id'] . ' logged in'
                );
                
                // Redirect based on voting status
                if ($student['has_voted']) {
                    header("Location: results.php");
                } else {
                    header("Location: voting.php");
                }
                exit();
            } else {
                $error_message = "Incorrect password";
            }
        } else {
            $error_message = "Student not found";
        }
        $stmt->close();
    }
    
    // If we get here, there was an error
    header("Location: login.html?error=" . urlencode($error_message));
    exit();
} else {
    // Not a POST request, redirect to login
    header("Location: login.html");
    exit();
}
?>
