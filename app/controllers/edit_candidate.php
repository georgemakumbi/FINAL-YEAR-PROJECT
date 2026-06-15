<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_MIDDLEWARE . '/admin_security.php';
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';
require_super_admin();
ensure_csrf_token();


$candidate_id = 0;
$student_id = '';
$first_name = '';
$last_name = '';
$position = '';
$faculty = '';
$manifesto = '';
$image_path = '';

// Get candidate data if ID is provided
if (isset($_GET["id"])) {
    $candidate_id = $_GET["id"];
    
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE candidate_id = ?");
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $candidate = $result->fetch_assoc();
        $student_id = $candidate['student_id'];
        $first_name = $candidate['first_name'];
        $last_name = $candidate['last_name'];
        $position = $candidate['position'];
        $faculty = $candidate['faculty'];
        $manifesto = $candidate['manifesto'];
        $image_path = $candidate['image_path'];
    } else {
        echo "Candidate not found.";
        exit();
    }
    $stmt->close();
}

// Handle form submission
if (isset($_POST['update_candidate'])) {
    verify_csrf_or_die();
    $candidate_id = $_POST['candidate_id'];
    $position = $conn->real_escape_string($_POST['position']);
    $manifesto = $conn->real_escape_string($_POST['manifesto']);
    
    $image_path = $_POST['old_image_path'];
    
    // Handle file upload
    if (isset($_FILES['candidate_image']) && $_FILES['candidate_image']['error'] == UPLOAD_ERR_OK) {
        $upload_error = null;
        $uploaded_path = upload_candidate_image($_FILES['candidate_image'], $upload_error);
        if ($uploaded_path) {
            $image_path = $uploaded_path;
        } else {
            $error = $upload_error;
        }
    }
    
    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE candidates SET position = ?, manifesto = ?, image_path = ? WHERE candidate_id = ?");
        $stmt->bind_param("sssi", $position, $manifesto, $image_path, $candidate_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                log_audit_event(
                    $conn,
                    isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                    'CANDIDATE_UPDATED',
                    'Candidate ID ' . $candidate_id . ' updated (position: ' . $position . ')'
                );
            }
            header('Location: admin_dashboard.php?success=Candidate updated successfully');
            exit();
        } else {
            $error = "Update failed.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Candidate - Admin</title>
    <link rel="icon" href="images/image.png" type="png">
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--app-bg);
            color: var(--text);
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--surface);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        h2 {
            color: var(--text);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--text);
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--input-border);
            border-radius: 5px;
            font-size: 14px;
            background: var(--input-bg);
            color: var(--text);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #003366;
            color: white;
        }
        
        .btn-primary:hover {
            background: #00509e;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
        }
        
        .current-image {
            margin-top: 10px;
        }
        
        .current-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Candidate</h2>
        
        <?php if (isset($error)): ?>
            <p style="color: red; margin-bottom: 15px;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="candidate_id" value="<?php echo $candidate_id; ?>">
            <input type="hidden" name="old_image_path" value="<?php echo htmlspecialchars($image_path); ?>">
            
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" value="<?php echo htmlspecialchars($student_id); ?>" readonly style="background: #f0f0f0;">
            </div>
            
            <div class="form-group">
                <label>Name</label>
                <input type="text" value="<?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>" readonly style="background: #f0f0f0;">
            </div>
            
            <div class="form-group">
                <label>Faculty</label>
                <input type="text" value="<?php echo htmlspecialchars($faculty); ?>" readonly style="background: var(--surface-2); color: var(--text);">
            </div>
            
            <div class="form-group">
                <label>Position</label>
                <select name="position" required>
                    <option value="Guild President" <?php echo $position == 'Guild President' ? 'selected' : ''; ?>>Guild President</option>
                    <option value="Guild Vice President" <?php echo $position == 'Guild Vice President' ? 'selected' : ''; ?>>Guild Vice President</option>
                    <option value="Secretary General" <?php echo $position == 'Secretary General' ? 'selected' : ''; ?>>Secretary General</option>
                    <option value="Finance Minister" <?php echo $position == 'Finance Minister' ? 'selected' : ''; ?>>Finance Minister</option>
                    <option value="Academic Affairs" <?php echo $position == 'Academic Affairs' ? 'selected' : ''; ?>>Academic Affairs</option>
                    <option value="Sports Minister" <?php echo $position == 'Sports Minister' ? 'selected' : ''; ?>>Sports Minister</option>
                    <option value="Cultural Affairs" <?php echo $position == 'Cultural Affairs' ? 'selected' : ''; ?>>Cultural Affairs</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Manifesto</label>
                <textarea name="manifesto" rows="6" required><?php echo htmlspecialchars($manifesto); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Candidate Image</label>
                <input type="file" name="candidate_image" accept="image/*">
                <?php 
                $is_url = (strpos($image_path, 'http://') === 0 || strpos($image_path, 'https://') === 0);
                $exists = $is_url || ($image_path && file_exists(PROJECT_ROOT . '/public/' . $image_path));
                if ($exists): 
                ?>
                    <div class="current-image">
                        <p>Current Image:</p>
                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Current Image">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="button-group">
                <button type="submit" name="update_candidate" class="btn btn-primary">Update Candidate</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <script src="../views/components/includes/theme.js" defer></script>
</body>
</html>
