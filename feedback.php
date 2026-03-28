<?php
include 'db_connection.php';
session_start();



if (!isset($_SESSION['student_id'])) {
    header("Location: index.html?error=Please login first");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Submit'])) {
    $student_id = $_SESSION['student_id'];
    $feedback = trim($_POST['feedback'] ?? '');

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
    <link rel="icon" href="images/image.png" type="png">
    <title>Feedback - Kyambogo University Voting System</title>
    <style>
        <?php include 'styles/theme.css'; ?>
        <?php include 'styles/feedback.css'; ?>
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
    <script src="includes/theme.js" defer></script>
</body>
</html>
