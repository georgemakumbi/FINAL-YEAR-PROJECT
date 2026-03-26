# Admin Dashboard Modular Refactoring

## Overview

The admin dashboard has been refactored from a monolithic 1000+ line file into a clean, modular architecture with separate logic and view layers. This improves maintainability, testability, and code organization.

## Directory Structure

```
finalyearproject/
├── admin_dashboard.php                 (Main router/controller)
├── admin_dashboard.backup.php          (Original backup)
└── includes/modules/
    ├── common.php                      (Shared utilities)
    ├── elections/
    │   ├── elections.logic.php         (Elections business logic)
    │   └── elections.view.php          (Elections HTML view)
    ├── students/
    │   ├── students.logic.php          (Students business logic)
    │   └── students.view.php           (Students HTML view)
    ├── candidates/
    │   ├── candidates.logic.php        (Candidates business logic)
    │   └── candidates.view.php         (Candidates HTML view)
    ├── audit_logs/
    │   ├── audit_logs.logic.php        (Audit logs business logic)
    │   └── audit_logs.view.php         (Audit logs HTML view)
    └── feedback/
        ├── feedback.logic.php          (Feedback business logic)
        └── feedback.view.php           (Feedback HTML view)
```

## Architecture Patterns

### 1. **Main Router** (`admin_dashboard.php`)

**Responsibility:** 
- Authentication and session management
- Central orchestrator for module loading
- Dashboard overview rendering
- Page layout (header, sidebar, footer)
- Message handling and CSRF token management

**Key Functions:**
```php
// Routes to modules based on section parameter
$section = $_GET['section'] ?? 'dashboard';

// Loads appropriate module logic
include_once 'includes/modules/'.$section.'/'.$section.'.logic.php';

// Renders appropriate module view
include_once 'includes/modules/'.$section.'/'.$section.'.view.php';
```

### 2. **Logic Files** (`.logic.php`)

**Responsibility:**
- Database queries and data fetching
- Form processing and validation
- Business logic calculations
- Preparing data for views

**Pattern:**
```php
<?php
// 1. Initialize variables
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// 2. Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process form, validate, execute queries
}

// 3. Fetch data from database
$data = $conn->query("SELECT * FROM table");

// 4. Calculate statistics
$total = $data->num_rows;
?>
```

### 3. **View Files** (`.view.php`)

**Responsibility:**
- HTML rendering
- Data display with proper escaping
- Form generation
- User interface

**Pattern:**
```php
<div id="section-name" class="section">
    <!-- Use safe_output() for escaping -->
    <?php echo safe_output($variable); ?>
    
    <!-- Use render_* functions for common elements -->
    <?php echo render_status_badge($status); ?>
</div>
```

## Common Utilities (`common.php`)

Provides shared functions available to all modules:

### Status Functions
- `render_status_badge($status, $display_text)` - Renders status badge HTML
- `render_alert($type, $message)` - Renders alert messages

### Output Functions
- `safe_output($value)` - HTML-safe escaping
- `format_number($number)` - Format numbers with thousands separator
- `format_datetime($datetime, $format)` - Format dates

### Form Functions
- `render_button($text, $type, $classes, $attributes)` - Render button element
- `render_csrf_field()` - Render hidden CSRF token input

### HTML Functions
- `is_super_admin()` - Check admin permissions
- `get_csrf_token()` - Get CSRF token from session

## Module Breakdown

### Elections Module
**Files:**
- `elections.logic.php` - Create/update elections, fetch election data
- `elections.view.php` - Display elections management interface

**Features:**
- Create new elections
- Update election status (scheduled → active → closed)
- Display election statistics
- Election table with actions

---

### Students Module
**Files:**
- `students.logic.php` - Search students, fetch student data
- `students.view.php` - Display student management interface

**Features:**
- Add individual students
- Bulk import from CSV
- Search students by ID, name, email
- Student statistics (total, voted, not voted)
- Edit/delete student actions

---

### Candidates Module
**Files:**
- `candidates.logic.php` - Fetch candidates, calculate statistics
- `candidates.view.php` - Display candidate management interface

**Features:**
- Add new candidates with photo upload
- Display candidate statistics
- Show votes and vote percentages
- Edit/delete candidate actions
- University-wide vs department-specific positions

---

### Audit Logs Module
**Files:**
- `audit_logs.logic.php` - Fetch audit logs with filtering
- `audit_logs.view.php` - Display audit logs interface

**Features:**
- Filter logs by date and action type
- Display action history
- Show user information
- Log statistics (votes, admin actions, logins)

---

### Feedback Module
**Files:**
- `feedback.logic.php` - Fetch feedback entries
- `feedback.view.php` - Display feedback interface

**Features:**
- Display all student feedback
- Show student names with feedback
- Display feedback submission dates
- Feedback statistics

