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
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--app-bg);
            color: var(--text);
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 16px 30px;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .report-card {
            background: var(--surface);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 24px;
        }

        h1 {
            margin: 0 0 5px;
            color: var(--text);
        }

        h2 {
            margin: 26px 0 12px;
            color: var(--text);
        }

        .subtitle {
            margin: 0 0 18px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 10px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px;
        }

        .stats-grid {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .stat {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px;
        }

        .stat .label {
            font-size: 13px;
            color: var(--text-muted);
        }

        .stat .value {
            font-size: 24px;
            font-weight: 700;
            margin-top: 6px;
        }

        .winner-box {
            margin-top: 16px;
            padding: 12px 14px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            color: #065f46;
        }

        .tie-note {
            font-style: italic;
            margin-left: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 1px solid var(--border);
            padding: 10px;
            text-align: left;
        }

        th {
            background: var(--surface-2);
            color: var(--text);
        }

        .empty {
            text-align: center;
            color: var(--text-muted);
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-scheduled {
            background: #fef3c7;
            color: #92400e;
        }

        .btn {
            border: none;
            border-radius: 6px;
            padding: 10px 12px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            display: inline-block;
        }

        .btn-primary {
            background: var(--link);
        }

        .btn-secondary {
            background: #475569;
        }

        .btn-success {
            background: #16a34a;
        }

        footer {
            margin-top: 50px;
            padding: 40px 20px;
            background: var(--footer-bg);
            color: var(--footer-text);
            text-align: center;
        }

        @media (max-width: 700px) {
            .toolbar {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .container {
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .report-card {
                box-shadow: none;
                border: 0;
                padding: 0;
            }
        }

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
