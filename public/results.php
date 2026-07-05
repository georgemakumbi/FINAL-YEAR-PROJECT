<?php
/**
 * =============================================================================
 * RESULTS PAGE — With Live Updates & Animated Progress Bars (Phase 4)
 * =============================================================================
 *   1. Auto-refresh results every 10 seconds (using Fetch API)
 *   2. Animated progress bars with smooth transitions
 *   3. Voter turnout statistics card
 *   4. Winner highlighting with crown icon
 *   5. "Last updated" timestamp
 *
 * =============================================================================
 */
require_once '../bootstrap.php';
require_once VIEWS_COMPONENTS . '/includes/results_publish.php';

// ─── Require Login ──────────────────────────────────────────────────────────
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

// ─── Check Voting Status ────────────────────────────────────────────────────
$student_id = $_SESSION['student_id'];
$has_voted = Student::hasVoted($conn, $student_id);
$_SESSION['has_voted'] = $has_voted ? 1 : 0;

if (!$has_voted) {
    header("Location: voting.php");
    exit();
}

// ─── Get Initial Results ────────────────────────────────────────────────────
$results_published = results_are_published();
$results = [];
$stats = [];
if ($results_published) {
    $results = Candidate::getResults($conn);
    $stats = [
        'total_students' => Student::countAll($conn),
        'total_voted'    => Student::countVoted($conn),
        'turnout'        => Student::getVoterTurnout($conn),
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results — Kyambogo University</title>
    <meta name="description" content="Live election results for Kyambogo University student elections">
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
        <?php include ASSETS_CSS . '/results.css'; ?>

        /* ── Voter Statistics Card ──────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: var(--surface, #fff);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow, 0 2px 8px rgba(0,0,0,0.1));
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--brand-primary, #1a5632);
            line-height: 1;
        }
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted, #888);
            margin-top: 5px;
        }

        /* ── Animated Progress Bars ─────────────────────────────── */
        .result-bar-container {
            background: var(--border, #e9ecef);
            border-radius: 12px;
            height: 28px;
            overflow: hidden;
            position: relative;
            margin: 4px 0;
        }
        .result-bar {
            height: 100%;
            border-radius: 12px;
            background: linear-gradient(90deg, var(--brand-primary, #1a5632), #28a745);
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            min-width: 40px;
            transition: width 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .winner-row .result-bar {
            background: linear-gradient(90deg, #ffc107, #ff9800);
        }

        /* ── Winner Badge ────────────────────────────────────────── */
        .winner-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 8px;
            vertical-align: middle;
        }

        /* ── Live Update Indicator ───────────────────────────────── */
        .live-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
            font-size: 0.85rem;
            color: var(--text-muted, #888);
        }
        .live-dot {
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        /* ── Result Cards ────────────────────────────────────────── */
        .result-position-card {
            background: var(--surface, #fff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow, 0 2px 8px rgba(0,0,0,0.1));
        }
        .result-position-card h2 {
            color: var(--brand-primary, #1a5632);
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border, #eee);
        }
        .result-candidate-row {
            display: flex;
            align-items: center;
            padding: 10px 0;
            gap: 12px;
        }
        .result-candidate-row .rank {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--border, #eee);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-muted, #888);
            flex-shrink: 0;
        }
        .winner-row .rank {
            background: #ffc107;
            color: #333;
        }
        .result-candidate-info { flex: 1; min-width: 0; }
        .result-candidate-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text, #333);
        }
        .result-candidate-faculty {
            font-size: 0.8rem;
            color: var(--text-muted, #888);
        }
        .result-votes {
            text-align: right;
            flex-shrink: 0;
            min-width: 60px;
        }
        .result-votes-count {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text, #333);
        }
        .result-votes-label {
            font-size: 0.75rem;
            color: var(--text-muted, #888);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="Kyambogo University Logo">
            <div class="university-name">KYAMBOGO UNIVERSITY ONLINE VOTING SYSTEM</div>
        </div>
        <div class="user-info">
            Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>
            <form action="logout.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>
    
    <div class="container">
        <h1>Election Results</h1>

        <div class="vote-message voted">
            ✅ Thank you for participating! Your vote has been recorded.
            <?php if (isset($_SESSION['latest_receipt_token'])): ?>
                <br>
                <span style="font-size: 0.9em; font-weight: normal; margin-top: 10px; display: inline-block;">
                    Your anonymous ballot verification token: <strong><code><?php echo htmlspecialchars($_SESSION['latest_receipt_token']); ?></code></strong>
                    <br>
                    <small style="opacity: 0.85;">Save this token to verify your vote anonymously on the audit board.</small>
                </span>
            <?php endif; ?>
        </div>

        <?php if (!$results_published): ?>
            <div class="vote-message not-voted">
                📊 Results are not yet published. Please check back later.
            </div>
        <?php else: ?>

            <!-- Live Update Indicator -->
            <div class="live-indicator">
                <span class="live-dot"></span>
                <span>Live results — Last updated: <span id="lastUpdated"><?php echo date('H:i:s'); ?></span></span>
            </div>

            <!-- Voter Turnout Statistics -->
            <div class="stats-grid" id="statsGrid">
                <div class="stat-card">
                    <div class="stat-value" id="statTotalStudents"><?php echo number_format($stats['total_students']); ?></div>
                    <div class="stat-label">Registered Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="statTotalVoted"><?php echo number_format($stats['total_voted']); ?></div>
                    <div class="stat-label">Votes Cast</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="statTurnout"><?php echo $stats['turnout']; ?>%</div>
                    <div class="stat-label">Voter Turnout</div>
                </div>
            </div>

            <!-- Results Container (filled by PHP initially, updated by JS) -->
            <div id="resultsContainer">
                <?php
                $current_position = "";
                $winner_shown = [];
                $rank = 0;
                
                foreach ($results as $row):
                    if ($row['position'] !== $current_position):
                        if ($current_position !== "") echo '</div>'; // Close previous card
                        $current_position = $row['position'];
                        $rank = 0;
                        echo '<div class="result-position-card">';
                        echo '<h2>' . htmlspecialchars($current_position) . '</h2>';
                    endif;
                    
                    $rank++;
                    $is_winner = !isset($winner_shown[$current_position]);
                    if ($is_winner) $winner_shown[$current_position] = true;
                    
                    $pct = round((float)($row['percentage'] ?? 0), 1);
                ?>
                    <div class="result-candidate-row <?php echo $is_winner ? 'winner-row' : ''; ?>">
                        <div class="rank"><?php echo $is_winner ? '👑' : $rank; ?></div>
                        <div class="result-candidate-info">
                            <div class="result-candidate-name">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                <?php if ($is_winner): ?><span class="winner-badge">LEADING</span><?php endif; ?>
                            </div>
                            <div class="result-candidate-faculty"><?php echo htmlspecialchars($row['faculty']); ?></div>
                            <div class="result-bar-container">
                                <div class="result-bar" style="width: <?php echo max($pct, 3); ?>%;">
                                    <?php echo $pct; ?>%
                                </div>
                            </div>
                        </div>
                        <div class="result-votes">
                            <div class="result-votes-count"><?php echo number_format((int)$row['votes']); ?></div>
                            <div class="result-votes-label">votes</div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if ($current_position !== "") echo '</div>'; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Feedback Section -->
    <div class="feedback" style="text-align: center; margin: 30px;">
        <a href="feedback.php">
            <button type="button" class="feedback-btn">📝 Share your feedback</button>
        </a>
    </div>

    <footer style="text-align: center; background-color: var(--footer-bg); color: var(--footer-text); padding: 20px; margin-top: 30px;">
        <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
        <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
    </footer>

    <script src="../assets/js/theme.js" defer></script>
    
    <?php if ($results_published): ?>
    <script>
    /**
     * ═════════════════════════════════════════════════════════════════════
     * LIVE RESULTS — Auto-Refresh Using Fetch API (AJAX)
     * ═════════════════════════════════════════════════════════════════════
     * 
     * HOW THIS WORKS:
     *   1. Every 10 seconds, JavaScript sends a request to api/results.php
     *   2. The API returns fresh data as JSON
     *   3. JavaScript updates the page WITHOUT a full reload
     *   4. Progress bars animate smoothly to new values
     *
     * WHY 10 SECONDS?
     *   - Fast enough to feel "live"
     *   - Slow enough to not overload the server
     *   - In a real production system, you'd use WebSockets for true real-time
     */

    async function fetchResults() {
        try {
            const response = await fetch('api/results.php');
            
            if (!response.ok) return; // Don't update on error
            
            const data = await response.json();
            
            if (!data.success || !data.results_published) return;
            
            // Update statistics
            if (data.statistics) {
                document.getElementById('statTotalStudents').textContent = 
                    data.statistics.total_students.toLocaleString();
                document.getElementById('statTotalVoted').textContent = 
                    data.statistics.total_voted.toLocaleString();
                document.getElementById('statTurnout').textContent = 
                    data.statistics.turnout + '%';
            }
            
            // Update timestamp
            document.getElementById('lastUpdated').textContent = 
                new Date().toLocaleTimeString();
                
        } catch (error) {
            // Silently fail — don't disrupt the user experience
            console.log('Results refresh failed:', error.message);
        }
    }

    // Refresh every 10 seconds
    setInterval(fetchResults, 10000);
    </script>
    <?php endif; ?>
    <script src="/finalyearproject/assets/js/pwa.js" defer></script>
</body>
</html>
