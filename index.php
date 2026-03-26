<?php
    date_default_timezone_set('Africa/Kampala');
    
    // Include database connection
    require 'db_connection.php';
    
    // Get the election end date from the database
    $election_query = $conn->query("SELECT end_date FROM elections WHERE status = 'active' ORDER BY end_date DESC LIMIT 1");
    if ($election_query && $election_row = $election_query->fetch_assoc()) {
        $deadline_str = $election_row['end_date'];
    } else {
        // Fallback: if no active election, check if there are any elections with end_date in the future
        $election_query = $conn->query("SELECT end_date FROM elections WHERE end_date > NOW() ORDER BY end_date DESC LIMIT 1");
        if ($election_query && $election_row = $election_query->fetch_assoc()) {
            $deadline_str = $election_row['end_date'];
        } else {
            // Ultimate fallback to deadline.txt if no election found
            $deadline_file = "deadline.txt";
            $deadline_str = file_exists($deadline_file) ? trim(file_get_contents($deadline_file)) : '+1 day';
        }
    }
    
    $deadline = strtotime($deadline_str);
    $now = time();
    $remaining = $deadline - $now;
    $expired = $remaining <= 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyambogo Guild Voting Portal</title>
    <link rel="icon" href="images/image.png" type="image/png">
    <style>
        <?php include 'styles/theme.css' ?>
        <?php include 'styles/index.css' ?>
    </style>
</head>
<body>

    <header class="header">
        <div class="logo">
            <img src="images/image.png" alt="KyU Logo">
        </div>
        <h1>Kyambogo Guild Online Voting Portal</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Knowledge And Skills For Service</p>
    </header>

    <div class="container">
        <section class="card countdown-container">
            <h2 style="font-size: 1rem; text-transform: uppercase; opacity: 0.9;">Time Remaining to Vote</h2>
            <div class="countdown" id="countdown">Loading...</div>
        </section>

        <section class="card">
            <h2 style="color: var(--primary); margin-bottom: 15px;">Official Announcement</h2>
            <p>This is the official online portal for Kyambogo University student registration and voting. 
            Elections are scheduled to commence in <strong>MARCH<?php  ?></strong>.</p>
            <br>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Note: All candidates must submit their manifesto and passport photos to the Dean's office for verification.</p>
        </section>

        <section class="card">
            <h2 style="color: var(--primary);">Voting Procedures</h2>
            <ul class="voting-steps">
                <li>Login using your student credentials</li>
                <li>Verify your personal details on the dashboard</li>
                <li>Review candidate profiles and manifestos</li>
                <li>Submit your ballot securely</li>
                <li><b>View final results via your portal</b></li>
            </ul>
        </section>

        <div id="main-content" class="<?= $expired ? 'inactive' : '' ?>">
            <?php if ($expired): ?>
                <div class="card" style="text-align: center; border: 2px solid #dc3545; background: #f8d7da; color: #721c24;">
                    <h3 style="margin-bottom: 10px;">⚠️ ELECTIONS CLOSED BY ADMINISTRATION</h3>
                    <p><strong>No active elections available.</strong></p>
                    <p style="font-size: 0.9em; opacity: 0.8;">Contact election officials for updates.</p>
                </div>
            <?php else: ?>
                <section class="login-section">
                    <h3 style="margin-bottom: 20px;">Access the Portal</h3>
                    <div class="btn-group">
                        <a href="login.html" class="btn btn-student">Login As Student</a>
                    </div>
                </section>
            <?php endif; ?>
            
        </div>

    </div>
    <p style="color: var(--text); font-size: 1.1rem; text-align: center;"><strong>KYAMBOGO UNIVERSITY DECIDES</strong></p>
    <footer>
        <div class="quick-links">
            <p>Technical Support: <a href="mailto:admin@kyu.ac.ug">admin@kyu.ac.ug</a> | <a href="tel:+256 747077274">+256 747 077 274</a></p>
        </div>
        <div class="quick-links" style="margin: 20px 0;">
            <a target="_blank" href="https://kyu.ac.ug/">KYU Home</a> |
            <a target="_blank" href="https://www.youtube.com/watch?v=qczpveru4Jc">Voting Tutorial</a> |
            <a target="_blank" href="https://gradsch.kyu.ac.ug/privacy-policy/">Privacy Policy</a> |
            <a  href="about_us.php">About Us</a>
        </div>
        <p class="copyright">&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        <p style="font-size: 0.8rem; color: var(--text-muted);">Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
    </footer>

    <script src="includes/theme.js" defer></script>
    <script>
        const countdownElement = document.getElementById("countdown");
        const contentElement = document.getElementById("main-content");
        const deadline = <?= $deadline * 1000 ?>;

        const timer = setInterval(() => {
            const now = new Date().getTime();
            const distance = deadline - now;

            if (distance <= 0) {
                clearInterval(timer);
                countdownElement.innerHTML = "VOTING CLOSED";
                contentElement.classList.add("inactive");
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }, 1000);
    </script>
<script>
document.addEventListener('keydown', function(e) {
  if (e.shiftKey && e.key.toLowerCase() === 'a') {
    e.preventDefault();
    window.location.href = 'admin_login.html';
  }
});
</script>
</body>
</html>
