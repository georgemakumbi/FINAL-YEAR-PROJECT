<?php
/**
 * =============================================================================
 * STUDENT BULK IMPORT - Kyambogo University Online Voting System
 * =============================================================================
 * Handles bulk import of students from CSV files.
 * 
 * Features:
 * - CSV file validation
 * - Data validation (email format, required fields)
 * - Duplicate detection (student_id and email)
 * - Transaction rollback on errors
 * - Detailed error reporting
 * - Success/error feedback messages
 * 
 * CSV Format:
 * student_id, first_name, last_name, email, password, faculty, department
 * 
 * Access Control: Super admin only
 * =============================================================================
 */

if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_MIDDLEWARE . '/admin_security.php';
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';


// Ensure only super_admin can access this functionality
require_super_admin();

// Initialize response variables
$success_message = '';
$error_message = '';
$imported_count = 0;
$failed_rows = [];
$warnings = [];

// =============================================================================
// FILE UPLOAD AND PROCESSING
// =============================================================================

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_FILES['csv_file'])) {
    // Verify CSRF token to prevent cross-site request forgery
    verify_csrf_or_die();

    $file = $_FILES['csv_file']['tmp_name'];
    $file_name = $_FILES['csv_file']['name'];
    $file_error = $_FILES['csv_file']['error'];
    $file_size = $_FILES['csv_file']['size'];

    // =================================================================
    // FILE VALIDATION
    // =================================================================
    
    // Check for file upload errors
    if ($file_error !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'File size exceeds server limit.',
            UPLOAD_ERR_FORM_SIZE => 'File size exceeds form limit.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server error: missing temporary directory.',
            UPLOAD_ERR_CANT_WRITE => 'Server error: cannot write file.',
        ];
        $error_message = 'File Upload Error: ' . ($error_messages[$file_error] ?? 'Unknown error');
        header("Location: admin_dashboard.php?section=students&error=" . urlencode($error_message));
        exit();
    }

    // Validate file is not empty
    if ($file_size === 0 || !file_exists($file)) {
        $error_message = 'CSV file is empty or invalid.';
        header("Location: admin_dashboard.php?section=students&error=" . urlencode($error_message));
        exit();
    }

    // Check file extension
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if ($file_ext !== 'csv') {
        $error_message = 'Invalid file type. Please upload a CSV file.';
        header("Location: admin_dashboard.php?section=students&error=" . urlencode($error_message));
        exit();
    }

    // =================================================================
    // CSV PROCESSING
    // =================================================================

    if (($handle = fopen($file, "r")) !== FALSE) {
        // Start database transaction for atomic operation (all or nothing)
        $conn->begin_transaction();
        $success = true;
        $row_number = 0;
        $headers_valid = false;

        try {
            // Read CSV file line by line
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row_number++;

                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }

                // Validate header row (first row must be header)
                if ($row_number === 1) {
                    // CSV Format (password column is now OPTIONAL):
                    // Required: student_id, first_name, last_name, email, faculty, department
                    // Optional 7th column: password (if blank, student sets via OTP registration)
                    $expected_headers_required = ['student_id', 'first_name', 'last_name', 'email', 'faculty', 'department'];
                    $expected_headers_with_pw  = ['student_id', 'first_name', 'last_name', 'email', 'password', 'faculty', 'department'];
                    $csv_headers = array_map('trim', $data);

                    $has_password_col = ($csv_headers === $expected_headers_with_pw);
                    $is_valid_headers = $has_password_col || ($csv_headers === $expected_headers_required);

                    if (!$is_valid_headers) {
                        throw new Exception(
                            'Invalid CSV headers. Expected: ' . implode(', ', $expected_headers_required) .
                            ' (optionally with a "password" column between email and faculty)' .
                            ' | Got: ' . implode(', ', $csv_headers)
                        );
                    }
                    $headers_valid = true;
                    continue; // Skip header row
                }


                // ==========================================================
                // DATA EXTRACTION AND VALIDATION
                // ==========================================================

                // Ensure we have required number of columns (6 required, 7 with optional password)
                if (count($data) < 6) {
                    $failed_rows[] = [
                        'row'    => $row_number,
                        'reason' => 'Insufficient columns. Expected at least 6, got ' . count($data)
                    ];
                    continue;
                }

                // Extract and trim data — handle both 6-col and 7-col formats
                $student_id = trim($data[0] ?? '');
                $first_name = trim($data[1] ?? '');
                $last_name  = trim($data[2] ?? '');
                $email      = trim($data[3] ?? '');

                // Determine if this CSV has a password column
                if (isset($has_password_col) && $has_password_col && count($data) >= 7) {
                    $password   = trim($data[4] ?? '');
                    $faculty    = trim($data[5] ?? '');
                    $department = trim($data[6] ?? '');
                } else {
                    $password   = ''; // No password — student will self-register via OTP
                    $faculty    = trim($data[4] ?? '');
                    $department = trim($data[5] ?? '');
                }

                // Validate required fields
                if (empty($student_id)) {
                    $failed_rows[] = ['row' => $row_number, 'reason' => 'Missing student_id'];
                    continue;
                }
                if (empty($first_name)) {
                    $failed_rows[] = ['row' => $row_number, 'reason' => 'Missing first_name'];
                    continue;
                }
                if (empty($last_name)) {
                    $failed_rows[] = ['row' => $row_number, 'reason' => 'Missing last_name'];
                    continue;
                }
                if (empty($email)) {
                    $failed_rows[] = ['row' => $row_number, 'reason' => 'Missing email'];
                    continue;
                }

                if (empty($faculty)) {
                    $failed_rows[] = ['row' => $row_number, 'reason' => 'Missing faculty'];
                    continue;
                }
                if (empty($department)) {
                    $failed_rows[] = ['row' => $row_number, 'reason' => 'Missing department'];
                    continue;
                }

                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $failed_rows[] = [
                        'row' => $row_number,
                        'data' => $student_id,
                        'reason' => 'Invalid email format: ' . htmlspecialchars($email)
                    ];
                    continue;
                }

                // Check for duplicate student_id in the same file
                $check_stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
                if (!$check_stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $check_stmt->bind_param("s", $student_id);
                $check_stmt->execute();
                if ($check_stmt->get_result()->num_rows > 0) {
                    $failed_rows[] = [
                        'row' => $row_number,
                        'data' => $student_id,
                        'reason' => 'Student ID already exists in database'
                    ];
                    $check_stmt->close();
                    continue;
                }
                $check_stmt->close();

                // Check for duplicate email
                $email_check_stmt = $conn->prepare("SELECT email FROM students WHERE email = ?");
                if (!$email_check_stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $email_check_stmt->bind_param("s", $email);
                $email_check_stmt->execute();
                if ($email_check_stmt->get_result()->num_rows > 0) {
                    $failed_rows[] = [
                        'row' => $row_number,
                        'data' => $student_id,
                        'reason' => 'Email already registered: ' . htmlspecialchars($email)
                    ];
                    $email_check_stmt->close();
                    continue;
                }
                $email_check_stmt->close();

                // ==========================================================
                // DATABASE INSERT
                // ==========================================================

                // Hash password if provided, otherwise store NULL (student self-registers via OTP)
                $password_hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

                // is_registered = TRUE only if a password was provided (admin-set)
                $is_registered = !empty($password) ? 1 : 0;

                // Prepare insert statement
                $insert_stmt = $conn->prepare(
                    "INSERT INTO students (student_id, first_name, last_name, email, password_hash, faculty, department, is_registered) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );

                if (!$insert_stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }

                // Data is safe from SQL injection because we are using prepared statements
                $insert_stmt->bind_param(
                    "sssssssi",
                    $student_id,
                    $first_name,
                    $last_name,
                    $email,
                    $password_hash,
                    $faculty,
                    $department,
                    $is_registered
                );

                // Execute insert
                if (!$insert_stmt->execute()) {
                    throw new Exception('Failed to insert row ' . $row_number . ': ' . $insert_stmt->error);
                }

                $imported_count++;
                $insert_stmt->close();
            }

            // Close file handle
            fclose($handle);

            // =================================================================
            // TRANSACTION FINALIZATION
            // =================================================================

            if ($success && $imported_count > 0) {
                // Commit transaction if at least one student was imported
                $conn->commit();

                // Log audit event
                log_audit_event(
                    $conn,
                    isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                    'STUDENTS_IMPORTED',
                    'Bulk imported ' . $imported_count . ' students from CSV file'
                );

                // Build success message
                $success_message = $imported_count . ' student(s) imported successfully.';
                
                if (!empty($failed_rows)) {
                    $success_message .= ' (' . count($failed_rows) . ' row(s) skipped due to errors)';
                }

                header("Location: admin_dashboard.php?section=students&success=" . urlencode($success_message));
            } elseif (empty($failed_rows)) {
                // No data was processed
                $conn->rollback();
                $error_message = 'No valid student records found in CSV file.';
                header("Location: admin_dashboard.php?section=students&error=" . urlencode($error_message));
            } else {
                // All rows failed
                $conn->rollback();
                $error_message = 'Import failed: All rows contained errors. Please check the CSV format and try again.';
                header("Location: admin_dashboard.php?section=students&error=" . urlencode($error_message));
            }
            exit();

        } catch (Exception $e) {
            // Rollback on any exception
            $conn->rollback();
            fclose($handle);
            $error_message = 'Import Error: ' . $e->getMessage();
            header("Location: admin_dashboard.php?section=students&error=" . urlencode($error_message));
            exit();
        }
    } else {
        // Cannot open file
        $error_message = 'Cannot open CSV file. Please check file permissions.';
        header("Location: admin_dashboard.php?section=students&error=" . urlencode($error_message));
        exit();
    }
} else {
    // Invalid request - redirect to dashboard
    header("Location: admin_dashboard.php?section=students");
    exit();
}
?>
