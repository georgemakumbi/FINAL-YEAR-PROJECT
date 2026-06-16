<?php require_once '../bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Kyambogo Guild Online Voting Portal</title>
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/about_us.css">
</head>
<body>
    <header class="header">
        <h1>About This Project</h1>
        <p>Kyambogo Guild Online Voting Portal</p>
    </header>

    <main class="container">
        <section class="card">
            <h2>Project Team</h2>
            <p>
                This system was designed and developed as a final year project by Kyambogo University students doing Bachelor's degree in Information Technology.
                It aims to provide a secure, transparent, and efficient online voting experience for university elections.
            </p>

            <div class="team-grid">
                <article class="member">
                    <h3>MAKUMBI GEORGE</h3>
                    <p><strong>Program:</strong> Bachelors degree in Information Technology</p>
                    <p><strong>Role:</strong> Project Manager and Backend Development</p>
                    <p><strong>Contact:</strong><a href="mailto:makumbigeorge5@gmail.com"> makumbigeorge5@gmail.com</a> | <a href="tel:+256 747077274">+256 747077274</a></p>
                </article>

                <article class="member">
                    <h3>AFUNGA RONALD MICHEAL</h3>
                    <p><strong>Program:</strong> Bachelors degree in Information Technology</p>
                    <p><strong>Role:</strong>Frontend Design and UI Development</p>
                    <p><strong>Contact:</strong><a href="mailto:afungaronald@gmail.com"> afungaronald@gmail.com</a> | <a href="tel:+256 764 511417">+256 764 511417</a></p>
                </article>

                <article class="member">
                    <h3>BEKISA PAULYNE</h3>
                    <p><strong>Program:</strong> Bachelors degree in Information Technology</p>
                    <p><strong>Role:</strong> Authentication, Security, and Testing</p>
                    <p><strong>Contact:</strong><a href="mailto:bekisapauline@gmail.com"> bekisapauline@gmail.com</a> | <a href="tel:+256 778 280070">+256 778 280070</a></p>
                </article>

                <article class="member">
                    <h3>KADABARA EMMANUEL MUNDUKU    </h3>
                    <p><strong>Program:</strong> Bachelors Degree in Information Systems</p>
                    <p><strong>Role:</strong> Documentation, Deployment, and Support</p>
                    <p><strong>Contact:</strong><a href="mailto:kadabaraemmaneul@gmail.com"> kadabaraemmanuel@gmail.com</a> | <a href="tel:+256 764 598571">+256 764 598571</a></p>
                </article>
            </div>

            <div class="actions">
                <a href="index.php">Back to Home</a>
            </div>
        </section>
    </main>
    
    <footer>
        <p>CERTIFIED DEVELOPERS</p>
        &copy; <?php echo date("Y"); ?> Kyambogo University BITC Final Year Project Team
    </footer>
    <script src="../assets/js/theme.js" defer></script>
</body>
</html>
