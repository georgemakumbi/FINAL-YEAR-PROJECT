<?php
    date_default_timezone_set('Africa/Kampala');
    
    require_once '../bootstrap.php';
    
    // Fetch candidates
    $candidates_summary = $conn->query("SELECT first_name, last_name, position, status, image_path FROM candidates ORDER BY position, first_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Candidates - Kyambogo Guild</title>
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        <?php include ASSETS_CSS . '/index.css'; ?>
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="KyU Logo">
        </div>
        <h1>Kyambogo Guild Online Voting Portal</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Knowledge And Skills For Service</p>
    </header>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="index.php" style="text-decoration: none; padding: 10px 15px; background: var(--surface-2); border: 1px solid var(--border); border-radius: 5px; color: var(--text); font-weight: bold;">
                &larr; Back to Home
            </a>
        </div>
        
        <section class="card">
            <h2 style="color: var(--primary); margin-bottom: 15px;">Application Status for Candidates</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                <?php if ($candidates_summary && $candidates_summary->num_rows > 0): ?>
                    <?php while($cand = $candidates_summary->fetch_assoc()): ?>
                        <div style="background: var(--surface-2); padding: 15px; border-radius: 8px; text-align: center; border: 1px solid var(--border);">
<?php
                                $img = $cand['image_path'] ?? '';
                                $src = $img ? htmlspecialchars($img) : '../assets/images/placeholder.png';
                            ?>
                            <img src="<?php echo $src; ?>" alt="Candidate Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin: 0 auto 10px; display: block; border: 2px solid var(--primary);">
                            <h4 style="margin: 0; font-size: 1.1em; color: var(--text);"><?php echo htmlspecialchars($cand['first_name'] . ' ' . $cand['last_name']); ?></h4>
                            <p style="margin: 5px 0; color: var(--text-muted); font-size: 0.9em;"><?php echo htmlspecialchars($cand['position']); ?></p>
                            <?php 
                                $status_color = ($cand['status'] == 'verified') ? '#27ae60' : (($cand['status'] == 'rejected') ? '#e74c3c' : '#f39c12'); 
                            ?>
                            <span style="background: <?php echo $status_color; ?>; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold;">
                                <?php echo ucfirst(htmlspecialchars($cand['status'] ?? 'pending')); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">No candidates have applied yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <footer>
        <div class="quick-links">
            <p>Technical Support: <a href="mailto:admin@kyu.ac.ug">admin@kyu.ac.ug</a> | <a href="tel:+256 747077274">+256 747 077 274</a></p>
        </div>
        <p class="copyright">&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 10px;">Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
    </footer>

    <script src="../assets/js/theme.js" defer></script>
</body>
</html>
