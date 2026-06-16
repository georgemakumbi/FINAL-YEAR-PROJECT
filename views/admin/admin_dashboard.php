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

    </style>
</head>
<body>
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SIDEBAR OVERLAY (mobile)                                           -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SIDEBAR                                                            -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </div>
            <h2>Admin Panel</h2>
            <p>Voting System</p>
        </div>
        
        <ul class="nav-menu">
            <li><a href="admin_dashboard.php?section=dashboard" class="nav-item <?php echo $section === 'dashboard' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span> Dashboard</a></li>
            <li><a href="admin_dashboard.php?section=elections" class="nav-item <?php echo $section === 'elections' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg></span> Elections</a></li>
            <li><a href="admin_dashboard.php?section=candidates" class="nav-item <?php echo $section === 'candidates' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span> Candidates</a></li>
            <li><a href="admin_dashboard.php?section=students" class="nav-item <?php echo $section === 'students' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span> Students</a></li>
            <li><a href="admin_dashboard.php?section=results" class="nav-item <?php echo $section === 'results' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span> Results</a></li>
            <li><a href="admin_dashboard.php?section=feedback" class="nav-item <?php echo $section === 'feedback' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span> Feedback</a></li>
            <li><a href="admin_dashboard.php?section=audit" class="nav-item <?php echo $section === 'audit' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span> Audit Log</a></li>
            <li><a href="admin_dashboard.php?section=settings" class="nav-item <?php echo $section === 'settings' ? 'active' : ''; ?>"><span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span> Settings</a></li>
        </ul>

        <div class="sidebar-footer">
            &copy; <?php echo date("Y"); ?> Kyambogo University
        </div>
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
            <div class="top-bar-left">
                <button class="hamburger" id="hamburgerBtn" aria-label="Toggle sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h1><?php echo ucfirst($section === 'dashboard' ? 'Dashboard' : $section); ?></h1>
            </div>
            <div class="top-bar-right">
                <button class="theme-toggle-btn" data-theme-toggle-btn aria-label="Toggle theme">
                    <svg class="theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg class="theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <span class="user-info">
                    <span class="user-avatar"><?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?></span>
                    <?php echo safe_output($_SESSION['admin_username']); ?>
                </span>
                <form action="admin_logout.php" method="POST" style="margin:0; display:inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <button type="submit" class="btn btn-danger" style="cursor:pointer;">Logout</button>
                </form>
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
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <h3>Total Students</h3>
                    <div class="value" id="stat-students"><?php echo format_number($total_students); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
                    </div>
                    <h3>Active Elections</h3>
                    <div class="value"><?php echo $active_elections; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                    <h3>Votes Cast</h3>
                    <div class="value" id="stat-votes"><?php echo format_number($total_votes); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <h3>Voter Turnout</h3>
                    <div class="value" id="stat-turnout"><?php echo $turnout; ?>%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
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
    <script>
    // ─── Theme Toggle ─────────────────────────────────────────────────────────
    (function() {
        var storageKey = "siteTheme";
        var prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
        var saved = localStorage.getItem(storageKey);
        var isDark = saved ? saved === "dark" : prefersDark;

        document.body.classList.toggle("dark", isDark);

        function updateButtons(dark) {
            document.querySelectorAll("[data-theme-toggle-btn]").forEach(function(btn) {
                var sun = btn.querySelector(".theme-icon-sun");
                var moon = btn.querySelector(".theme-icon-moon");
                if (sun && moon) {
                    sun.style.display = dark ? "none" : "block";
                    moon.style.display = dark ? "block" : "none";
                }
            });
        }

        function setTheme(dark) {
            document.body.classList.toggle("dark", dark);
            localStorage.setItem(storageKey, dark ? "dark" : "light");
            updateButtons(dark);
        }

        document.querySelectorAll("[data-theme-toggle-btn]").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                var dark = !document.body.classList.contains("dark");
                setTheme(dark);
            });
        });

        updateButtons(isDark);
    })();

    // ─── Sidebar Toggle (Mobile) ──────────────────────────────────────────────
    (function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const hamburger = document.getElementById('hamburgerBtn');

        if (sidebar && overlay && hamburger) {
            const open = () => {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            };
            const close = () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            };

            hamburger.addEventListener('click', open);
            overlay.addEventListener('click', close);

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            });

            // Close on nav link click (mobile)
            sidebar.querySelectorAll('.nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) close();
                });
            });

            // Handle resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) close();
            });
        }
    })();
    </script>
</body>
</html>
