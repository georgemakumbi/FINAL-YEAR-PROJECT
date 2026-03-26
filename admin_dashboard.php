<?php
/**
 * =============================================================================
 * ADMIN DASHBOARD - Kyambogo University Online Voting System
 * =============================================================================
 * Modular admin dashboard router and main controller.
 * 
 * This file:
 * - Handles authentication and session management
 * - Acts as a central router for all modules
 * - Manages the overall page layout (header, sidebar, footer)
 * - Loads appropriate module logic and views
 * - Handles dashboard statistics and messages
 * 
 * Modular Architecture:
 * - includes/modules/elections/
 * - includes/modules/students/
 * - includes/modules/candidates/
 * - includes/modules/audit_logs/
 * - includes/modules/feedback/
 * 
 * =============================================================================
 */

// Include database connection and security
include 'db_connection.php';
require 'admin_security.php';
require_once 'includes/audit_logger.php';
require_once 'includes/modules/common.php';

// Ensure admin is logged in
require_admin_login();

// Generate and verify CSRF token
ensure_csrf_token();

// Check super_admin privileges
$is_super_admin = isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';

// =============================================================================
// MESSAGE HANDLING
// =============================================================================

$message = '';
$message_type = '';

if (isset($_GET['success']) && $_GET['success'] !== '') {
    $message = $_GET['success'];
    $message_type = 'success';
} elseif (isset($_GET['error']) && $_GET['error'] !== '') {
    $message = $_GET['error'];
    $message_type = 'error';
}

// =============================================================================
// DEADLINE HANDLING
// =============================================================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deadline'])) {
    verify_csrf_or_die();
    
    $new_deadline = trim($_POST['deadline']);
    if (file_put_contents("deadline.txt", $new_deadline) !== false) {
        $message = "Deadline updated successfully.";
        $message_type = 'success';
        log_audit_event(
            $conn,
            isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
            'DEADLINE_UPDATED',
            'Voting deadline set to ' . $new_deadline
        );
    } else {
        $message = "Failed to update deadline.";
        $message_type = 'error';
    }
}

// =============================================================================
// DASHBOARD STATISTICS
// =============================================================================

$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_candidates = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];
$total_votes = $conn->query("SELECT COUNT(*) as count FROM votes")->fetch_assoc()['count'];
$voters_who_voted = $conn->query("SELECT COUNT(*) as count FROM students WHERE has_voted = 1")->fetch_assoc()['count'];
$turnout = $total_students > 0 ? round(($voters_who_voted / $total_students) * 100, 1) : 0;

