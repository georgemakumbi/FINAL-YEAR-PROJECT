<?php
/**
 * =============================================================================
 * STUDENTS MODULE - VIEW
 * =============================================================================
 * Renders the student management interface.
 * 
 * Requires (set by students.logic.php):
 * - $search_term (string)
 * - $students (ResultSet)
 * - $is_super_admin (bool)
 * - $total_students (int)
 * - $students_voted (int)
 * - $students_not_voted (int)
 * 
 * =============================================================================
 */
require_once __DIR__ . '/../common.php';
?>

<div class="card">
        <h2>🧑‍🎓 Student Management</h2>
        
        <!-- Search Box -->
        <div class="search-box">
            <form method="get" style="display: flex; gap: 10px; width: 100%;">
                <input type="text" name="search" placeholder="Search by registration number or name..." 
                       value="<?php echo safe_output($search_term); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if ($search_term): ?>
                    <a href="admin_dashboard.php?section=students" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Student Statistics -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px;">
            <div style="padding: 15px; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #3498db;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">TOTAL STUDENTS</div>
                <div style="font-size: 28px; font-weight: bold; color: #3498db;"><?php echo format_number($total_students); ?></div>
            </div>
            <div style="padding: 15px; background: #f0f8f5; border-radius: 8px; border-left: 4px solid #27ae60;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">HAVE VOTED</div>
                <div style="font-size: 28px; font-weight: bold; color: #27ae60;"><?php echo format_number($students_voted); ?></div>
            </div>
            <div style="padding: 15px; background: #ffe8e8; border-radius: 8px; border-left: 4px solid #e74c3c;">
                <div style="font-size: 12px; color: #666; font-weight: 500;">NOT YET VOTED</div>
                <div style="font-size: 28px; font-weight: bold; color: #e74c3c;"><?php echo format_number($students_not_voted); ?></div>
            </div>
        </div>

        <!-- Add New Student Form - Only visible to super_admin -->
        <?php if ($is_super_admin): ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- Individual Student Addition -->
            <div>
                <h3>➕ Add New Student</h3>
                <form action="add_student.php" method="post">
                    <?php echo render_csrf_field(); ?>
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
                    <?php echo render_csrf_field(); ?>
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
                            </code>
                        </div>
                    </details>
                    <button type="submit" name="import_students" class="btn btn-success" style="width: 100%;">Import Students</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div style="margin-top: 20px; padding: 16px; background: var(--surface-2); border: 1px dashed var(--border); border-radius: 8px;">
            <h3>CSV Import Restricted</h3>
            <p style="color: var(--text-muted);">
                Bulk student import is available only to super administrators.
            </p>
        </div>
        <?php endif; ?>
    </div>
<!-- Student List Table -->
<div class="card">
        <h3>📋 Student List <?php echo $search_term ? '(Search Results)' : ''; ?></h3>
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-students" onclick="toggleSelectAll(this)"> Select</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Faculty</th>
                    <th>Voted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students && $students->num_rows > 0):
                    while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" name="student_ids[]" value="<?php echo safe_output($student['student_id']); ?>"></td>
                            <td><?php echo safe_output($student['student_id']); ?></td>
                            <td><?php echo safe_output($student['first_name'] . ' ' . $student['last_name']); ?></td>
                            <td><?php echo safe_output($student['email']); ?></td>
                            <td><?php echo safe_output($student['faculty']); ?></td>
                            <td>
                                <?php echo render_status_badge(
                                    $student['has_voted'] ? 'active' : 'scheduled',
                                    $student['has_voted'] ? 'Yes' : 'No'
                                ); ?>
                            </td>
                            <td class="action-links">
                                <?php if ($is_super_admin): ?>
                                <a href="update.php?id=<?php echo safe_output($student['student_id']); ?>"><button class="btn btn-primary btn-small">Edit</button></a>
                                <form action="delete.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    <?php echo render_csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo safe_output($student['student_id']); ?>">
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
        
        <?php if ($is_super_admin): ?>
        <!-- Bulk Notification Form -->
        <div class="card" style="margin-top: 20px;">
            <h3>📧 Send Bulk Notification <span id="selected-count" style="color: #3498db; font-weight: bold;">(0 selected)</span></h3>
            <p style="color: #666; margin-bottom: 15px;">Select students above, then enter your message to send email notifications.</p>
            <form action="send_notifications.php" method="post" id="notification-form">
                <?php echo render_csrf_field(); ?>
                <div class="form-group">
                    <label>Notification Message</label>
                    <textarea name="notification_message" rows="5" class="form-control" 
                              placeholder="e.g., Election voting ends tomorrow! Please cast your vote." 
                              style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif;"><?php echo safe_output($notification_message); ?></textarea>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 15px;">
                    <button type="submit" class="btn btn-primary" id="send-notification-btn" disabled>
                        📤 Send Notifications
                    </button>
                    <span id="preview-count" style="color: #666;"></span>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script>
    // Select All functionality
    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        updateSelectedCount();
    }

    // Update count and enable/disable send button
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('input[name="student_ids[]"]:checked');
        const count = checkboxes.length;
        document.getElementById('selected-count').textContent = `(${count} selected)`;
        document.getElementById('preview-count').textContent = count > 0 ? `${count} students will receive this notification` : '';
        document.getElementById('send-notification-btn').disabled = count === 0;
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        updateSelectedCount();

        // ── CRITICAL FIX ──────────────────────────────────────────────────────
        // The student checkboxes live OUTSIDE the notification <form>, so they
        // are never included in the POST automatically.  Before submission we
        // inject each selected student_id as a hidden <input> into the form.
        // ─────────────────────────────────────────────────────────────────────
        document.getElementById('notification-form').addEventListener('submit', function(e) {
            const form = this;

            // Re-read currently checked boxes (they're in the table, outside the form)
            const checked = document.querySelectorAll('input[name="student_ids[]"]:checked');

            // Guard: at least one student must be selected
            if (checked.length === 0) {
                e.preventDefault();
                alert('Please select at least one student before sending.');
                return;
            }

            // Guard: message must not be empty
            const msg = form.querySelector('textarea[name="notification_message"]').value.trim();
            if (!msg) {
                e.preventDefault();
                alert('Please enter a notification message.');
                return;
            }

            // Remove any hidden inputs injected by a previous (failed) attempt
            form.querySelectorAll('input[type="hidden"][data-injected="1"]').forEach(el => el.remove());

            // Inject a hidden input for every selected student
            checked.forEach(function(checkbox) {
                const hidden = document.createElement('input');
                hidden.type  = 'hidden';
                hidden.name  = 'student_ids[]';
                hidden.value = checkbox.value;
                hidden.dataset.injected = '1';
                form.appendChild(hidden);
            });

            // Disable the button to prevent double-submit
            document.getElementById('send-notification-btn').disabled = true;
            document.getElementById('send-notification-btn').textContent = '⏳ Sending...';
        });
    });
    </script>

