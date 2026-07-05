<?php
/**
 * =============================================================================
 * FEEDBACK PAGE — With CSRF Protection (Phase 3)
 * =============================================================================
 * 
 * WHAT WAS ADDED:
 *   1. CSRF token generation & verification → prevents spam submissions
 *   2. InputValidator::sanitizeText() → cleans user input
 *   3. Better error handling
 *
 * =============================================================================
 */
require_once '../bootstrap.php';

// Generate CSRF token for the form
ensure_csrf_token();

// ─── Require Login ───────────────────────────────────────────────────────────
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

$message = '';
$message_type = '';

if (isset($_GET['success']) && $_GET['success'] !== '') {
    $message = $_GET['success'];
    $message_type = 'success';
} elseif (isset($_GET['error']) && $_GET['error'] !== '') {
    $message = $_GET['error'];
    $message_type = 'error';
}

// ─── Process Feedback Submission ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Submit'])) {
    
    // SECURITY: Verify CSRF token first
    verify_csrf_or_die();
    
    $student_id = $_SESSION['student_id'];
    
    // SECURITY: Sanitize the feedback text (remove HTML tags, limit length)
    $feedback = InputValidator::sanitizeText($_POST['feedback'] ?? '', 2000);

    if ($feedback === '') {
        header("Location: feedback.php?error=Please enter your feedback");
        exit();
    }

    $sql = "INSERT INTO feedback (student_id, feedback) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $feedback);
    $query = $stmt->execute();
    $stmt->close();

    if ($query) {
        // Log the feedback submission
        log_audit_event($conn, (string)$student_id, 'FEEDBACK_SUBMITTED', 'Student submitted feedback');
        header("Location: feedback.php?success=Feedback received successfully");
    } else {
        header("Location: feedback.php?error=Error sending feedback");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>
    <!-- PWA -->
    <link rel="manifest" href="/finalyearproject/public/manifest.json">
    <meta name="theme-color" content="#1a237e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KU Votes">
    <link rel="apple-touch-icon" href="/finalyearproject/assets/images/icons/icon-180.png">" type="image/png">
    <title>Feedback - Kyambogo University Voting System</title>
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        <?php include ASSETS_CSS . '/feedback.css'; ?>
    </style>
</head>
<body>
    <div class="feedback-card">
        <div class="card-header">
            <h1>Feedback Form</h1>
            <p>Share your experience with the online voting process.</p>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="message <?php echo htmlspecialchars($message_type); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="feedback.php" method="post">
                <!-- CSRF Token — prevents cross-site form forgery -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <label for="feedback">Your Feedback</label>
                <textarea id="feedback" name="feedback" rows="7" maxlength="2000" placeholder="Write your feedback here..." required></textarea>
                <div class="hint">Maximum 2000 characters.</div>

                <div class="actions">
                    <input type="submit" class="btn btn-primary" value="Submit Feedback" name="Submit">
                    <input type="reset" class="btn btn-secondary" value="Clear">
                </div>
            </form>

            <a class="back-link" href="results.php">Back to Election Results</a>
        </div>
        <footer>
            <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
            <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        </footer>
    </div>
    <script src="../assets/js/theme.js" defer></script>
    <script src="/finalyearproject/assets/js/pwa.js" defer></script>
</body>
</html>