// Recent voting activity
$recent_votes = $conn->query("SELECT v.*, s.first_name, s.last_name, c.first_name as candidate_first_name, c.last_name as candidate_last_name 
    FROM votes v 
    LEFT JOIN students s ON v.student_id = s.student_id 
    LEFT JOIN candidates c ON v.candidate_id = c.candidate_id 
    ORDER BY v.vote_date DESC LIMIT 10");

// Current deadline
$deadline = file_get_contents("deadline.txt");
$current_deadline = $deadline ? $deadline : '';

// =============================================================================
// DETERMINE WHICH SECTION TO DISPLAY
// =============================================================================

$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
$valid_sections = ['dashboard', 'elections', 'candidates', 'students', 'results', 'feedback', 'audit'];

if (!in_array($section, $valid_sections)) {
    $section = 'dashboard';
}

// =============================================================================
// LOAD MODULE LOGIC FILES
// =============================================================================

switch ($section) {
    case 'elections':
        include_once 'includes/modules/elections/elections.logic.php';
        break;
    case 'candidates':
        include_once 'includes/modules/candidates/candidates.logic.php';
        break;
    case 'students':
        include_once 'includes/modules/students/students.logic.php';
        break;
    case 'audit':
        include_once 'includes/modules/audit_logs/audit_logs.logic.php';
        break;
    case 'feedback':
        include_once 'includes/modules/feedback/feedback.logic.php';
        break;
}

// We also need results data for the results section
if ($section === 'results') {
    $positions = $conn->query("SELECT DISTINCT position FROM candidates");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kyambogo University Voting System</title>
    <link rel="icon" href="images/image.png" type="png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        <?php include 'styles/admin_dashboard.css'; ?>
    </style>
</head>
<body>
    <!-- =========================================================================
    SIDEBAR NAVIGATION
    ========================================================================= -->
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
        </ul>
    </div>

    <!-- =========================================================================
    MAIN CONTENT
    ========================================================================= -->
    <div class="main-content">
        
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo safe_output($message); ?>
            </div>
        <?php endif; ?>

        <!-- Top bar -->
        <div class="top-bar">
            <h1>Admin Dashboard</h1>
            <div class="top-bar-right">
                <span class="user-info">👤 <?php echo safe_output($_SESSION['admin_username']); ?></span>
                <a href="admin_logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <!-- =========================================================================
        DASHBOARD SECTION
        ========================================================================= -->
        <?php if ($section === 'dashboard'): ?>
        <div id="dashboard" class="section active">
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <div class="value"><?php echo format_number($total_students); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Active Elections</h3>
                    <div class="value"><?php echo $conn->query("SELECT COUNT(*) as count FROM elections WHERE status = 'active'")->fetch_assoc()['count']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Votes Cast</h3>
                    <div class="value"><?php echo format_number($total_votes); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Voter Turnout</h3>
                    <div class="value"><?php echo $turnout; ?>%</div>
                </div>
            </div>

            <!-- Activity and Deadline -->
            <div class="two-columns">
                <div class="card">
                    <h2>📊 Recent Voting Activity</h2>
                    <?php if ($recent_votes && $recent_votes->num_rows > 0): ?>
                        <?php while ($vote = $recent_votes->fetch_assoc()): ?>
                            <div class="activity-item">
                                <div>
                                    <strong><?php echo safe_output($vote['first_name'] . ' ' . $vote['last_name']); ?></strong> 
                                    voted for 
                                    <strong><?php echo safe_output($vote['candidate_first_name'] . ' ' . $vote['candidate_last_name']); ?></strong>
                                </div>
                                <div class="activity-time"><?php echo safe_output($vote['vote_date']); ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No recent voting activity.</p>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2>⏰ Voting Deadline</h2>
                    <form method="post">
                        <?php echo render_csrf_field(); ?>
                        <div class="form-group">
                            <label style="color: #7f8c8d;">Set Deadline (YYYY-MM-DD HH:MM:SS):</label>
                            <input type="text" name="deadline" required placeholder="2025-09-01 19:00:00" 
                                   value="<?php echo safe_output($current_deadline); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Deadline</button>
                    </form>
                    <?php if ($current_deadline): ?>
                        <p style="margin-top: 15px; font-size: 18px; font-weight: bold;">
                            Current: <?php echo safe_output($current_deadline); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Voting Statistics Chart -->
            <div class="card">
                <h2>📈 Real-time Voting Statistics</h2>
                <div class="chart-container">
                    <canvas id="votingChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- =========================================================================
        ELECTIONS MODULE
        ========================================================================= -->
<?php if ($section === 'elections'): ?>
            <div id="elections" class="section active">
                <?php include_once 'includes/modules/elections/elections.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- =========================================================================
        CANDIDATES MODULE
        ========================================================================= -->
<?php if ($section === 'candidates'): ?>
            <div id="candidates" class="section active">
                <?php include_once 'includes/modules/candidates/candidates.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- =========================================================================
        STUDENTS MODULE
        ========================================================================= -->
<?php if ($section === 'students'): ?>
            <div id="students" class="section active">
                <?php include_once 'includes/modules/students/students.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- =========================================================================
        RESULTS SECTION
        ========================================================================= -->
        <?php if ($section === 'results'): ?>
        <div id="results" class="section active">
            <div class="card">
                <h2>📈 Election Results</h2>
                
                <?php if ($positions && $positions->num_rows > 0): ?>
                    <?php while ($position = $positions->fetch_assoc()): 
                        $pos = $position['position'];
                        
                        $candidates_stmt = $conn->prepare("SELECT * FROM candidates WHERE position = ? ORDER BY votes DESC");
                        $candidates_stmt->bind_param("s", $pos);
                        $candidates_stmt->execute();
                        $candidates_by_pos = $candidates_stmt->get_result();

                        $total_stmt = $conn->prepare("SELECT SUM(votes) as total FROM candidates WHERE position = ?");
                        $total_stmt->bind_param("s", $pos);
                        $total_stmt->execute();
                        $total_votes_pos = $total_stmt->get_result()->fetch_assoc()['total'] ?: 0;
                    ?>
                        <div style="margin-bottom: 40px;">
                            <h3><?php echo safe_output($pos); ?></h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Faculty</th>
                                        <th>Votes</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($cand = $candidates_by_pos->fetch_assoc()): 
                                        $percentage = $total_votes_pos > 0 ? round(($cand['votes'] / $total_votes_pos) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo safe_output($cand['first_name'] . ' ' . $cand['last_name']); ?></td>
                                            <td><?php echo safe_output($cand['faculty']); ?></td>
                                            <td><strong><?php echo format_number($cand['votes']); ?></strong></td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div style="flex: 1; background: #ecf0f1; height: 20px; border-radius: 10px; overflow: hidden;">
                                                        <div style="width: <?php echo $percentage; ?>%; background: #27ae60; height: 100%;"></div>
                                                    </div>
                                                    <?php echo $percentage; ?>%
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php $candidates_stmt->close(); ?>
                        <?php $total_stmt->close(); ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No election results available.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- =========================================================================
        AUDIT LOGS MODULE
        ========================================================================= -->
        <?php if ($section === 'audit'): ?>
            <div id="audit" class="section active">
                <?php include_once 'includes/modules/audit_logs/audit_logs.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- =========================================================================
        FEEDBACK MODULE
        ========================================================================= -->
        <?php if ($section === 'feedback'): ?>
            <div id="feedback" class="section active">
                <?php include_once 'includes/modules/feedback/feedback.view.php'; ?>
            </div>
        <?php endif; ?>

        <!-- =========================================================================
        FOOTER
        ========================================================================= -->
        <footer>
            <p>Designed and Developed by the Kyambogo University BITC students Class Of 2023</p>
            <p>&copy; <?php echo date("Y"); ?> Kyambogo University. All rights reserved.</p>
        </footer>
    </div>

    <!-- =========================================================================
    JAVASCRIPT
    ========================================================================= -->
    <script>
        const ctx = document.getElementById('votingChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total Students', 'Candidates', 'Votes Cast', 'Voter Turnout %'],
                    datasets: [{
                        label: 'Statistics',
                        data: [<?php echo $total_students; ?>, <?php echo $total_candidates; ?>, <?php echo $total_votes; ?>, <?php echo $turnout; ?>],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(39, 174, 96, 0.7)',
                            'rgba(243, 156, 18, 0.7)',
                            'rgba(155, 89, 182, 0.7)'
                        ],
                        borderColor: [
                            'rgba(52, 152, 219, 1)',
                            'rgba(39, 174, 96, 1)',
                            'rgba(243, 156, 18, 1)',
                            'rgba(155, 89, 182, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
