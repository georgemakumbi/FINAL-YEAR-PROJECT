<?php
include 'db_connection.php';
require 'admin_security.php';
require_once 'includes/audit_logger.php';
require_super_admin();
ensure_csrf_token();

$student_id = '';
$first_name = '';
$last_name = '';
$email = '';
$faculty = '';
$department = '';

if (isset($_GET["id"])) {
    $student_id = $_GET["id"];

    // Fetch student data
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        $first_name = $student['first_name'];
        $last_name = $student['last_name'];
        $email = $student['email'];
        $faculty = $student['faculty'];
        $department = $student['department'];
    } else {
        echo "Student not found.";
        exit();
    }

    $stmt->close();
}

if (isset($_POST['submit'])) {
    verify_csrf_or_die();
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $faculty = $_POST['faculty'];
    $department = $_POST['department'];

    $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, email=?, faculty=?, department=? WHERE student_id=?");
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $faculty, $department, $student_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            log_audit_event(
                $conn,
                isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                'STUDENT_UPDATED',
                'Student ' . $student_id . ' updated'
            );
        }
        header('Location: admin_dashboard.php?success=Student updated successfully');
        exit(); // Important to stop execution after header
    } else {
        echo "Update failed.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        <?php include 'styles/update.css'; ?>
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Edit Student Information</h2>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="faculty">Faculty</label>
                    <input type="text" name="faculty" id="faculty" value="<?php echo htmlspecialchars($faculty); ?>" required>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($department); ?>" required>
                </div>

                <div class="actions">
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                    <input type="submit" value="Update Student" name="submit" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