---

## Adding a New Module

To add a new module (e.g., "reports"):

### Step 1: Create Directory
```bash
mkdir includes/modules/reports
```

### Step 2: Create Logic File (`reports.logic.php`)
```php
<?php
// Fetch data, handle forms
$report_data = $conn->query("SELECT ...");
?>
```

### Step 3: Create View File (`reports.view.php`)
```php
<?php require_once __DIR__ . '/../common.php'; ?>
<div id="reports" class="section">
    <!-- HTML rendering -->
</div>
```

### Step 4: Update Router (`admin_dashboard.php`)
```php
// In module logic loading section
case 'reports':
    include_once 'includes/modules/reports/reports.logic.php';
    break;

// In module rendering section
<?php if ($section === 'reports'): ?>
    <?php include_once 'includes/modules/reports/reports.view.php'; ?>
<?php endif; ?>

// In sidebar navigation
<li class="nav-item" onclick="showSection('reports')">📊 Reports</li>
```

## Security Considerations

### CSRF Protection
- All forms use `render_csrf_field()` helper
- Token verified via `verify_csrf_or_die()` in logic files

### SQL Injection Prevention
- Use prepared statements: `$conn->prepare()` with `bind_param()`
- Use `safe_output()` or `escape_string()` where needed

### XSS Prevention
- Always use `safe_output()` for user-generated content
- Use `htmlspecialchars()` for HTML attributes
- Use `nl2br()` for preserving line breaks

### Access Control
- Use `is_super_admin()` to check permissions
- Modules require `$is_super_admin` to be set (from router)
- Admin login required via `require_admin_login()`

## Best Practices

### In Logic Files
✅ Do:
- Initialize all variables at the top
- Use prepared statements for queries
- Set clear variable names for views
- Add comments for complex logic
- Validate form inputs

❌ Don't:
- Include HTML/output statements
- Use `echo` or `print` statements
- Directly output database results without assignment
- Mix display logic with business logic

### In View Files
✅ Do:
- Use `safe_output()` for variable output
- Use helper functions from `common.php`
- Keep logic minimal
- Write clean, readable HTML
- Include proper `require` statement for common.php

❌ Don't:
- Include database queries directly
- Process form submissions
- Make calculations
- Output without escaping
- Use `<?php ?>` tags unnecessarily

### In common.php
✅ Do:
- Create reusable utility functions
- Keep function names descriptive
- Add PHPDoc comments
- Return HTML strings for rendering functions

❌ Don't:
- Add module-specific logic
- Create functions used by only one module
- Echo directly (return instead)
- Mix concerns in a single function

## Important Notes

⚠️ **Backward Compatibility:**
- Original `admin_dashboard.php` backed up as `admin_dashboard.backup.php`
- All external links to `admin_dashboard.php` still work
- Module routing via `?section=` parameter

⚠️ **Performance:**
- Modules load only one logic file per request
- Unused modules are not included
- Database queries only execute for active module

⚠️ **Maintenance:**
- Each module is independent and testable
- Changes to one module don't affect others
- Easy to add new modules or remove old ones
- Centralized utilities reduce code duplication

## Testing

### Manual Testing Checklist
- [ ] Dashboard loads correctly
- [ ] All module links navigate properly
- [ ] Forms submit without errors
- [ ] CSRF tokens work
- [ ] Messages display correctly
- [ ] Search/filter functions work
- [ ] Statistics calculations are accurate
- [ ] Images/assets load properly

### Database Queries
All major queries in each logic file:

**Elections:**
- `SELECT * FROM elections ORDER BY start_date DESC`
- `SELECT COUNT(*) FROM elections WHERE status = 'active'`

**Students:**
- `SELECT * FROM students ORDER BY registration_date DESC`
- `SELECT * FROM students WHERE student_id LIKE ?`

**Candidates:**
- `SELECT * FROM candidates ORDER BY votes DESC`
- `SELECT SUM(votes) FROM candidates`

**Audit Logs:**
- Merged query from `votes` and `audit_log` tables
- Filtering by date and action type

**Feedback:**
- `SELECT * FROM feedback LEFT JOIN students ...`

## Future Improvements

- [ ] Create base Module class for shared functionality
- [ ] Implement dependency injection for cleaner module loading
- [ ] Add module configuration files (permissions, routes)
- [ ] Create module loader/dispatcher class
- [ ] Add unit tests for module logic
- [ ] Implement pagination for large result sets
- [ ] Add module-level caching
- [ ] Create admin audit log for dashboard actions

## Version Info

- **Refactoring Version:** 2.0
- **Date:** March 2026
- **Modules:** 5 (Elections, Students, Candidates, Audit Logs, Feedback)
- **Common Utilities:** 12 functions
- **Total Lines Reduced:** ~200 lines (improved maintainability)
