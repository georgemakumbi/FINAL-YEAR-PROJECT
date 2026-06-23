<?php
/**
 * =============================================================================
 * HOMEPAGE — Kyambogo University Online Voting Portal (Phase 5 Redesign)
 * =============================================================================
 */
require_once '../bootstrap.php';

$deadline_str = Election::getEndDate($conn) ?? '+1 day';
$deadline  = strtotime($deadline_str);
$now       = time();
$remaining = $deadline - $now;
$expired   = $remaining <= 0;

// Get live stats for the homepage
$total_students = Student::countAll($conn);
$total_voted    = Student::countVoted($conn);
$turnout        = Student::getVoterTurnout($conn);
$total_candidates = Candidate::countByStatus($conn, 'verified');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyambogo University — Online Voting Portal</title>
    <meta name="description" content="Official online voting portal for Kyambogo University student guild elections. Cast your vote securely.">
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <style>
        <?php include ASSETS_CSS . '/theme.css'; ?>

        /* ═══════════════════════════════════════════════════════════════ */
        /* HERO SECTION                                                    */
        /* ═══════════════════════════════════════════════════════════════ */
        .hero {
            background: var(--gradient-hero);
            color: #fff;
            padding: 60px 20px 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 70%, rgba(255,193,7,0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 30%, rgba(255,255,255,0.05) 0%, transparent 50%);
            animation: subtleFloat 20s ease-in-out infinite;
        }
        @keyframes subtleFloat {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-2%, 1%); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
            margin: 0 auto;
        }

        .hero-logo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .hero-logo:hover { transform: scale(1.05); }

        .hero h1 {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .hero-motto {
            font-size: 1rem;
            opacity: 0.8;
            font-weight: 400;
            font-style: italic;
            margin-bottom: 25px;
        }

        /* ── Countdown ─────────────────────────────────────────────── */
        .countdown-wrapper {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: var(--radius-lg);
            padding: 20px 30px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .countdown-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.7;
            margin-bottom: 8px;
        }
        .countdown-digits {
            display: flex;
            gap: 6px;
            justify-content: center;
            align-items: center;
        }
        .countdown-unit {
            text-align: center;
        }
        .countdown-number {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            min-width: 56px;
            background: rgba(255,255,255,0.12);
            border-radius: var(--radius);
            padding: 10px 6px;
            display: block;
        }
        .countdown-text {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            margin-top: 4px;
        }
        .countdown-sep {
            font-size: 1.8rem;
            font-weight: 300;
            opacity: 0.4;
            padding-bottom: 18px;
        }

        /* ═══════════════════════════════════════════════════════════════ */
        /* MAIN CONTENT                                                    */
        /* ═══════════════════════════════════════════════════════════════ */
        .main {
            max-width: 960px;
            margin: -30px auto 0;
            padding: 0 20px 40px;
            position: relative;
            z-index: 2;
        }

        /* ── Stats Grid ────────────────────────────────────────────── */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            margin-bottom: 30px;
        }
        .stat-item {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 18px 12px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: transform 0.2s;
        }
        .stat-item:hover { transform: translateY(-3px); }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--brand-primary);
            line-height: 1;
        }
        .stat-text {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

        /* ── Action Card ───────────────────────────────────────────── */
        .action-card {
            background: var(--surface);
            border-radius: var(--radius-xl);
            padding: 40px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-accent);
        }
        .action-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
        }
        .action-card p {
            color: var(--text-muted);
            margin-bottom: 24px;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-vote {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 40px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            background: var(--gradient-primary);
            border-radius: var(--radius-full);
            text-decoration: none;
            box-shadow: var(--shadow), var(--shadow-glow);
            transition: all 0.3s ease;
        }
        .btn-vote:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: var(--shadow-lg), 0 0 30px rgba(37, 99, 235, 0.25);
            text-decoration: none;
        }
        .btn-vote:active { transform: translateY(0) scale(1); }

        .election-closed {
            background: var(--brand-danger-light);
            border: 2px solid var(--brand-danger);
            color: var(--brand-danger);
            border-radius: var(--radius-lg);
            padding: 20px;
            text-align: center;
            margin-bottom: 24px;
        }

        /* ── Info Cards Grid ───────────────────────────────────────── */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .info-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        .info-card-icon {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        .info-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }
        .info-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .steps-list {
            list-style: none;
            padding: 0;
            counter-reset: step;
        }
        .steps-list li {
            counter-increment: step;
            padding: 6px 0 6px 32px;
            position: relative;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .steps-list li::before {
            content: counter(step);
            position: absolute;
            left: 0;
            top: 6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--brand-primary-light);
            color: var(--brand-primary);
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Footer ────────────────────────────────────────────────── */
        .site-footer {
            background: var(--footer-bg);
            color: var(--footer-text);
            padding: 30px 20px;
            text-align: center;
        }
        .footer-links {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .footer-links a {
            color: var(--footer-text);
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .footer-links a:hover { color: var(--brand-accent); text-decoration: none; }
        .footer-copy {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        /* ═══════════════════════════════════════════════════════════════ */
        /* RESPONSIVE                                                      */
        /* ═══════════════════════════════════════════════════════════════ */
        @media (max-width: 768px) {
            .hero { padding: 40px 16px 40px; }
            .hero h1 { font-size: 1.6rem; }
            .hero-logo { width: 70px; height: 70px; }
            .countdown-number { font-size: 1.6rem; min-width: 44px; }
            .countdown-sep { font-size: 1.4rem; }
            .main { padding: 0 16px 30px; }
            .action-card { padding: 28px 20px; }
            .btn-vote { padding: 14px 28px; font-size: 1rem; }
            .info-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .hero h1 { font-size: 1.3rem; }
            .stats-bar { grid-template-columns: repeat(2, 1fr); }
            .countdown-number { font-size: 1.3rem; min-width: 38px; padding: 8px 4px; }
        }
    </style>
    <script>
        window.va = window.va || function () { (window.vaq = window.vaq || []).push(arguments); };
    </script>
    <script defer src="/_vercel/insights/script.js"></script>
</head>
<body>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- HERO SECTION                                                       -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <section class="hero">
        <div class="hero-content">
            <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="Kyambogo University Logo" class="hero-logo">
            <h1>KYAMBOGO UNIVERSITY</h1>
            <p class="hero-motto">Knowledge And Skills For Service</p>

            <!-- Countdown Timer -->
            <div class="countdown-wrapper">
                <div class="countdown-label">⏱ Time Remaining to Vote</div>
                <div class="countdown-digits" id="countdown">
                    <div class="countdown-unit">
                        <span class="countdown-number" id="cd-days">--</span>
                        <span class="countdown-text">Days</span>
                    </div>
                    <span class="countdown-sep">:</span>
                    <div class="countdown-unit">
                        <span class="countdown-number" id="cd-hours">--</span>
                        <span class="countdown-text">Hours</span>
                    </div>
                    <span class="countdown-sep">:</span>
                    <div class="countdown-unit">
                        <span class="countdown-number" id="cd-mins">--</span>
                        <span class="countdown-text">Minutes</span>
                    </div>
                    <span class="countdown-sep">:</span>
                    <div class="countdown-unit">
                        <span class="countdown-number" id="cd-secs">--</span>
                        <span class="countdown-text">Seconds</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- MAIN CONTENT                                                       -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <main class="main">

        <!-- Live Statistics -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($total_students); ?></div>
                <div class="stat-text">Registered Voters</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($total_voted); ?></div>
                <div class="stat-text">Votes Cast</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $turnout; ?>%</div>
                <div class="stat-text">Voter Turnout</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_candidates; ?></div>
                <div class="stat-text">Candidates</div>
            </div>
        </div>

        <!-- Call to Action -->
        <?php if ($expired): ?>
            <div class="election-closed">
                <h3>⚠️ Elections Closed</h3>
                <p>No active elections available. Contact election officials for updates.</p>
            </div>
        <?php else: ?>
            <div class="action-card">
                <h2>🗳️ Student Guild Elections</h2>
                <p>Cast your vote securely in the Kyambogo University student guild elections. Your voice matters.</p>
                <a href="login.php" class="btn-vote">
                    Login & Vote →
                </a>
            </div>
        <?php endif; ?>

        <!-- Info Cards -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">📋</div>
                <h3>How to Vote</h3>
                <ol class="steps-list">
                    <li>Login with your student ID & password</li>
                    <li>Review candidate profiles and manifestos</li>
                    <li>Select one candidate per position</li>
                    <li>Confirm and submit your ballot</li>
                    <li>View live results after voting</li>
                </ol>
            </div>

            <div class="info-card">
                <div class="info-card-icon">🔒</div>
                <h3>Security & Privacy</h3>
                <p>Your vote is protected by multiple security layers including encrypted sessions, CSRF tokens, and row-level database locking. No one — not even administrators — can see who you voted for.</p>
            </div>

            <div class="info-card">
                <div class="info-card-icon">👥</div>
                <h3>View Candidates</h3>
                <p>Review verified candidates and their manifestos before casting your vote.</p>
                <a href="view_candidates.php" class="btn btn-outline" style="margin-top: 12px;">View All Candidates →</a>
            </div>
        </div>
    </main>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- FOOTER                                                             -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <footer class="site-footer">
        <div class="footer-links">
            <a href="https://kyu.ac.ug/" target="_blank">KYU Home</a>
            <a href="https://gradsch.kyu.ac.ug/privacy-policy/" target="_blank">Privacy Policy</a>
            <a href="about_us.php">About Us</a>
            <a href="mailto:admin@kyu.ac.ug">Technical Support</a>
        </div>
        <p class="footer-copy">&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        <p style="font-size: 0.75rem; opacity: 0.5; margin-top: 4px;">Designed by BITC Students — Class of 2023</p>
    </footer>

    <script src="../assets/js/theme.js" defer></script>
    <script>
    /* ═══════════════════════════════════════════════════════════════════════ */
    /* COUNTDOWN TIMER — Individual digit units                               */
    /* ═══════════════════════════════════════════════════════════════════════ */
    const deadline = <?= $deadline * 1000 ?>;
    const cdDays  = document.getElementById('cd-days');
    const cdHours = document.getElementById('cd-hours');
    const cdMins  = document.getElementById('cd-mins');
    const cdSecs  = document.getElementById('cd-secs');

    function updateCountdown() {
        const now = Date.now();
        const distance = deadline - now;

        if (distance <= 0) {
            cdDays.textContent = '0';
            cdHours.textContent = '0';
            cdMins.textContent = '0';
            cdSecs.textContent = '0';
            return;
        }

        cdDays.textContent  = Math.floor(distance / (1000 * 60 * 60 * 24));
        cdHours.textContent = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        cdMins.textContent  = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        cdSecs.textContent  = Math.floor((distance % (1000 * 60)) / 1000);
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    /* Admin shortcut: Shift+A */
    document.addEventListener('keydown', function(e) {
        if (e.shiftKey && e.key.toLowerCase() === 'a') {
            e.preventDefault();
            window.location.href = 'admin_login.php';
        }
    });
    </script>
</body>
</html>
