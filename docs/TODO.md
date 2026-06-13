# Notification System Implementation TODO

## Status: In Progress

### Step 1: [DONE] Create TODO.md
- Create this file with step-by-step tasks.

### Step 2: Create send_notifications.php (root level)
- New PHP file for handling bulk notifications.
- Validate super_admin & CSRF.
- Process selected student_ids[], send emails via SMTP.
- Log to audit, redirect with success count.

### Step 3: Update includes/modules/students/students.logic.php
- Add variables for selected students & message prep.
- Handle POST processing if needed.

### Step 4: [DONE] Update includes/modules/students/students.view.php  
- Add checkbox column to students table.
- Add 'Select All' functionality.
- Add notification form (textarea + send button).
- Only visible to super_admin.

### Step 5: Test Implementation
- Add test students.
- Go to admin_dashboard.php?section=students.
- Select students, send test notification.
- Verify emails in MailHog (localhost:8025).
- Check audit logs.

### Step 6: Complete
- Update this TODO with completion status.
- Run attempt_completion.

**Next: Step 5 (Testing)**

### Step 2: [DONE]
### Step 3: [DONE] 
### Step 4: [DONE]
