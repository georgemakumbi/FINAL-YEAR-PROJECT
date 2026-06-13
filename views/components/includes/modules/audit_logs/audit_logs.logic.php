<?php
/**
 * =============================================================================
 * AUDIT LOGS MODULE - LOGIC
 * =============================================================================
 * Handles all database operations and business logic for audit log management.
 * 
 * Responsibilities:
 * - Fetch audit log entries from database
 * - Handle filtering by date and action type
 * - Prepare data for audit logs view
 * - Statistics calculations
 * 
 * Dependencies:
 * - $conn (database connection)
 * 
 * =============================================================================
 */

// =============================================================================
// HANDLE LOG CLEARING
// =============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_audit_logs'])) {
    verify_csrf_or_die();
    if (isset($is_super_admin) && $is_super_admin) {
        $audit_log_table_exists = $conn->query("SHOW TABLES LIKE 'audit_log'")->num_rows > 0;
        if ($audit_log_table_exists) {
            $conn->query("TRUNCATE TABLE audit_log");
            header("Location: admin_dashboard.php?section=audit&success=" . urlencode("Audit logs cleared successfully."));
            exit();
        }
    } else {
        header("Location: admin_dashboard.php?section=audit&error=" . urlencode("Unauthorized action."));
        exit();
    }
}

// Initialize audit log variables
$audit_date = isset($_GET['audit_date']) ? $_GET['audit_date'] : '';
$audit_action = isset($_GET['audit_action']) ? $_GET['audit_action'] : '';
$audit_result = null;

// =============================================================================
// AUDIT LOG FILTERING AND FETCHING
// =============================================================================

// Validate audit date format
if ($audit_date !== '') {
    $date_obj = DateTime::createFromFormat('Y-m-d', $audit_date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $audit_date) {
        $audit_date = '';
    }
}

// Check if audit_log table exists
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
        $audit_conditions[] = "audit_entries.action IN ('DEADLINE_UPDATED', 'ELECTION_CREATED', 'ELECTION_STATUS_UPDATED', 'RESULTS_PUBLISHED', 'RESULTS_UNPUBLISHED', 'CANDIDATE_ADDED', 'CANDIDATE_UPDATED', 'CANDIDATE_DELETED', 'STUDENT_ADDED', 'STUDENT_UPDATED', 'STUDENT_DELETED', 'STUDENTS_IMPORTED')";
    } else {
        $audit_conditions[] = "1 = 0";
    }
}

$audit_where = '';
if (!empty($audit_conditions)) {
    $audit_where = "WHERE " . implode(" AND ", $audit_conditions);
}

if ($audit_log_table_exists) {
    $audit_source_query = "SELECT
            al.timestamp AS timestamp,
            al.action AS action,
            COALESCE(a.username, NULLIF(TRIM(CONCAT(st.first_name, ' ', st.last_name)), ''), al.user_id, 'Unknown') AS user,
            COALESCE(al.details, 'N/A') AS details
        FROM audit_log al
        LEFT JOIN admin a ON al.user_id = CAST(a.admin_id AS CHAR)
        LEFT JOIN students st ON al.user_id = st.student_id";
} else {
    $audit_source_query = "SELECT NOW() AS timestamp, 'NONE' AS action, 'System' AS user, 'No logs available' AS details";
}

$audit_query = "SELECT audit_entries.timestamp, audit_entries.action, audit_entries.user, audit_entries.details
    FROM ($audit_source_query) AS audit_entries
    $audit_where
    ORDER BY audit_entries.timestamp DESC
    LIMIT 100";

if (!empty($audit_params)) {
    $audit_stmt = $conn->prepare($audit_query);
    $audit_stmt->bind_param($audit_param_types, ...$audit_params);
    $audit_stmt->execute();
    $audit_result = $audit_stmt->get_result();
} else {
    $audit_result = $conn->query($audit_query);
}

// Count various action types
$vote_count = $conn->query("SELECT COUNT(*) as count FROM votes")->fetch_assoc()['count'];
$admin_action_count = $audit_log_table_exists 
    ? $conn->query("SELECT COUNT(*) as count FROM audit_log WHERE action NOT IN ('ADMIN_LOGIN', 'ADMIN_LOGOUT', 'STUDENT_LOGIN', 'STUDENT_LOGOUT', 'VOTE_CAST')")->fetch_assoc()['count']
    : 0;
$login_count = $audit_log_table_exists 
    ? $conn->query("SELECT COUNT(*) as count FROM audit_log WHERE action IN ('ADMIN_LOGIN', 'ADMIN_LOGOUT', 'STUDENT_LOGIN', 'STUDENT_LOGOUT')")->fetch_assoc()['count']
    : 0;

?>
