<?php
include 'db_connection.php';
session_start();
require 'admin_security.php';
ensure_csrf_token();

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html?error=Please login first");
    exit();
}

// Check if user has already voted
$student_id = $_SESSION['student_id'];
$check_voted = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ?");
$check_voted->bind_param("s", $student_id);
$check_voted->execute();
$check_voted->bind_result($has_voted);
$check_voted->fetch();
$check_voted->close();

if ((int)$has_voted === 1) {
    $_SESSION['has_voted'] = 1;
    header("Location: results.php");
    exit();
}
$_SESSION['has_voted'] = 0;

require 'countdown.php'; // Include countdown logic

// Get voter's department from session for filtering
$voter_department = isset($_SESSION['department']) ? $_SESSION['department'] : '';

// Get candidates - filter by department for non-university-wide positions
// University-wide positions show all candidates, department positions show only matching candidates
$candidates_query = "
    SELECT * FROM candidates 
    WHERE is_university_wide = 1 
       OR department = ? 
       OR department IS NULL
    ORDER BY position, last_name
";
$stmt = $conn->prepare($candidates_query);
$stmt->bind_param("s", $voter_department);
$stmt->execute();
$candidates_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyambogo University - Cast Your Vote</title>
    <link rel="icon" href="images/image.png" type="png">
    <style>
        <?php include 'styles/voting.css'; ?>
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
        <h1>Cast Your Vote</h1>
        <?php if (isset($_GET['error']) && $_GET['error'] !== ''): ?>
            <div class="instructions" style="background-color:#fdecea; border-left-color:#d9534f; color:#8a1f11;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <div class="instructions">
            <h3>Voting Instructions:</h3>
            <ol>
                <li>Select your preferred candidate for each position by clicking on their card.</li>
                <li>You can only vote for one candidate per position.</li>
                <li>Review your selections before submitting.</li>
                <li>Once submitted, your votes cannot be changed.</li>
            </ol>
        </div>
        
        <form id="votingForm" action="processvote.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <?php
            $current_position = "";
            while ($candidate = $candidates_result->fetch_assoc()) {
                if ($candidate['position'] != $current_position) {
                    // Close previous position section if exists
                    if ($current_position != "") {
                        echo '</div></div>';
                    }
                    // Start new position section
                    $current_position = $candidate['position'];
                    echo '<div class="position-section">';
                    echo '<h2 class="position-title">' . htmlspecialchars($current_position) . '</h2>';
                    echo '<div class="candidates-grid">';
                }
                
                $position_js = htmlspecialchars(json_encode($candidate['position']), ENT_QUOTES, 'UTF-8');
                echo '<div class="candidate-card" onclick="selectCandidate(this, ' . $position_js . ')">';
                echo '<input type="radio" name="' . htmlspecialchars($candidate['position']) . '" value="' . (int)$candidate['candidate_id'] . '" class="hidden" required>';
                echo '<img src="' . ($candidate['image_path'] ? htmlspecialchars($candidate['image_path']) : 'default_profile.jpg') . '" alt="Candidate Image" class="candidate-image">';
                echo '<div class="candidate-name">' . htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']) . '</div>';
                echo '<div class="candidate-manifesto">' . htmlspecialchars($candidate['manifesto']) . '</div>';
                echo '<div class="candidate-position">' . htmlspecialchars($candidate['position']) . '</div>';
                echo '<div class="candidate-faculty">' . htmlspecialchars($candidate['faculty']) . '</div>';
                echo '</div>';
            }
            // Close the last position section
            if ($current_position != "") {
                echo '</div></div>';
            }
            ?>
            
            <button type="submit" class="vote-btn" id="submitVote">Submit Votes</button>
        </form>
    </div>
    <footer>
        <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
        <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
    </footer>
    <script>
        function selectCandidate(card, position) {
            // Deselect all candidates for this position
            const positionCards = document.querySelectorAll('.candidate-card');
            positionCards.forEach(c => {
                if (c.querySelector('input').name === position) {
                    c.classList.remove('selected');
                    c.querySelector('input').checked = false;
                }
            });
            
            // Select the clicked candidate
            card.classList.add('selected');
            card.querySelector('input').checked = true;
            
            // Check if all positions have selections to enable submit button
            checkFormCompletion();
        }
        
        function checkFormCompletion() {
            const form = document.getElementById('votingForm');
            const submitBtn = document.getElementById('submitVote');
            let allSelected = true;
            
            // Get all position radio groups
            const radioGroups = {};
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                if (!radioGroups[radio.name]) {
                    radioGroups[radio.name] = false;
                }
                if (radio.checked) {
                    radioGroups[radio.name] = true;
                }
            });
            
            // Check if all groups have a selection
            for (const group in radioGroups) {
                if (!radioGroups[group]) {
                    allSelected = false;
                    break;
                }
            }
            
            submitBtn.disabled = !allSelected;
        }
        
        // Initial check
        checkFormCompletion();
    </script>
</body>
</html>
