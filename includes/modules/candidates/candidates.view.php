<?php
/**
 * =============================================================================
 * CANDIDATES MODULE - VIEW
 * =============================================================================
 * Renders the candidate management interface.
 * 
 * Requires (set by candidates.logic.php):
 * - $candidates (ResultSet)
 * - $is_super_admin (bool)
 * - $total_candidates (int)
 * - $total_candidates_votes (int)
 * - $positions_with_candidates (ResultSet)
 * 
 * =============================================================================
 */
require_once __DIR__ . '/../common.php';
?>

<div id="candidates" class="section">
    <div class="card">
        <h2>👥 Manage Candidates</h2>
        
        <!-- Display messages -->
        <?php if ($candidates_message): ?>
            <?php echo render_alert($candidates_message_type, $candidates_message); ?>
        <?php endif; ?>
        
        <!-- Candidate Statistics -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 30px;">
            <div style="padding: 15px; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #3498db;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">TOTAL CANDIDATES</div>
                <div style="font-size: 28px; font-weight: bold; color: #3498db;"><?php echo format_number($total_candidates); ?></div>
            </div>
            <div style="padding: 15px; background: #f0f8f5; border-radius: 8px; border-left: 4px solid #27ae60;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">TOTAL VOTES</div>
                <div style="font-size: 28px; font-weight: bold; color: #27ae60;"><?php echo format_number($total_candidates_votes); ?></div>
            </div>
        </div>
        
        <!-- Add New Candidate Form - Only visible to super_admin -->
        <?php if ($is_super_admin): ?>
        <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <h3>Add New Candidate</h3>
            <form action="add_candidate.php" method="post" enctype="multipart/form-data">
                <?php echo render_csrf_field(); ?>
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
                        $image_src = $candidate['image_path'] ? $candidate['image_path'] : 'images/placeholder.png';
                        $scope = (isset($candidate['is_university_wide']) && $candidate['is_university_wide'] == 1) 
                            ? '<span class="status-badge status-active">University-wide</span>' 
                            : '<span class="status-badge status-scheduled">' . safe_output($candidate['department'] ?? 'N/A') . '</span>';
                    ?>
                        <tr>
                            <td>
                                <?php if (file_exists($image_src)): ?>
                                    <img src="<?php echo safe_output($image_src); ?>" class="candidate-img" alt="Candidate">
                                <?php else: ?>
                                    <div class="candidate-img" style="background: #ddd; display: flex; align-items: center; justify-content: center;">
                                        <?php echo strtoupper(substr($candidate['first_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo safe_output($candidate['first_name'] . ' ' . $candidate['last_name']); ?></td>
                            <td><?php echo safe_output($candidate['position']); ?></td>
                            <td><?php echo safe_output($candidate['faculty']); ?></td>
                            <td><?php echo safe_output($candidate['department'] ?? 'N/A'); ?></td>
                            <td><?php echo $scope; ?></td>
                            <td><strong><?php echo format_number($candidate['votes']); ?></strong></td>
                            <td class="action-links">
                                <?php if ($is_super_admin): ?>
                                <a href="edit_candidate.php?id=<?php echo safe_output($candidate['candidate_id']); ?>">Edit</a>
                                <form action="delete_candidate.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                    <?php echo render_csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$candidate['candidate_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-small">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No candidates found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
