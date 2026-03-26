<?php
/**
 * =============================================================================
 * ELECTIONS MODULE - VIEW
 * =============================================================================
 * Renders the elections management interface.
 * 
 * Requires (set by elections.logic.php):
 * - $elections (ResultSet)
 * - $elections_message (string)
 * - $elections_message_type (string)
 * - $is_super_admin (bool)
 * - $active_elections_count (int)
 * 
 * =============================================================================
 */
?>

<div class="card">
        <h2>🗳️ Manage Elections</h2>
        
        <!-- Display messages -->
        <?php if ($elections_message): ?>
            <?php echo render_alert($elections_message_type, $elections_message); ?>
        <?php endif; ?>
        
        <!-- Create Election Form -->
        <div style="margin-bottom: 30px; padding: 20px; background: var(--surface-2); border-radius: 10px;">
            <h3>Create New Election</h3>
            <form method="post">
                <?php echo render_csrf_field(); ?>
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
                            <optgroup label="GRCs">
                                <option value="Faculty of Science">Faculty of Science</option>
                                <option value="Faculty of Engineering">Faculty of Engineering</option>
                                <option value="Faculty of Social sciences">Faculty of Social sciences</option>
                            </optgroup>
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
        
        <!-- Elections Statistics -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px;">
            <div style="padding: 15px; background: var(--surface-2); border-radius: 8px; border-left: 4px solid var(--brand-primary);">
                <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">ACTIVE ELECTIONS</div>
                <div style="font-size: 28px; font-weight: bold; color: var(--brand-primary);"><?php echo format_number($active_elections_count); ?></div>
            </div>
            <div style="padding: 15px; background: var(--surface-2); border-radius: 8px; border-left: 4px solid var(--brand-accent);">
                <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">SCHEDULED ELECTIONS</div>
                <div style="font-size: 28px; font-weight: bold; color: var(--brand-accent);"><?php echo format_number($scheduled_elections_count); ?></div>
            </div>
            <div style="padding: 15px; background: var(--surface-2); border-radius: 8px; border-left: 4px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">CLOSED ELECTIONS</div>
                <div style="font-size: 28px; font-weight: bold; color: var(--border);"><?php echo format_number($closed_elections_count); ?></div>
            </div>
        </div>
        
        <!-- Elections Table -->
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
                $table_exists = $conn->query("SHOW TABLES LIKE 'elections'")->num_rows > 0;
                
                if ($table_exists && $elections && $elections->num_rows > 0):
                    while ($election = $elections->fetch_assoc()): 
                        $status_class = ($election['status'] === 'active') ? 'status-active' : 
                                       (($election['status'] === 'closed') ? 'status-closed' : 'status-scheduled');
                        $status_text = ucfirst($election['status']);
                ?>
                    <tr>
                        <td><?php echo safe_output($election['election_title']); ?></td>
                        <td><?php echo safe_output($election['position']); ?></td>
                        <td><?php echo safe_output($election['start_date']); ?></td>
                        <td><?php echo safe_output($election['end_date']); ?></td>
                        <td><?php echo render_status_badge($election['status'], $status_text); ?></td>
                        <td class="action-btns">
                            <?php if ($is_super_admin && $election['status'] == 'scheduled'): ?>
                                <form method="post" style="display:inline;">
                                    <?php echo render_csrf_field(); ?>
                                    <input type="hidden" name="update_election" value="1">
                                    <input type="hidden" name="election_id" value="<?php echo (int)$election['election_id']; ?>">
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success btn-small">Start</button>
                                </form>
                            <?php elseif ($is_super_admin && $election['status'] == 'active'): ?>
                                <form method="post" style="display:inline;">
                                    <?php echo render_csrf_field(); ?>
                                    <input type="hidden" name="update_election" value="1">
                                    <input type="hidden" name="election_id" value="<?php echo (int)$election['election_id']; ?>">
                                    <input type="hidden" name="status" value="closed">
                                    <button type="submit" class="btn btn-warning btn-small">Close</button>
                                </form>
                            <?php endif; ?>
                            <a href="election_report.php?election_id=<?php echo (int)$election['election_id']; ?>"
                               class="btn btn-primary btn-small">Report</a>
                            <a href="edit_election.php?id=<?php echo safe_output($election['election_id']); ?>" 
                               class="btn btn-primary btn-small">Edit</a>
                        </td>
                    </tr>
                <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No elections found. Create one above.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php require_once __DIR__ . '/../common.php'; ?>
