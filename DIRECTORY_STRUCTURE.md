# Project Directory Structure

```
finalyearproject/
├── public/                          # Web-accessible entry points
│   ├── index.php                    # Homepage
│   ├── login.html                   # Student login
│   ├── admin_login.html             # Admin login
│   ├── voting.php                   # Voting interface
│   ├── results.php                  # View results
│   ├── feedback.php                 # Feedback submission
│   ├── view_candidates.php          # View candidates list
│   ├── view_feedback.php            # View feedback
│   ├── about_us.php                 # About page
│   ├── countdown.php                # Countdown timer
│   └── election_report.php          # Election report
│
├── app/                             # Core application logic
│   ├── config/
│   │   ├── composer.json            # PHP dependencies
│   │   ├── database.sql             # Database schema (moved to database/)
│   │   ├── deadline.txt             # Election deadline
│   │   └── results_status.txt       # Results status
│   │
│   ├── controllers/                 # Business logic & operations
│   │   ├── add_candidate.php
│   │   ├── add_student.php
│   │   ├── add_department_columns.php
│   │   ├── edit_candidate.php
│   │   ├── edit_election.php
│   │   ├── delete_candidate.php
│   │   ├── delete.php
│   │   ├── apply_candidate.php
│   │   └── import_students.php
│   │
│   ├── middleware/                  # Authentication & Security
│   │   ├── authenticate.php         # Student authentication
│   │   ├── admin_authenticate.php   # Admin authentication
│   │   ├── admin_security.php       # Admin security checks
│   │   ├── admin_logout.php
│   │   ├── logout.php
│   │   ├── forgot_password.php
│   │   └── reset_password.php
│   │
│   ├── services/                    # Core services
│   │   ├── send_notifications.php   # Email notifications
│   │   ├── send_otp.php             # OTP sending
│   │   ├── verify_otp.php           # OTP verification
│   │   ├── processvote.php          # Vote processing
│   │   ├── password.php             # Password management
│   │   ├── update.php               # Generic updates
│   │   └── update_db.php            # Database updates
│   │
│   └── utils/                       # Database & utilities
│       ├── db_connection.php        # Database connection
│       └── smtp_mailer.php          # Email sending utility
│
├── views/                           # HTML Templates & Layouts
│   ├── admin/                       # Admin dashboard views
│   │   ├── admin_dashboard.php
│   │   └── admin_dashboard.backup.php
│   │
│   ├── student/                     # Student views (empty - see public/)
│   │
│   ├── auth/                        # Authentication views (empty - see public/)
│   │
│   └── components/                  # Reusable components
│       └── includes/                # Modular view includes
│           ├── audit_logger.php
│           ├── results_publish.php
│           ├── theme.js
│           └── modules/
│               ├── common.php
│               ├── audit_logs/
│               ├── candidates/
│               ├── elections/
│               ├── feedback/
│               ├── settings/
│               └── students/
│
├── assets/                          # Static files
│   ├── css/                         # Stylesheets
│   │   ├── about_us.css
│   │   ├── admin_dashboard.css
│   │   ├── election_report.css
│   │   ├── feedback.css
│   │   ├── home.css
│   │   ├── index.css
│   │   ├── login.css
│   │   ├── results.css
│   │   ├── theme.css
│   │   ├── update.css
│   │   └── voting.css
│   │
│   ├── images/                      # Images & candidate photos
│   │   └── (candidate images)
│   │
│   └── js/                          # JavaScript files
│       └── theme.js
│
├── database/                        # Database files
│   ├── database.sql                 # Database schema
│   └── kyambogo_voting_system/      # Visual Studio SQL project
│
├── storage/                         # Writable directories
│   ├── logs/                        # Application logs (empty)
│   ├── uploads/
│   │   ├── candidates/              # Candidate files
│   │   └── kyambogo_students database.csv  # Student data
│   └── exports/                     # Exports (empty)
│
├── docs/                            # Documentation
│   ├── ADMIN_DASHBOARD_MODULAR_GUIDE.md
│   ├── TODO.md
│   └── authenticate.py
│
├── .git/                            # Git repository
└── (legacy files may still be tracked in git)
```

## Directory Organization Summary

| Directory | Purpose | Files |
|-----------|---------|-------|
| `public/` | Web-accessible entry points | 11 PHP files |
| `app/config/` | Configuration & settings | Database, deadline, status |
| `app/controllers/` | Business logic & data operations | 9 PHP files |
| `app/middleware/` | Authentication & security | 7 PHP files |
| `app/services/` | Core services (notifications, voting, etc) | 7 PHP files |
| `app/utils/` | Database & utility functions | 2 PHP files |
| `views/admin/` | Admin dashboard templates | 3 PHP files |
| `views/components/` | Reusable view components | Modular includes |
| `assets/css/` | Stylesheets | 10 CSS files |
| `assets/images/` | Images & photos | Candidate images |
| `database/` | Database schema & project files | database.sql |
| `storage/logs/` | Application logs | (writable directory) |
| `storage/uploads/` | Uploaded files & data | CSV data, candidate files |
| `docs/` | Documentation & guides | 3 files |

## How to Update Code

When moving forward with updates, remember that files are now organized as follows:

### To access database connection:
```php
require 'app/utils/db_connection.php';
```

### To use authentication:
```php
require 'app/middleware/authenticate.php';
```

### To access services:
```php
require 'app/services/send_notifications.php';
```

### For public-facing pages:
All files in `public/` are directly accessible via the web.

## Next Steps

1. Update all require/include paths in files to point to new locations
2. Create a `.htaccess` in `public/` to route all requests through index.php
3. Create a centralized bootstrap/loader file in root or public/
4. Move remaining legacy code and refactor as needed

