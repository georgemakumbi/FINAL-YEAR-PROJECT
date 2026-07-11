<?php
// Set the default timezone to Kampala, Africa
date_default_timezone_set('africa/kampala');

// Include database connection
require_once '../bootstrap.php';

// Get the active election's end date from the database
$election_query = $conn->query("SELECT end_date FROM elections WHERE status = 'active' ORDER BY end_date DESC LIMIT 1");
if ($election_query && $election_row = $election_query->fetch_assoc()) {
    $deadline_str = $election_row['end_date'];
} else {
    // Fallback: if no active election, check if there are any elections with end_date in the future
    $election_query = $conn->query("SELECT end_date FROM elections WHERE end_date > NOW() ORDER BY end_date DESC LIMIT 1");
    if ($election_query && $election_row = $election_query->fetch_assoc()) {
        $deadline_str = $election_row['end_date'];
    } else {
        // Ultimate fallback: check the settings table for an admin-set deadline
        $settings_row = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'voting_deadline' LIMIT 1");
        $deadline_str = ($settings_row && $sr = $settings_row->fetch_assoc()) ? trim($sr['setting_value']) : '';
    }
}

// Convert deadline to Unix timestamp
$deadline = strtotime($deadline_str); 
$now = time(); // Get current Unix timestamp
$remaining = $deadline - $now; // Calculate remaining seconds until deadline
$expired = $remaining <= 0; // Check if deadline has passed

if($expired) {
    // If the deadline has passed, set deadline to now and show inactive message
    $deadline = $now;
    echo "The voting system is now inactive due to the deadline being reached.";
    echo "<div id='countdown'>TIME OUT!</div>";
    echo "<div id='main-content' class='inactive'>";    
    header('Location: results.php'); // Redirect to results page
    exit(); // Stop further execution after redirect
}
else {
    // If the deadline has not passed, show active message with deadline time
    $deadline = strtotime($deadline_str); // Ensure deadline is set correctly
    echo "The voting system is active. Countdown is running until " . date("Y-m-d H:i:s", $deadline);
}
?>

<script>
// Get references to countdown and main content elements by their IDs
const countdownElement = document.getElementById("countdown");
const contentElement = document.getElementById("main-content");

// Set the deadline in milliseconds for JavaScript countdown
const deadline = <?= $deadline * 1000 ?>;

// Start a timer that updates every second
const timer = setInterval(() => {
    const now = new Date().getTime(); // Get current time in milliseconds
    const distance = deadline - now; // Calculate remaining time

    if (distance <= 0) {
        // If countdown is finished, clear timer and update UI to show timeout
        clearInterval(timer);
        countdownElement.innerHTML = "TIME OUT!";
        contentElement.classList.add("inactive"); // Optionally add a CSS class for styling
        contentElement.innerHTML = "<p>The system is now inactive. Please check back later.</p>";
        return;
    }

    // Calculate days, hours, minutes, and seconds remaining
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Update the countdown display
    countdownElement.innerHTML = `${days} Days ${hours} Hrs ${minutes} Mins ${seconds} Secs`;
}, 1000); // Repeat every 1000 milliseconds (1 second)
</script>

