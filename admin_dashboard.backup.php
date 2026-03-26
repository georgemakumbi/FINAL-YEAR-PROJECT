<?php
/**
 * =============================================================================
 * ADMIN DASHBOARD - Kyambogo University Online Voting System
 * =============================================================================
 * This file serves as the main administration panel for the voting system.
 * It provides functionality for managing elections, candidates, students,
 * viewing results, and monitoring system activity.
 * 
 * Features:
 * - Dashboard overview with statistics
 * - Election management (create, update status)
 * - Candidate management (add, edit, delete)
 * - Student management (add, edit, delete, search)
 * - Real-time election results
 * - Audit log tracking
 * - Feedback viewing
 * 
 * Access Control: Requires admin login. Some features restricted to super_admin role.
 * =============================================================================
 */

// Include database connection configuration
include 'db_connection.php';

// Include security functions (authentication, CSRF protection)
require 'admin_security.php';
require_once 'includes/audit_logger.php';

// Ensure admin is logged in - redirects to login page if not authenticated
require_admin_login();

// Generate and verify CSRF token to prevent cross-site request forgery attacks
ensure_csrf_token();

// Check if the current admin has super_admin privileges
// Super admins can: manage elections status, add/edit/delete candidates & students
// Regular admins can: view data, create elections
$is_super_admin = isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';

// =============================================================================
// FORM SUBMISSION HANDLING
// =============================================================================
// Initialize message variables for user feedback
$message = '';        // The message content to display
$message_type = '';   // Type: 'success' or 'error' for styling

// Check for messages passed via URL parameters (from other pages)
if (isset($_GET['success']) && $_GET['success'] !== '') {
    $message = $_GET['success'];
    $message_type = 'success';
} elseif (isset($_GET['error']) && $_GET['error'] !== '') {
    $message = $_GET['error'];
    $message_type = 'error';
}

// Process POST form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token to prevent CSRF attacks - dies if invalid
    verify_csrf_or_die();

    // -----------------------------------------------------------------------------
    // DEADLINE UPDATE HANDLER
    // -----------------------------------------------------------------------------
    // Allows admin to update the voting deadline
    if (isset($_POST['deadline'])) {
        // Get and sanitize the new deadline value
        $new_deadline = trim($_POST['deadline']);
        
        // Save deadline to a text file for simple persistence
        // Format: YYYY-MM-DD HH:MM:SS
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

    // -----------------------------------------------------------------------------
    // ELECTION CREATION HANDLER
    // -----------------------------------------------------------------------------
    // Creates a new election with scheduled status
    if (isset($_POST['create_election'])) {
        // Escape user inputs to prevent SQL injection
        $election_title = $conn->real_escape_string($_POST['election_title']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $position = $conn->real_escape_string($_POST['position']);

        // Prepare and execute INSERT statement
        $stmt = $conn->prepare("INSERT INTO elections (election_title, start_date, end_date, position, status) VALUES (?, ?, ?, ?, 'scheduled')");
        $stmt->bind_param("ssss", $election_title, $start_date, $end_date, $position);

        if ($stmt->execute()) {
            $message = "Election created successfully!";
            $message_type = 'success';
            log_audit_event(
                $conn,
                isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                'ELECTION_CREATED',
                'Election "' . $election_title . '" created for position "' . $position . '"'
            );
        } else {
            $message = "Error creating election.";
            $message_type = 'error';
        }
        $stmt->close();
    }

    // -----------------------------------------------------------------------------
    // ELECTION STATUS UPDATE HANDLER (Super Admin Only)
    // -----------------------------------------------------------------------------
    // Allows super admins to start or close elections
    if (isset($_POST['update_election']) && $is_super_admin) {
        // Get and validate election ID (cast to int for security)
        $election_id = (int)($_POST['election_id'] ?? 0);
        $new_status = $_POST['status'] ?? '';
        
        // Validate status is one of the allowed values
        if ($election_id > 0 && in_array($new_status, ['scheduled', 'active', 'closed'], true)) {
            $stmt = $conn->prepare("UPDATE elections SET status = ? WHERE election_id = ?");
            $stmt->bind_param("si", $new_status, $election_id);
            $stmt->execute();
            $stmt->close();
            if ($conn->affected_rows > 0) {
                $message = "Election status updated.";
                $message_type = 'success';
                log_audit_event(
                    $conn,
                    isset($_SESSION['admin_id']) ? (string)$_SESSION['admin_id'] : null,
                    'ELECTION_STATUS_UPDATED',
                    'Election ID ' . $election_id . ' status changed to ' . $new_status
                );
            }
        }
    }
}

// =============================================================================
// DATABASE QUERIES - STATISTICS
// =============================================================================
// These queries fetch system-wide statistics for the dashboard

// Get total number of registered students
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];

