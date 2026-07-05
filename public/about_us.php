<?php require_once '../bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Kyambogo Guild Online Voting Portal</title>
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>
    <!-- PWA -->
    <link rel="manifest" href="/finalyearproject/public/manifest.json">
    <meta name="theme-color" content="#1a237e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KU Votes">
    <link rel="apple-touch-icon" href="/finalyearproject/assets/images/icons/icon-180.png">" type="image/png">
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: var(--app-bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }

        .header {
            background: var(--gradient-primary, linear-gradient(135deg, #003366, #004d99));
            color: #fff;
            padding: 60px 20px;
            text-align: center;
            box-shadow: var(--shadow-lg, 0 12px 40px rgba(0,0,0,0.12));
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(255,193,7,0.1), transparent);
            pointer-events: none;
        }
        .header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        .header p {
            font-size: 1.1rem;
            font-weight: 500;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .card {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: var(--shadow, 0 4px 12px rgba(0, 0, 0, 0.08));
            margin-bottom: 32px;
            padding: 40px;
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: var(--shadow-lg, 0 12px 40px rgba(0,0,0,0.12));
            transform: translateY(-2px);
        }
        .card h2 {
            color: var(--brand-indigo, #6366f1);
            margin-bottom: 20px;
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .card > p {
            font-size: 1rem;
            line-height: 1.8;
            color: var(--text-muted, #6b7280);
            margin-bottom: 28px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .member {
            border: 2px solid var(--border);
            border-left: 5px solid var(--brand-orange, #f59e0b);
            border-radius: 10px;
            padding: 20px;
            background: var(--surface-2);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-orange, #f59e0b), var(--brand-indigo, #6366f1));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        .member:hover {
            border-color: var(--brand-indigo, #6366f1);
            background: var(--surface);
            box-shadow: var(--shadow, 0 4px 12px rgba(0, 0, 0, 0.08));
            transform: translateY(-4px);
        }
        .member:hover::before { transform: scaleX(1); }
        .member h3 {
            color: var(--text);
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
        .member p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            line-height: 1.6;
        }
        .member p:last-child { margin-bottom: 0; }
        .member strong { color: var(--text); font-weight: 600; }
        .member a {
            color: var(--brand-indigo, #6366f1);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .member a:hover { color: var(--brand-orange, #f59e0b); text-decoration: underline; }

        .actions {
            margin-top: 32px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: var(--gradient-primary, linear-gradient(135deg, #003366, #004d99));
            color: #fff;
            padding: 13px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.15);
            letter-spacing: 0.3px;
        }
        .actions a:hover {
            opacity: 0.92;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 51, 102, 0.25);
        }

        footer {
            margin-top: 80px;
            padding: 50px 20px;
            background: var(--footer-bg, #0a1628);
            color: var(--footer-text, #d1d5db);
            text-align: center;
            border-top: 3px solid var(--brand-orange, #f59e0b);
        }
        footer p { font-size: 0.95rem; margin-bottom: 8px; }
        footer p:first-child {
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--brand-orange, #f59e0b);
        }

        @media (max-width: 768px) {
            .header { padding: 40px 20px; }
            .header h1 { font-size: 1.8rem; }
            .header p { font-size: 1rem; }
            .container { margin: 30px auto; }
            .card { padding: 24px; }
            .card h2 { font-size: 1.4rem; margin-bottom: 16px; }
            .team-grid { gap: 16px; }
            .member { padding: 16px; }
            .actions { flex-direction: column; }
            .actions a { width: 100%; }
            footer { margin-top: 60px; padding: 40px 20px; }
        }

        @media (max-width: 480px) {
            .header { padding: 30px 16px; }
            .header h1 { font-size: 1.5rem; }
            .header p { font-size: 0.95rem; }
            .card { padding: 20px; }
            .card h2 { font-size: 1.2rem; }
            .team-grid { grid-template-columns: 1fr; }
            .member { padding: 14px; }
            .member h3 { font-size: 1rem; }
            .member p { font-size: 0.85rem; }
        }

        body.dark .card { background: var(--surface); }
        body.dark .member { background: var(--surface-2); }
    </style>
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
                    <p><strong>Contact:</strong> <a href="mailto:makumbigeorge5@gmail.com">makumbigeorge5@gmail.com</a> | <a href="tel:+256 747077274">+256 747077274</a></p>
                </article>

                <article class="member">
                    <h3>AFUNGA RONALD MICHEAL</h3>
                    <p><strong>Program:</strong> Bachelors degree in Information Technology</p>
                    <p><strong>Role:</strong> Frontend Design and UI Development</p>
                    <p><strong>Contact:</strong> <a href="mailto:afungaronald@gmail.com">afungaronald@gmail.com</a> | <a href="tel:+256 764 511417">+256 764 511417</a></p>
                </article>

                <article class="member">
                    <h3>BEKISA PAULYNE</h3>
                    <p><strong>Program:</strong> Bachelors degree in Information Technology</p>
                    <p><strong>Role:</strong> Authentication, Security, and Testing</p>
                    <p><strong>Contact:</strong> <a href="mailto:bekisapauline@gmail.com">bekisapauline@gmail.com</a> | <a href="tel:+256 778 280070">+256 778 280070</a></p>
                </article>

                <article class="member">
                    <h3>KADABARA EMMANUEL MUNDUKU</h3>
                    <p><strong>Program:</strong> Bachelors Degree in Information Systems</p>
                    <p><strong>Role:</strong> Documentation, Deployment, and Support</p>
                    <p><strong>Contact:</strong> <a href="mailto:kadabaraemmaneul@gmail.com">kadabaraemmanuel@gmail.com</a> | <a href="tel:+256 764 598571">+256 764 598571</a></p>
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
    <script src="/finalyearproject/assets/js/pwa.js" defer></script>
</body>
</html>
