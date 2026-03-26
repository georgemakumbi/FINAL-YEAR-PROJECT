<?php
/**
 * =============================================================================
 * FEEDBACK MODULE - VIEW
 * =============================================================================
 * Renders the feedback viewing interface.
 * 
 * Requires (set by feedback.logic.php):
 * - $feedback_entries (ResultSet)
 * - $feedback_table_exists (bool)
 * - $total_feedback (int)
 * 
 * =============================================================================
 */
require_once __DIR__ . '/../common.php';
?>

<div class="card">
        <h2>💬 Student Feedback</h2>
        
        <!-- Feedback Statistics -->
        <?php if ($feedback_table_exists): ?>
            <div style="padding: 15px; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #3498db; margin-bottom: 30px;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">TOTAL FEEDBACK ENTRIES</div>
                <div style="font-size: 28px; font-weight: bold; color: #3498db;"><?php echo format_number($total_feedback); ?></div>
            </div>
        <?php endif; ?>
        
        <!-- Feedback Table -->
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Feedback</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$feedback_table_exists): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">
                            <em>Feedback table not found in database.</em>
                        </td>
                    </tr>
                <?php elseif ($feedback_entries && $feedback_entries->num_rows > 0): ?>
                    <?php while ($entry = $feedback_entries->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo safe_output($entry['student_id']); ?></td>
                            <td>
                                <?php
                                $student_name = trim(($entry['first_name'] ?? '') . ' ' . ($entry['last_name'] ?? ''));
                                echo safe_output($student_name !== '' ? $student_name : 'Unknown Student');
                                ?>
                            </td>
                            <td style="word-wrap: break-word;">
                                <?php echo nl2br(safe_output($entry['feedback'])); ?>
                            </td>
                            <td><?php echo safe_output($entry['feedback_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No feedback available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
