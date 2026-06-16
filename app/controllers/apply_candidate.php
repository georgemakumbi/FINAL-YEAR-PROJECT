<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_MIDDLEWARE . '/admin_security.php';
ensure_csrf_token();


// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if student has already applied
$check_candidate = $conn->prepare("SELECT candidate_id, status FROM candidates WHERE student_id = ?");
$check_candidate->bind_param("s", $student_id);
$check_candidate->execute();
$result_candidate = $check_candidate->get_result();

$already_applied = false;
$application_status = '';
if ($row = $result_candidate->fetch_assoc()) {
    $already_applied = true;
    $application_status = $row['status'];
}
$check_candidate->close();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$already_applied) {
    verify_csrf_or_die();
    
    $position = $_POST['position'];
    $manifesto = $_POST['manifesto'];
    
    $is_university_wide = 0;
    if (in_array($position, ['Guild President', 'Guild Vice President'])) {
        $is_university_wide = 1;
    }
    
    // Get student details
    $stmt = $conn->prepare("SELECT first_name, last_name, faculty, department FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $department = $is_university_wide ? null : $student['department'];
    
    // Handle image upload
    $image_path = null;
    if (isset($_FILES['candidate_image']) && $_FILES['candidate_image']['error'] == UPLOAD_ERR_OK) {
        $upload_error = null;
        $uploaded_path = upload_candidate_image($_FILES['candidate_image'], $upload_error);
        if ($uploaded_path) {
            $image_path = $uploaded_path;
        } else {
            $message = $upload_error;
            $message_type = "error";
        }
    }

    if (empty($message_type)) {
        // Insert application
        $insert_stmt = $conn->prepare("INSERT INTO candidates (student_id, first_name, last_name, position, faculty, department, manifesto, image_path, is_university_wide, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $insert_stmt->bind_param("ssssssssi", $student_id, $student['first_name'], $student['last_name'], $position, $student['faculty'], $department, $manifesto, $image_path, $is_university_wide);
        
        if ($insert_stmt->execute()) {
            $already_applied = true;
            $application_status = 'pending';
            $message = "Application submitted successfully! It is pending admin verification.";
            $message_type = "success";
        } else {
            $message = "Error submitting application. Please try again.";
            $message_type = "error";
        }
        $insert_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Candidacy - Kyambogo University</title>
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        <?php include ASSETS_CSS . '/voting.css'; ?>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: var(--text); }
        .form-control { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 6px; box-sizing: border-box; background: var(--surface); color: var(--text); }
        .btn-submit { background-color: var(--primary); color: blue; border: none; padding: 12px 20px; font-size: 16px; border-radius: 6px; cursor: pointer; display: inline-block; }
        .btn-submit:hover { background-color: var(--primary-dark); }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info { background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.9em; font-weight: bold; }
        .badge-pending { background-color: #ffeeba; color: #856404; }
        .badge-verified { background-color: #c3e6cb; margin-left: 10px; color: #155724; }
        .badge-rejected { background-color: #f5c6cb; color: #721c24; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="Kyambogo University Logo">
            <div class="university-name">KYAMBOGO UNIVERSITY ONLINE VOTING SYSTEM</div>
        </div>
        <div class="user-info" style="display: flex; gap: 15px; align-items: center;">
            <a href="voting.php" class="vote-btn" style="padding: 5px 15px; text-decoration: none; font-size: 0.9em; width: auto; margin:0;">Back to Voting</a>
            Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            <form action="logout.php" method="POST" style="margin:0;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>
    
    <div class="container" style="max-width: 800px; margin: 40px auto; background: var(--surface-2); padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h1>Apply for Candidacy</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($already_applied): ?>
            <div class="alert alert-info">
                <h3>Application Status</h3>
                <p>You have already submitted an application.</p>
                <p>Status: <span class="badge badge-<?php echo $application_status; ?>"><?php echo strtoupper($application_status); ?></span></p>
                <?php if ($application_status == 'pending'): ?>
                    <p style="margin-top: 10px; font-size: 0.9em;">Your application is currently being reviewed by the administration.</p>
                <?php elseif ($application_status == 'verified'): ?>
                    <p style="margin-top: 10px; font-size: 0.9em;">Congratulations! Your application has been verified. You will appear on the ballot.</p>
                <?php else: ?>
                    <p style="margin-top: 10px; font-size: 0.9em;">Your application was rejected. Please contact the administration for more info.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <form action="apply_candidate.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="form-group">
                    <label>Position</label>
                    <select name="position" class="form-control" required>
                        <option value="">-- Select Position --</option>
                        <option value="Guild President">Guild President</option>
                        <option value="Guild Vice President">Guild Vice President</option>
                        <option value="Secretary General">Secretary General</option>
                        <option value="Finance Minister">Finance Minister</option>
                        <option value="Academic Affairs">Academic Affairs</option>
                        <optgroup label="GRCs">
                            <option value="Faculty of Science">Faculty of Science</option>
                            <option value="Faculty of Engineering">Faculty of Engineering</option>
                            <option value="Faculty of Social sciences">Faculty of Social sciences</option>
                        </optgroup>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Manifesto</label>
                    <textarea name="manifesto" rows="6" class="form-control" placeholder="Write your manifesto here..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Candidate Photo (JPG, PNG, WEBP, max 2MB)</label>
                    <input type="file" name="candidate_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                </div>
                
                <div class="form-group" style="text-align: right; margin-top: 30px;">
                    <button type="submit" class="btn-submit">Submit Application</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
        <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
    </footer>
    <script src="../views/components/includes/theme.js" defer></script>
</body>
</html>
