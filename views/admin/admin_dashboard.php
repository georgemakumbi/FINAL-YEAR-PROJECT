<?php
/**
 * =============================================================================
 * ADMIN DASHBOARD — Refactored with Models & Live Analytics (Phase 6)
 * =============================================================================
 * 
 * WHAT CHANGED:
 *   1. Raw SQL queries → Model class methods (Student, Candidate, Vote, Election)
 *   2. Results section → Uses Candidate::getResults() instead of raw queries
 *   3. Dashboard stats → Powered by Models with live-refresh capability
 *   4. Cleaner code → No SQL in the template layer
 *   5. Enhanced Chart.js with per-position breakdown
 *
 * =============================================================================
 */

require_once dirname(dirname(__FILE__)) . '/../bootstrap.php';

// ─── Require Admin Login ─────────────────────────────────────────────────────
require_admin_login();
ensure_csrf_token();

// ─── Check Permissions ───────────────────────────────────────────────────────
$is_super_admin = isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';

// ─── Results Status ──────────────────────────────────────────────────────────
$results_status = get_results_publish_status();
$results_published = $results_status === 'published';

// ─── Flash Messages ──────────────────────────────────────────────────────────
$message = '';
$message_type = '';
if (isset($_GET['success']) && $_GET['success'] !== '') {
    $message = $_GET['success'];
    $message_type = 'success';
} elseif (isset($_GET['error']) && $_GET['error'] !== '') {
    $message = $_GET['error'];
    $message_type = 'error';
}

// ═════════════════════════════════════════════════════════════════════════════
// DASHBOARD STATISTICS — Using Model classes instead of raw SQL!
// ═════════════════════════════════════════════════════════════════════════════
// BEFORE (old way):
//   $total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
//   $total_candidates = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];
//   ...
//
// AFTER (with Models):
$total_students   = Student::countAll($conn);
$total_voted      = Student::countVoted($conn);
$total_candidates = Candidate::countByStatus($conn, 'verified');
$total_votes      = Vote::countAll($conn);
$turnout          = Student::getVoterTurnout($conn);

// Get per-position vote statistics for the chart
$votes_by_position = Vote::getStatsByPosition($conn);

// Get election counts for the dashboard
$election_counts = Election::countByStatus($conn);
$active_elections = $election_counts['active'] ?? 0;

// ═════════════════════════════════════════════════════════════════════════════
// ROUTING — Determine which module to display
// ═════════════════════════════════════════════════════════════════════════════
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
$valid_sections = ['dashboard', 'elections', 'candidates', 'students', 'results', 'feedback', 'audit', 'settings'];

if (!in_array($section, $valid_sections)) {
    $section = 'dashboard';
}

// ─── Load Module Logic Files ─────────────────────────────────────────────────
switch ($section) {
    case 'elections':
        include_once VIEWS_COMPONENTS . '/includes/modules/elections/elections.logic.php';
        break;
    case 'candidates':
        include_once VIEWS_COMPONENTS . '/includes/modules/candidates/candidates.logic.php';
        break;
    case 'students':
        include_once VIEWS_COMPONENTS . '/includes/modules/students/students.logic.php';
        break;
    case 'audit':
        include_once VIEWS_COMPONENTS . '/includes/modules/audit_logs/audit_logs.logic.php';
        break;
    case 'feedback':
        include_once VIEWS_COMPONENTS . '/includes/modules/feedback/feedback.logic.php';
        break;
    case 'settings':
        include_once VIEWS_COMPONENTS . '/includes/modules/settings/settings.logic.php';
        break;
}

