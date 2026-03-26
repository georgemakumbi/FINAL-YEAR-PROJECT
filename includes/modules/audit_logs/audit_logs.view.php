<?php
/**
 * =============================================================================
 * AUDIT LOGS MODULE - VIEW
 * =============================================================================
 * Renders the audit log interface.
 * 
 * Requires (set by audit_logs.logic.php):
 * - $audit_result (ResultSet)
 * - $audit_date (string)
 * - $audit_action (string)
 * - $vote_count (int)
 * - $admin_action_count (int)
 * - $login_count (int)
 * 
 * =============================================================================
 */
require_once __DIR__ . '/../common.php';
?>

<div class="card">
        <h2>🗞️ Audit Log</h2>
        
        <!-- Audit Log Statistics -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px;">
            <div style="padding: 15px; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #3498db;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">TOTAL VOTES LOGGED</div>
                <div style="font-size: 28px; font-weight: bold; color: #3498db;"><?php echo format_number($vote_count); ?></div>
            </div>
            <div style="padding: 15px; background: #f0f8f5; border-radius: 8px; border-left: 4px solid #27ae60;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">ADMIN ACTIONS</div>
                <div style="font-size: 28px; font-weight: bold; color: #27ae60;"><?php echo format_number($admin_action_count); ?></div>
            </div>
            <div style="padding: 15px; background: #ffe8e8; border-radius: 8px; border-left: 4px solid #e74c3c;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">LOGIN EVENTS</div>
                <div style="font-size: 28px; font-weight: bold; color: #e74c3c;"><?php echo format_number($login_count); ?></div>
            </div>
        </div>
        
        <!-- Audit Log Filter Form -->
        <div class="search-box">
            <form method="get" style="display: flex; gap: 10px; width: 100%;">
                <input type="hidden" name="section" value="audit">
                <input type="date" name="audit_date" value="<?php echo safe_output($audit_date); ?>">
                <select name="audit_action">
                    <option value="" <?php echo $audit_action === '' ? 'selected' : ''; ?>>All Actions</option>
                    <option value="vote" <?php echo $audit_action === 'vote' ? 'selected' : ''; ?>>Vote Cast</option>
                    <option value="login" <?php echo $audit_action === 'login' ? 'selected' : ''; ?>>Login</option>
                    <option value="admin" <?php echo $audit_action === 'admin' ? 'selected' : ''; ?>>Admin Action</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if ($audit_date || $audit_action !== ''): ?>
                    <a href="admin_dashboard.php?section=audit" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
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
                            <td><?php echo safe_output($log['timestamp']); ?></td>
                            <td><?php echo render_status_badge('active', safe_output($log['action'])); ?></td>
                            <td><?php echo safe_output($log['user']); ?></td>
                            <td><?php echo safe_output($log['details']); ?></td>
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