// Get total number of candidates in the system
$total_candidates = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];

// Get total number of votes cast
$total_votes = $conn->query("SELECT COUNT(*) as count FROM votes")->fetch_assoc()['count'];

// Get number of students who have voted
$voters_who_voted = $conn->query("SELECT COUNT(*) as count FROM students WHERE has_voted = 1")->fetch_assoc()['count'];

// Calculate voter turnout percentage
// Formula: (voters who voted / total students) * 100
$turnout = $total_students > 0 ? round(($voters_who_voted / $total_students) * 100, 1) : 0;

// =============================================================================
// DATABASE QUERIES - DATA FETCHING
// =============================================================================

// Get current voting deadline from file
$deadline = file_get_contents("deadline.txt");
$current_deadline = $deadline ? $deadline : '';

// Fetch all elections ordered by start date (newest first)
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date DESC");

// Fetch recent voting activity for the activity feed
// Joins votes with students and candidates tables to get full details
// Limits to 10 most recent votes
$recent_votes = $conn->query("SELECT v.*, s.first_name, s.last_name, c.first_name as candidate_first_name, c.last_name as candidate_last_name 
    FROM votes v 
    LEFT JOIN students s ON v.student_id = s.student_id 
    LEFT JOIN candidates c ON v.candidate_id = c.candidate_id 
    ORDER BY v.vote_date DESC LIMIT 10");

// -----------------------------------------------------------------------------
// SEARCH FUNCTIONALITY
// -----------------------------------------------------------------------------
// Allows admin to search for students by ID, name, or email
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$search_results = null;

if ($search_term) {
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ?");
    $search_param = "%$search_term%";
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $search_results = $stmt->get_result();
}

// Fetch all candidates ordered by vote count (highest first)
$candidates = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");

// -----------------------------------------------------------------------------
// FEEDBACK QUERY
// -----------------------------------------------------------------------------
// Check if feedback table exists before querying
$feedback_entries = null;
$feedback_table_exists = $conn->query("SHOW TABLES LIKE 'feedback'")->num_rows > 0;

if ($feedback_table_exists) {
    // Fetch all feedback entries with student names
    $feedback_entries = $conn->query("SELECT f.student_id, f.feedback, s.first_name, s.last_name
        FROM feedback f
        LEFT JOIN students s ON f.student_id = s.student_id
        ORDER BY f.student_id ASC");
}

// -----------------------------------------------------------------------------
// AUDIT LOG FILTERING
// -----------------------------------------------------------------------------
$audit_date = isset($_GET['audit_date']) ? $_GET['audit_date'] : '';
$audit_action = isset($_GET['audit_action']) ? $_GET['audit_action'] : '';

if ($audit_date !== '') {
    $date_obj = DateTime::createFromFormat('Y-m-d', $audit_date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $audit_date) {
        $audit_date = '';
    }
}

$audit_log_table_exists = $conn->query("SHOW TABLES LIKE 'audit_log'")->num_rows > 0;

// Build audit query with filters
$audit_conditions = [];
$audit_params = [];
$audit_param_types = '';

if ($audit_date) {
    $audit_conditions[] = "DATE(audit_entries.timestamp) = ?";
    $audit_params[] = $audit_date;
    $audit_param_types .= 's';
}

if ($audit_action !== '') {
    if ($audit_action === 'vote') {
        $audit_conditions[] = "audit_entries.action = 'VOTE_CAST'";
    } elseif ($audit_action === 'login') {
        $audit_conditions[] = "audit_entries.action IN ('ADMIN_LOGIN', 'ADMIN_LOGOUT', 'STUDENT_LOGIN', 'STUDENT_LOGOUT')";
    } elseif ($audit_action === 'admin') {
        $audit_conditions[] = "audit_entries.action IN ('DEADLINE_UPDATED', 'ELECTION_CREATED', 'ELECTION_STATUS_UPDATED', 'CANDIDATE_ADDED', 'CANDIDATE_UPDATED', 'CANDIDATE_DELETED', 'STUDENT_ADDED', 'STUDENT_UPDATED', 'STUDENT_DELETED')";
    } else {
        $audit_conditions[] = "1 = 0";
    }
}

$audit_where = '';
if (!empty($audit_conditions)) {
    $audit_where = "WHERE " . implode(" AND ", $audit_conditions);
}

$audit_source_query = "SELECT
        v.vote_date AS timestamp,
        'VOTE_CAST' AS action,
        COALESCE(NULLIF(TRIM(CONCAT(s.first_name, ' ', s.last_name)), ''), v.student_id) AS user,
        COALESCE(CONCAT('Voted for ', c.first_name, ' ', c.last_name), 'N/A') AS details
    FROM votes v
    LEFT JOIN students s ON v.student_id = s.student_id
    LEFT JOIN candidates c ON v.candidate_id = c.candidate_id";

if ($audit_log_table_exists) {
    $audit_source_query .= "
    UNION ALL
    SELECT
        al.timestamp AS timestamp,
        al.action AS action,
        COALESCE(a.username, NULLIF(TRIM(CONCAT(st.first_name, ' ', st.last_name)), ''), al.user_id, 'Unknown') AS user,
        COALESCE(al.details, 'N/A') AS details
    FROM audit_log al
    LEFT JOIN admin a ON al.user_id = CAST(a.admin_id AS CHAR)
    LEFT JOIN students st ON al.user_id = st.student_id
    WHERE al.action <> 'VOTE_CAST'";
}

$audit_query = "SELECT audit_entries.timestamp, audit_entries.action, audit_entries.user, audit_entries.details
    FROM ($audit_source_query) AS audit_entries
    $audit_where
    ORDER BY audit_entries.timestamp DESC
    LIMIT 50";

if (!empty($audit_params)) {
    $audit_stmt = $conn->prepare($audit_query);
    $audit_stmt->bind_param($audit_param_types, ...$audit_params);
    $audit_stmt->execute();
    $audit_result = $audit_stmt->get_result();
} else {
    $audit_result = $conn->query($audit_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kyambogo University Voting System</title>
    <!-- Favicon for the admin panel -->
    <link rel="icon" href="images/image.png" type="png">
    <!-- Chart.js library for rendering voting statistics charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Include admin dashboard-specific styles */
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
        
        <!-- Navigation menu with section toggles -->
        <ul class="nav-menu">
            <!-- Each nav-item has onclick handler to show corresponding section -->
            <li class="nav-item active" onclick="showSection('dashboard')">📊 Dashboard</li>
            <li class="nav-item" onclick="showSection('elections')">🗳️ Elections</li>
            <li class="nav-item" onclick="showSection('candidates')">👥 Candidates</li>
            <li class="nav-item" onclick="showSection('students')">🎓 Students</li>
            <li class="nav-item" onclick="showSection('results')">📈 Election Results</li>
            <li class="nav-item" onclick="showSection('feedback')">💬 Feedback</li>
            <li class="nav-item" onclick="showSection('audit')">🔍 Audit Log</li>
        </ul>
    </div>

    <!-- =========================================================================
    MAIN CONTENT AREA
    ========================================================================= -->
    <div class="main-content">
        
        <!-- =========================================================================
        SUCCESS/ERROR MESSAGE DISPLAY
        ========================================================================= -->
        <!-- Displays messages to user after form submissions or actions -->
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Top bar with title and user info -->
        <div class="top-bar">
            <h1>Admin Dashboard</h1>
            <div class="top-bar-right">
                <!-- Display logged-in admin username -->
                <span class="user-info">👤 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <!-- Logout button -->
                <a href="admin_logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <!-- =========================================================================
        SECTION 1: DASHBOARD OVERVIEW
        ========================================================================= -->
        <!-- Shows key statistics and recent activity at a glance -->
        <div id="dashboard" class="section active">
            
            <!-- Statistics cards showing key metrics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <div class="value"><?php echo number_format($total_students); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Active Elections</h3>
                    <!-- Dynamic query to count only active elections -->
                    <div class="value"><?php echo $conn->query("SELECT COUNT(*) as count FROM elections WHERE status = 'active'")->fetch_assoc()['count']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Votes Cast Today</h3>
                    <div class="value"><?php echo number_format($total_votes); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Voter Turnout</h3>
                    <div class="value"><?php echo $turnout; ?>%</div>
                </div>
            </div>

            <!-- Two-column layout for activity and deadline -->
            <div class="two-columns">
                <!-- Recent voting activity card -->
                <div class="card">
                    <h2>📊 Recent Voting Activity</h2>
                    <?php if ($recent_votes && $recent_votes->num_rows > 0): ?>
                        <!-- Loop through each recent vote -->
                        <?php while ($vote = $recent_votes->fetch_assoc()): ?>
                            <div class="activity-item">
                                <div>
                                    <!-- Display voter name and candidate they voted for -->
                                    <strong><?php echo htmlspecialchars($vote['first_name'] . ' ' . $vote['last_name']); ?></strong> 
                                    voted for 
                                    <strong><?php echo htmlspecialchars($vote['candidate_first_name'] . ' ' . $vote['candidate_last_name']); ?></strong>
                                </div>
                                <div class="activity-time"><?php echo htmlspecialchars($vote['vote_date']); ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No recent voting activity.</p>
                    <?php endif; ?>
                </div>

                <!-- Voting deadline management card -->
                <div class="card">
                    <h2>⏰ Voting Deadline</h2>
                    <!-- Form to update the voting deadline -->
                    <form method="post">
                        <!-- CSRF token for security -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <div class="form-group">
                            <label style="color: #7f8c8d;">Set Deadline (YYYY-MM-DD HH:MM:SS):</label>
                            <input type="text" name="deadline" required placeholder="2025-09-01 19:00:00" 
                                   value="<?php echo htmlspecialchars($current_deadline); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Deadline</button>
                    </form>
                    <!-- Display current deadline if set -->
                    <?php if ($current_deadline): ?>
                        <p style="margin-top: 15px; font-size: 18px; font-weight: bold;">
                            Current: <?php echo htmlspecialchars($current_deadline); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Real-time voting statistics chart -->
            <div class="card">
                <h2>📈 Real-time Voting Statistics</h2>
                <div class="chart-container">
                    <!-- Canvas element for Chart.js to render the bar chart -->
                    <canvas id="votingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- =========================================================================
        SECTION 2: ELECTIONS MANAGEMENT
        ========================================================================= -->
        <!-- Create new elections and manage existing ones -->
        <div id="elections" class="section">
            <div class="card">
                <h2>🗳️ Manage Elections</h2>
                
                <!-- Create Election Form -->
                <!-- Allows admins to create new elections with title, position, and dates -->
                <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <h3>Create New Election</h3>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="create_election" value="1">
                        <div class="two-columns">
                            <div class="form-group">
                                <label>Election Title</label>
                                <input type="text" name="election_title" required placeholder="e.g., Guild Elections 2026">
                            </div>
                            <div class="form-group">
                                <label>Position</label>
                                <select name="position" required>
                                    <option value="Guild President">Guild President</option>
                                    <option value="Guild Vice President">Guild Vice President</option>
                                    <option value="Secretary General">Secretary General</option>
                                    <option value="Finance Minister">Finance Minister</option>
                                    <option value="Academic Affairs">Academic Affairs</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="datetime-local" name="start_date" required>
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="datetime-local" name="end_date" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Create Election</button>
                    </form>
                </div>
                
                <!-- Existing Elections Table -->
                <h3>Existing Elections</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Election Title</th>
                            <th>Position</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if elections table exists in database
                        $table_exists = $conn->query("SHOW TABLES LIKE 'elections'")->num_rows > 0;
                        
                        // If table exists and has records, display them
                        if ($table_exists && $elections && $elections->num_rows > 0):
                            // Loop through each election and display details
                            while ($election = $elections->fetch_assoc()): 
                                // Determine status CSS class and display text based on status
                                $status_class = '';
                                $status_text = '';
                                switch($election['status']) {
                                    case 'active':
                                        $status_class = 'status-active';
                                        $status_text = 'Active';
                                        break;
                                    case 'closed':
                                        $status_class = 'status-closed';
                                        $status_text = 'Closed';
                                        break;
                                    default:
                                        $status_class = 'status-scheduled';
                                        $status_text = 'Scheduled';
                                }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($election['election_title']); ?></td>
                                <td><?php echo htmlspecialchars($election['position']); ?></td>
                                <td><?php echo htmlspecialchars($election['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($election['end_date']); ?></td>
                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                <td class="action-btns">
                                    <!-- Start Election button - only visible to super_admin when status is scheduled -->
                                    <?php if ($is_super_admin && $election['status'] == 'scheduled'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="update_election" value="1">
                                            <input type="hidden" name="election_id" value="<?php echo (int)$election['election_id']; ?>">
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-success btn-small">Start</button>
                                        </form>
                                    <!-- Close Election button - only visible to super_admin when status is active -->
                                    <?php elseif ($is_super_admin && $election['status'] == 'active'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="update_election" value="1">
                                            <input type="hidden" name="election_id" value="<?php echo (int)$election['election_id']; ?>">
                                            <input type="hidden" name="status" value="closed">
                                            <button type="submit" class="btn btn-warning btn-small">Close</button>
                                        </form>
                                    <?php endif; ?>
                                    <!-- View Report button - links to detailed election report -->
                                    <a href="election_report.php?election_id=<?php echo (int)$election['election_id']; ?>"
                                       class="btn btn-primary btn-small">Report</a>
                                    <!-- Edit Election button - links to edit page -->
                                    <a href="edit_election.php?id=<?php echo $election['election_id']; ?>" 
                                       class="btn btn-primary btn-small">Edit</a>
                                </td>
                            </tr>
                        <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    No elections found. Create one above.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================================
        SECTION 3: CANDIDATES MANAGEMENT
        ========================================================================= -->
        <!-- Add, edit, delete candidates and view their vote counts -->
        <div id="candidates" class="section">
            <div class="card">
                <h2>👥 Manage Candidates</h2>
                
                <!-- Add New Candidate Form - Only visible to super_admin -->
                <?php if ($is_super_admin): ?>
                <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <h3>Add New Candidate</h3>
                    <!-- Form with multipart for file upload (candidate photo) -->
                    <form action="add_candidate.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <div class="two-columns">
                            <div class="form-group">
                                <label>Student ID</label>
                                <input type="text" name="student_id" required placeholder="e.g., 23/U/1234">
                            </div>
                            <div class="form-group">
                                <label>Position</label>
                                <select name="position" required>
                                    <option value="Guild President">Guild President</option>
                                    <option value="Guild Vice President">Guild Vice President</option>
                                    <option value="Secretary General">Secretary General</option>
                                    <option value="Finance Minister">Finance Minister</option>
                                    <option value="Academic Affairs">Academic Affairs</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Manifesto</label>
                                <textarea name="manifesto" rows="4" placeholder="Candidate manifesto..." style="width: 100%; padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; resize: vertical;"></textarea>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Candidate Photo (JPG, PNG, WEBP, max 2MB)</label>
                                <input type="file" name="candidate_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            </div>
                            <div class="form-group">
                                <label>Department (leave empty to auto-fill from student)</label>
                                <input type="text" name="department" placeholder="e.g., Computer Science">
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_university_wide" value="1"> 
                                    University-wide Position (e.g., Guild President, Guild Vice President)
                                </label>
                                <p style="font-size: 12px; color: #666; margin-top: 5px;">
                                    Check this for positions that all students can vote for, regardless of department.
                                </p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Add Candidate</button>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Candidates Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Faculty</th>
                            <th>Department</th>
                            <th>Scope</th>
                            <th>Votes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($candidates && $candidates->num_rows > 0): ?>
                            <?php while ($candidate = $candidates->fetch_assoc()): 
                                // Set image source - use uploaded image or placeholder
                                $image_src = $candidate['image_path'] ? $candidate['image_path'] : 'images/placeholder.png';
                                // Determine scope - university-wide or department
                                $scope = (isset($candidate['is_university_wide']) && $candidate['is_university_wide'] == 1) 
                                    ? '<span class="status-badge status-active">University-wide</span>' 
                                    : '<span class="status-badge status-scheduled">' . htmlspecialchars($candidate['department'] ?? 'N/A') . '</span>';
                            ?>
                                <tr>
                                    <td>
                                        <?php if (file_exists($image_src)): ?>
                                            <!-- Display candidate photo -->
                                            <img src="<?php echo htmlspecialchars($image_src); ?>" class="candidate-img" alt="Candidate">
                                        <?php else: ?>
                                            <!-- Display initials as placeholder if no image -->
                                            <div class="candidate-img" style="background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                <?php echo strtoupper(substr($candidate['first_name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['position']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['faculty']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['department'] ?? 'N/A'); ?></td>
                                    <td><?php echo $scope; ?></td>
                                    <td><strong><?php echo $candidate['votes']; ?></strong></td>
                                    <td class="action-links">
                                        <?php if ($is_super_admin): ?>
                                        <!-- Edit candidate link -->
                                        <a href="edit_candidate.php?id=<?php echo $candidate['candidate_id']; ?>">Edit</a>
                                        <!-- Delete candidate form with confirmation -->
                                        <form action="delete_candidate.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="id" value="<?php echo (int)$candidate['candidate_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-small">Delete</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No candidates found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================================
        SECTION 4: STUDENTS MANAGEMENT
        ========================================================================= -->
        <!-- Search, add, edit, and delete student voters -->
        <div id="students" class="section">
            <div class="card">
                <h2>🧑‍🎓 Student Management</h2>
                
                <!-- Search Box -->
                <!-- Allows filtering students by ID, name, or email -->
                <div class="search-box">
                    <form method="get" style="display: flex; gap: 10px; width: 100%;">
                        <input type="text" name="search" placeholder="Search by registration number or name..." 
                               value="<?php echo htmlspecialchars($search_term); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <?php if ($search_term): ?>
                            <a href="admin_dashboard.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Add New Student Form - Only visible to super_admin -->
                <?php if ($is_super_admin): ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <!-- Individual Student Addition -->
                    <div>
                        <h3>➕ Add New Student</h3>
                        <form action="add_student.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <div class="form-group">
                                <label>Student ID</label>
                                <input type="text" name="student_id" required>
                            </div>
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label>Faculty</label>
                                <input type="text" name="faculty" required>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <input type="text" name="department" required>
                            </div>
                            <button type="submit" class="btn btn-success">Add Student</button>
                        </form>
                    </div>

                    <!-- Bulk Import Students from CSV -->
                    <div>
                        <h3>📥 Import Students (CSV)</h3>
                        <form action="import_students.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <div class="form-group">
                                <label for="csv_file">Select CSV File</label>
                                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                                <small style="color: #666; margin-top: 8px; display: block;">
                                    CSV Format: student_id, first_name, last_name, email, password, faculty, department
                                </small>
                            </div>
                            <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13px;">
                                <strong>CSV Header (required):</strong><br>
                                <code>student_id,first_name,last_name,email,password,faculty,department</code>
                            </div>
                            <details style="margin-bottom: 15px;">
                                <summary style="cursor: pointer; color: #3498db; font-weight: 500;">
                                    View CSV Format Example
                                </summary>
                                <div style="background: #f8f9fa; padding: 12px; margin-top: 10px; border-radius: 6px; font-family: monospace; font-size: 12px;">
                                    <code>
                                        student_id,first_name,last_name,email,password,faculty,department<br>
                                        23/U/1001,John,Doe,john.doe@student.kyu.ac.ug,SecurePass123,Science,Computer Science<br>
                                        23/U/1002,Jane,Smith,jane.smith@student.kyu.ac.ug,SecurePass456,Engineering,Software Engineering
                                    </code>
                                </div>
                            </details>
                            <button type="submit" name="import_students" class="btn btn-success" style="width: 100%;">Import Students</button>
                            <a href="sample_students.csv" class="btn btn-secondary" style="width: 100%; margin-top: 8px; text-align: center; display: inline-block;">
                                ⬇️ Download Sample CSV
                            </a>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Student List Table -->
            <div class="card">
                <h3>📋 Student List <?php echo $search_term ? '(Search Results)' : ''; ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Faculty</th>
                            <th>Voted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // If search term exists, show search results; otherwise show all students (limited to 100)
                        if ($search_term && $search_results) {
                            $students = $search_results;
                        } else {
                            $students = $conn->query("SELECT * FROM students ORDER BY registration_date DESC LIMIT 100");
                        }
                        
                        // Display each student row
                        if ($students && $students->num_rows > 0):
                            while ($student = $students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['faculty']); ?></td>
                                    <td>
                                        <!-- Show whether student has voted -->
                                        <span class="status-badge <?php echo $student['has_voted'] ? 'status-active' : 'status-scheduled'; ?>">
                                            <?php echo $student['has_voted'] ? 'Yes' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td class="action-links">
                                        <?php if ($is_super_admin): ?>
                                        <!-- Edit student button -->
                                        <a href="update.php?id=<?php echo htmlspecialchars($student['student_id']); ?>"><button class="btn btn-primary btn-small">Edit</button></a>
                                        <!-- Delete student form with confirmation -->
                                        <form action="delete.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                                            <button type="submit" class="btn btn-danger btn-small">Delete</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================================
        SECTION 5: ELECTION RESULTS
        ========================================================================= -->
        <!-- Display real-time election results with vote percentages -->
        <div id="results" class="section">
            <div class="card">
                <h2>📈 Election Results</h2>
                
                <?php
                // Get all distinct positions that have candidates
                $positions = $conn->query("SELECT DISTINCT position FROM candidates");
                ?>
                
                <?php if ($positions && $positions->num_rows > 0): ?>
                    <!-- Loop through each position separately -->
                    <?php while ($position = $positions->fetch_assoc()): 
                        $pos = $position['position'];
                        
                        // Get candidates for this position, ordered by votes (highest first)
                        $candidates_stmt = $conn->prepare("SELECT * FROM candidates WHERE position = ? ORDER BY votes DESC");
                        $candidates_stmt->bind_param("s", $pos);
                        $candidates_stmt->execute();
                        $candidates_by_pos = $candidates_stmt->get_result();

                        // Calculate total votes for this position
                        $total_stmt = $conn->prepare("SELECT SUM(votes) as total FROM candidates WHERE position = ?");
                        $total_stmt->bind_param("s", $pos);
                        $total_stmt->execute();
                        $total_votes_pos = $total_stmt->get_result()->fetch_assoc()['total'] ?: 0;
                    ?>
                        <!-- Results for each position -->
                        <div style="margin-bottom: 40px;">
                            <h3><?php echo htmlspecialchars($pos); ?></h3>
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
                                        // Calculate percentage of total votes
                                        $percentage = $total_votes_pos > 0 ? round(($cand['votes'] / $total_votes_pos) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cand['first_name'] . ' ' . $cand['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($cand['faculty']); ?></td>
                                            <td><strong><?php echo $cand['votes']; ?></strong></td>
                                            <td>
                                                <!-- Visual progress bar showing vote percentage -->
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

        <!-- =========================================================================
        SECTION 6: AUDIT LOG
        ========================================================================= -->
        <!-- Track and view all voting activities for security and compliance -->
        <div id="audit" class="section">
            <div class="card">
                <h2>🗞️ Audit Log</h2>
                
                <!-- Audit Log Filter Form -->
                <!-- Allows filtering by date and action type -->
                <div class="search-box">
                    <form method="get" style="display: flex; gap: 10px; width: 100%;">
                        <input type="hidden" name="section" value="audit">
                        <input type="date" name="audit_date" value="<?php echo htmlspecialchars($audit_date); ?>">
                        <select name="audit_action">
                            <option value="" <?php echo $audit_action === '' ? 'selected' : ''; ?>>All Actions</option>
                            <option value="vote" <?php echo $audit_action === 'vote' ? 'selected' : ''; ?>>Vote Cast</option>
                            <option value="login" <?php echo $audit_action === 'login' ? 'selected' : ''; ?>>Login</option>
                            <option value="admin" <?php echo $audit_action === 'admin' ? 'selected' : ''; ?>>Admin Action</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>

                <!-- Audit Log Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Action Type</th>
                            <th>User</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($audit_result && $audit_result->num_rows > 0):
                            while ($log = $audit_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                                    <td><span class="status-badge status-active"><?php echo htmlspecialchars($log['action']); ?></span></td>
                                    <td><?php echo htmlspecialchars($log['user']); ?></td>
                                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No audit log entries found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================================
        SECTION 7: FEEDBACK VIEWING
        ========================================================================= -->
        <!-- View feedback submitted by students about the voting system -->
        <div id="feedback" class="section">
            <div class="card">
                <h2>Student Feedback</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$feedback_table_exists): ?>
                            <!-- Feedback table doesn't exist in database -->
                            <tr>
                                <td colspan="3" style="text-align: center;">Feedback table not found.</td>
                            </tr>
                        <?php elseif ($feedback_entries && $feedback_entries->num_rows > 0): ?>
                            <!-- Display all feedback entries -->
                            <?php while ($entry = $feedback_entries->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['student_id']); ?></td>
                                    <td>
                                        <?php
                                        // Combine first and last name, handle NULL values
                                        $student_name = trim(($entry['first_name'] ?? '') . ' ' . ($entry['last_name'] ?? ''));
                                        echo htmlspecialchars($student_name !== '' ? $student_name : 'Unknown Student');
                                        ?>
                                    </td>
                                    <!-- Use nl2br to preserve line breaks in feedback -->
                                    <td><?php echo nl2br(htmlspecialchars($entry['feedback'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">No feedback available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

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
        /**
         * Navigation function to switch between dashboard sections
         * @param {string} sectionId - The ID of the section to display
         * 
         * This function:
         * 1. Hides all sections by removing 'active' class
         * 2. Removes 'active' class from all nav items
         * 3. Shows the selected section and highlights its nav item
         */
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            // Remove active state from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Show the selected section
            document.getElementById(sectionId).classList.add('active');
            // Highlight the clicked nav item
            event.currentTarget.classList.add('active');
        }

        // Keep the selected section open after GET-based filtering.
        (function openSectionFromQuery() {
            const params = new URLSearchParams(window.location.search);
            const section = params.get('section');
            if (!section) return;
            const targetSection = document.getElementById(section);
            if (!targetSection) return;

            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            targetSection.classList.add('active');

            const activeNav = Array.from(document.querySelectorAll('.nav-item')).find(n =>
                (n.getAttribute('onclick') || '').includes("'" + section + "'")
            );
            if (activeNav) {
                activeNav.classList.add('active');
            }
        })();

        /**
         * Initialize Chart.js bar chart for voting statistics
         * Displays: Total Students, Candidates, Votes Cast, Voter Turnout %
         */
        const ctx = document.getElementById('votingChart');
        if (ctx) {
            // Create new Chart instance with bar chart configuration
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total Students', 'Candidates', 'Votes Cast', 'Voter Turnout %'],
                    datasets: [{
                        label: 'Statistics',
                        // Data values from PHP variables
                        data: [<?php echo $total_students; ?>, <?php echo $total_candidates; ?>, <?php echo $total_votes; ?>, <?php echo $turnout; ?>],
                        // Bar colors (RGBA format with transparency)
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',  // Blue - Students
                            'rgba(39, 174, 96, 0.7)',   // Green - Candidates
                            'rgba(243, 156, 18, 0.7)',  // Orange - Votes
                            'rgba(155, 89, 182, 0.7)'   // Purple - Turnout
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
                            beginAtZero: true  // Start y-axis at 0
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>