// ─── Results Section: Now uses Candidate Model ───────────────────────────────
$results_data = [];
$positions_list = [];
if ($section === 'results') {
    $results_data = Candidate::getResults($conn);
    $positions_list = Candidate::getPositions($conn);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Kyambogo Voting System</title>
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        <?php readfile(ASSETS_CSS . '/theme.css'); ?>
        <?php readfile(ASSETS_CSS . '/admin_dashboard.css'); ?>

        /* ── Phase 6: Enhanced Dashboard Cards ─────────────────────── */
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
        }
        .stat-card:nth-child(1)::after { background: linear-gradient(90deg, #3498db, #2980b9); }
        .stat-card:nth-child(2)::after { background: linear-gradient(90deg, #27ae60, #2ecc71); }
        .stat-card:nth-child(3)::after { background: linear-gradient(90deg, #f39c12, #e67e22); }
        .stat-card:nth-child(4)::after { background: linear-gradient(90deg, #9b59b6, #8e44ad); }
        .stat-card:nth-child(5)::after { background: linear-gradient(90deg, #e74c3c, #c0392b); }

        /* ── Enhanced Chart Area ───────────────────────────────────── */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        @media (max-width: 900px) {
            .charts-grid { grid-template-columns: 1fr; }
        }

        /* ── Results Cards (Phase 6) ───────────────────────────────── */
        .result-position-block {
            background: var(--surface);
            border-radius: var(--radius-lg, 16px);
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }
        .result-position-block h3 {
            color: var(--brand-primary);
            font-size: 1.1rem;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border);
        }
        .result-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 0;
        }
        .result-rank {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
            background: var(--border);
            color: var(--text-muted);
        }
        .result-row:first-child .result-rank {
            background: #ffc107;
            color: #333;
        }
        .result-info { flex: 1; min-width: 0; }
        .result-name { font-weight: 600; font-size: 0.95rem; }
        .result-faculty { font-size: 0.8rem; color: var(--text-muted); }
        .result-bar-track {
            background: var(--border);
            border-radius: 8px;
            height: 22px;
            overflow: hidden;
            margin-top: 4px;
        }
        .result-bar-fill {
            height: 100%;
            border-radius: 8px;
            background: linear-gradient(90deg, var(--brand-primary), #2980b9);
            display: flex;
            align-items: center;
            padding: 0 8px;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            min-width: 30px;
            transition: width 0.6s ease;
        }
        .result-row:first-child .result-bar-fill {
            background: linear-gradient(90deg, #ffc107, #ff9800);
            color: #333;
        }
        .result-votes {
            text-align: right;
            flex-shrink: 0;
            min-width: 50px;
        }
        .result-votes-num { font-weight: 700; font-size: 1.1rem; }
        .result-votes-label { font-size: 0.7rem; color: var(--text-muted); }

        /* ── Live Indicator ────────────────────────────────────────── */
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--text-muted);
            float: right;
        }
        .live-badge .dot {
            width: 7px;
            height: 7px;
            background: #27ae60;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body>
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SIDEBAR                                                            -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>👤 Admin Panel</h2>
            <p>Voting System</p>
        </div>
        
        <ul class="nav-menu">
            <li><a href="admin_dashboard.php?section=dashboard" class="nav-item <?php echo $section === 'dashboard' ? 'active' : ''; ?>">📊 Dashboard</a></li>
            <li><a href="admin_dashboard.php?section=elections" class="nav-item <?php echo $section === 'elections' ? 'active' : ''; ?>">🗳️ Elections</a></li>
            <li><a href="admin_dashboard.php?section=candidates" class="nav-item <?php echo $section === 'candidates' ? 'active' : ''; ?>">👥 Candidates</a></li>
            <li><a href="admin_dashboard.php?section=students" class="nav-item <?php echo $section === 'students' ? 'active' : ''; ?>">🎓 Students</a></li>
            <li><a href="admin_dashboard.php?section=results" class="nav-item <?php echo $section === 'results' ? 'active' : ''; ?>">📈 Results</a></li>
            <li><a href="admin_dashboard.php?section=feedback" class="nav-item <?php echo $section === 'feedback' ? 'active' : ''; ?>">💬 Feedback</a></li>
            <li><a href="admin_dashboard.php?section=audit" class="nav-item <?php echo $section === 'audit' ? 'active' : ''; ?>">🔍 Audit Log</a></li>
            <li><a href="admin_dashboard.php?section=settings" class="nav-item <?php echo $section === 'settings' ? 'active' : ''; ?>">⚙️ Settings</a></li>
        </ul>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- MAIN CONTENT                                                       -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="main-content">
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo safe_output($message); ?>
            </div>
        <?php endif; ?>

        <!-- Top Bar -->
        <div class="top-bar">
            <h1><?php echo ucfirst($section === 'dashboard' ? 'Dashboard' : $section); ?></h1>
            <div class="top-bar-right">
                <span class="user-info">👤 <?php echo safe_output($_SESSION['admin_username']); ?></span>
                <a href="admin_logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- DASHBOARD SECTION                                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'dashboard'): ?>
        <div id="dashboard" class="section active">
            
            <!-- Statistics Cards — now powered by Models -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <div class="value" id="stat-students"><?php echo format_number($total_students); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Active Elections</h3>
                    <div class="value"><?php echo $active_elections; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Votes Cast</h3>
                    <div class="value" id="stat-votes"><?php echo format_number($total_votes); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Voter Turnout</h3>
                    <div class="value" id="stat-turnout"><?php echo $turnout; ?>%</div>
                </div>
                <div class="stat-card">
                    <h3>Candidates</h3>
                    <div class="value"><?php echo $total_candidates; ?></div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid">
                <div class="card">
                    <h2>📊 Votes by Position <span class="live-badge"><span class="dot"></span> Live</span></h2>
                    <div class="chart-container">
                        <canvas id="positionChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h2>📈 Overview Statistics</h2>
                    <div class="chart-container">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- ELECTIONS MODULE                                           -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'elections'): ?>
            <div id="elections" class="section active">
                <?php include_once VIEWS_COMPONENTS . '/includes/modules/elections/elections.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- CANDIDATES MODULE                                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'candidates'): ?>
            <div id="candidates" class="section active">
                <?php include_once VIEWS_COMPONENTS . '/includes/modules/candidates/candidates.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- STUDENTS MODULE                                            -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'students'): ?>
            <div id="students" class="section active">
                <?php include_once VIEWS_COMPONENTS . '/includes/modules/students/students.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- RESULTS SECTION — Redesigned with Models (Phase 6)         -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'results'): ?>
        <div id="results" class="section active">
            <div class="card">
                <h2>📈 Election Results <span class="live-badge"><span class="dot"></span> Live</span></h2>
                
                <?php if (empty($results_data)): ?>
                    <p style="text-align:center; color:var(--text-muted); padding:30px;">No election results available yet.</p>
                <?php else: ?>
                    <?php
                    // Group results by position using the Model data
                    $grouped_results = [];
                    foreach ($results_data as $row) {
                        $pos = $row['position'];
                        if (!isset($grouped_results[$pos])) {
                            $grouped_results[$pos] = [];
                        }
                        $grouped_results[$pos][] = $row;
                    }
                    
                    foreach ($grouped_results as $position => $candidates):
                    ?>
                        <div class="result-position-block">
                            <h3><?php echo safe_output($position); ?></h3>
                            <?php 
                            $rank = 0;
                            foreach ($candidates as $cand): 
                                $rank++;
                                $pct = round((float)($cand['percentage'] ?? 0), 1);
                            ?>
                                <div class="result-row">
                                    <div class="result-rank"><?php echo $rank === 1 ? '👑' : $rank; ?></div>
                                    <div class="result-info">
                                        <div class="result-name"><?php echo safe_output($cand['first_name'] . ' ' . $cand['last_name']); ?></div>
                                        <div class="result-faculty"><?php echo safe_output($cand['faculty']); ?></div>
                                        <div class="result-bar-track">
                                            <div class="result-bar-fill" style="width: <?php echo max($pct, 3); ?>%;">
                                                <?php echo $pct; ?>%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="result-votes">
                                        <div class="result-votes-num"><?php echo format_number((int)$cand['votes']); ?></div>
                                        <div class="result-votes-label">votes</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- AUDIT LOGS MODULE                                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'audit'): ?>
            <div id="audit" class="section active">
                <?php include_once VIEWS_COMPONENTS . '/includes/modules/audit_logs/audit_logs.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- FEEDBACK MODULE                                            -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'feedback'): ?>
            <div id="feedback" class="section active">
                <?php include_once VIEWS_COMPONENTS . '/includes/modules/feedback/feedback.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SETTINGS MODULE                                            -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <?php if ($section === 'settings'): ?>
            <div id="settings" class="section active">
                <?php include_once VIEWS_COMPONENTS . '/includes/modules/settings/settings.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <footer>
            <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
            <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        </footer>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- JAVASCRIPT — Charts & Live Refresh                                 -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <script>
    <?php if ($section === 'dashboard'): ?>
    
    // ─── Position Chart (Horizontal Bar) ─────────────────────────────────────
    // Shows votes per position — much more useful than the old overview chart!
    const posCtx = document.getElementById('positionChart');
    if (posCtx) {
        const posLabels = <?php echo json_encode(array_column($votes_by_position, 'position')); ?>;
        const posVotes = <?php echo json_encode(array_map('intval', array_column($votes_by_position, 'total_votes'))); ?>;
        
        new Chart(posCtx, {
            type: 'bar',
            data: {
                labels: posLabels,
                datasets: [{
                    label: 'Votes',
                    data: posVotes,
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(39, 174, 96, 0.7)',
                        'rgba(243, 156, 18, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(231, 76, 60, 0.7)',
                        'rgba(26, 188, 156, 0.7)',
                        'rgba(241, 196, 15, 0.7)',
                    ],
                    borderColor: [
                        'rgba(52, 152, 219, 1)',
                        'rgba(39, 174, 96, 1)',
                        'rgba(243, 156, 18, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(231, 76, 60, 1)',
                        'rgba(26, 188, 156, 1)',
                        'rgba(241, 196, 15, 1)',
                    ],
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, grid: { display: false } } }
            }
        });
    }
    
    // ─── Overview Doughnut Chart ─────────────────────────────────────────────
    const overCtx = document.getElementById('overviewChart');
    if (overCtx) {
        new Chart(overCtx, {
            type: 'doughnut',
            data: {
                labels: ['Voted', 'Not Yet Voted'],
                datasets: [{
                    data: [<?php echo $total_voted; ?>, <?php echo max(0, $total_students - $total_voted); ?>],
                    backgroundColor: ['rgba(39, 174, 96, 0.8)', 'rgba(189, 195, 199, 0.5)'],
                    borderColor: ['rgba(39, 174, 96, 1)', 'rgba(189, 195, 199, 1)'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // ─── Live Refresh — Fetch analytics every 15 seconds ─────────────────────
    setInterval(async () => {
        try {
            const res = await fetch('api/analytics.php');
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;

            document.getElementById('stat-students').textContent = data.overview.total_students.toLocaleString();
            document.getElementById('stat-votes').textContent = data.overview.total_votes_cast.toLocaleString();
            document.getElementById('stat-turnout').textContent = data.overview.voter_turnout + '%';
        } catch (e) { /* silent fail */ }
    }, 15000);
    
    <?php endif; ?>
    </script>
    <script src="../assets/js/theme.js" defer></script>
</body>
</html>
