<?php
include 'db_connection.php';
session_start();
require_once 'includes/results_publish.php';

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php?error=Please login first");
    exit();
}

// If session doesn't have has_voted, fetch from DB
if (!isset($_SESSION['has_voted'])) {
    $student_id = $_SESSION['student_id'];
    $check_voted = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ?");
    $check_voted->bind_param("s", $student_id);
    $check_voted->execute();
    $check_voted->bind_result($_SESSION['has_voted']);
    $check_voted->fetch();
    $check_voted->close();
}

// Get election results
$results_published = results_are_published();
$results = null;
if ($results_published) {
    $results_query = "
        SELECT c.position, c.candidate_id, c.first_name, c.last_name, c.faculty, c.votes, 
               CASE 
                   WHEN total.total_votes > 0 THEN (c.votes / total.total_votes * 100)
                   ELSE 0
               END as percentage
        FROM candidates c
        JOIN (
            SELECT position, SUM(votes) as total_votes
            FROM candidates
            GROUP BY position
        ) total ON c.position = total.position
        ORDER BY c.position, c.votes DESC
    ";
    $results = $conn->query($results_query);
}

// Check if user has voted to show appropriate message
$student_id = $_SESSION['student_id'];
$check_voted = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ?");
$check_voted->bind_param("s", $student_id);
$check_voted->execute();
$check_voted->bind_result($has_voted);
$check_voted->fetch();
$check_voted->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyambogo University - Election Results</title>
    <style>
        <?php include 'styles/theme.css'; ?>
        <?php include 'styles/results.css'; ?>
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/image.png" alt="Kyambogo University Logo">
            <div class="university-name">KYAMBOGO UNIVERSITY ONLINE VOTING SYSTEM</div>
        </div>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['first_name']; ?>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>
    
    <div class="container">
        <h1>Election Results</h1>
        
        <div class="vote-message <?php echo $has_voted ? 'voted' : 'not-voted'; ?>">
            <?php 
            if ($has_voted) {
                echo "Thank you for participating in the election. Your vote has been recorded.";
            } else {
                // Redirect to voting page instead of showing error message
                header("Location: voting.php");
                exit();
            }
            ?>
        </div>

        <?php if (!$results_published): ?>
            <div class="vote-message not-voted">
                Results are not yet published. Please check back later.
            </div>
        <?php else: ?>
            <?php
            $current_position = "";
            while ($row = $results->fetch_assoc()) {
                if ($row['position'] != $current_position) {
                    // Close previous position section if exists
                    if ($current_position != "") {
                        echo '</table></div>';
                    }
                    // Start new position section
                    $current_position = $row['position'];
                    echo '<div class="position-section">';
                    echo '<h2 class="position-title">' . htmlspecialchars($current_position) . '</h2>';
                    echo '<table class="results-table">';
                    echo '<thead><tr><th>Candidate</th><th>Faculty</th><th>Votes</th><th>Percentage</th></tr></thead>';
                    echo '<tbody>';
                }
                
                $is_winner = false;
                // Simple logic to highlight winner (first row in each position group is the winner due to ORDER BY votes DESC)
                if ($current_position == $row['position'] && !isset($winner_shown[$current_position])) {
                    $is_winner = true;
                    $winner_shown[$current_position] = true;
                }
                
                $percentage_value = is_numeric($row['percentage']) ? (float)$row['percentage'] : 0;
                $percentage_width = round($percentage_value);
                $percentage_label = round($percentage_value, 1);

                echo '<tr' . ($is_winner ? ' class="winner"' : '') . '>';
                echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['faculty']) . '</td>';
                echo '<td>' . htmlspecialchars($row['votes']) . '</td>';
                echo '<td>';
                echo '<div class="percentage-bar-container">';
                echo '<div class="percentage-bar" style="width: ' . $percentage_width . '%;">' . $percentage_label . '%</div>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            // Close the last position section
            if ($current_position != "") {
                echo '</tbody></table></div>';
            }
            ?>
        <?php endif; ?>
    </div>
    <div class="feedback">
        <h2>Feedback</h2>
        <a href="feedback.php"><button type="button" class="feedback-btn">Share your experience with the online voting process.</button></a>
        <footer style="text-align: center; background-color: var(--footer-bg); color: var(--footer-text); padding: 20px; margin-top: 50px;">
        <p >Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
        <p">&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
    </footer>
    </div>
    <script src="includes/theme.js" defer></script>
</body>
</html>
