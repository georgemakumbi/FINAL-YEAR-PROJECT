# 🗳️ Kyambogo University Online Voting System

> A secure, web-based electronic voting platform for Kyambogo University student guild elections.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation & Setup](#installation--setup)
- [Database Setup](#database-setup)
- [Usage Guide](#usage-guide)
- [Security Features](#security-features)
- [API Reference](#api-reference)
- [Contributing](#contributing)
- [Authors](#authors)

---

## Overview

The **Kyambogo University Online Voting System** ("Kyambogo Decides") is designed to digitize
the student guild election process. It provides a secure, transparent, and user-friendly
platform for students to cast their votes and for administrators to manage elections.

### Problem Statement
Traditional paper-based voting at Kyambogo University is:
- Time-consuming and labor-intensive
- Prone to human error in counting
- Difficult to audit and verify
- Not accessible to all students

### Solution
This system provides:
- Secure online voting with one-vote-per-position enforcement
- Real-time result computation and visualization
- Comprehensive audit trails for transparency
- Role-based access control (Student, Admin, Super Admin)

---

## Features

### 👨‍🎓 Student Features
| Feature | Description |
|---------|-------------|
| **Secure Login** | Student ID + password authentication |
| **OTP Registration** | Email-based OTP verification for first-time setup |
| **Vote Casting** | Select candidates for each position with visual cards |
| **View Results** | See election results with percentage bars (when published) |
| **Apply for Candidacy** | Students can apply to run for positions |
| **Feedback** | Submit feedback about the voting experience |
| **Password Reset** | OTP-based password recovery |

### 🔧 Admin Features
| Feature | Description |
|---------|-------------|
| **Dashboard** | Overview of elections, candidates, and voters |
| **Election Management** | Create, schedule, activate, and close elections |
| **Candidate Management** | Add, edit, verify, or reject candidate applications |
| **Student Management** | Import students via CSV, manage accounts |
| **Results Publishing** | Control when results become visible |
| **Audit Logs** | View all system activities with filters |
| **Email Notifications** | Bulk email students about elections |
| **Election Reports** | Generate detailed election reports |

### 🔒 Security Features
| Feature | Implementation |
|---------|---------------|
| Password Hashing | bcrypt via `password_hash()` |
| SQL Injection Prevention | Prepared statements with parameter binding |
| CSRF Protection | Token-based form verification |
| XSS Prevention | `htmlspecialchars()` on all output |
| Session Security | HttpOnly cookies, SameSite=Lax, session regeneration |
| Vote Integrity | Database transactions with row-level locking |
| Audit Trail | All actions logged with user ID, IP, timestamp |

### 🔐 Anonymous Voting (Privacy)
The system records votes anonymously using a per-ballot `receipt_token` rather than storing `student_id` on the `votes` table. This preserves voter privacy while still allowing:
- vote verification via the `receipt_token` shown to a voter after casting their ballot;
- enforcement of one-vote-per-person at the application level using `students.has_voted`.

Note: The `votes` table therefore uses `receipt_token` as the unique vote identifier (together with `position`) and does not contain `student_id`.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Backend** | PHP 8.0+ |
| **Database** | MySQL 8.0 / MariaDB 10.4+ |
| **Server** | Apache (WAMP on Windows) |
| **Email** | SMTP via GMass relay |
| **Deployment** | Vercel (serverless PHP) |

---

## Project Structure

```
finalyearproject/
├── public/                    # 🌐 Web entry points (user-facing pages)
│   ├── index.php              # Homepage with countdown timer
│   ├── login.php              # Student login page
│   ├── admin_login.php        # Admin login page
│   ├── voting.php             # Vote casting interface
│   ├── results.php            # Election results display
│   └── ...                    # Other public pages
│
├── app/                       # ⚙️ Core application logic
│   ├── config/                # Configuration files
│   ├── controllers/           # Business logic (CRUD operations)
│   ├── middleware/             # Authentication & security
│   ├── services/              # Core services (voting, email, OTP)
│   └── utils/                 # Database connection & utilities
│
├── views/                     # 🎨 HTML templates & components
│   ├── admin/                 # Admin dashboard views
│   └── components/            # Reusable UI components
│
├── assets/                    # 📦 Static files
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript files
│   └── images/                # Images & candidate photos
│
├── database/                  # 🗄️ Database schema & migrations
│   └── schema.sql             # Complete database schema
│
├── storage/                   # 📁 Uploads & logs
│   ├── uploads/               # Candidate photos & CSV imports
│   └── logs/                  # Application error logs
│
├── docs/                      # 📚 Documentation
├── bootstrap.php              # Application bootstrap & path setup
├── .env                       # Environment variables (not in git)
└── .htaccess                  # Apache security configuration
```

---

## Installation & Setup

### Prerequisites
- [WAMP Server](https://www.wampserver.com/) (Windows) or XAMPP
- PHP 8.0 or higher
- MySQL 8.0 / MariaDB 10.4+
- Composer (PHP package manager)

### Step 1: Clone the Repository
```bash
cd c:\wamp64\www\
git clone <repository-url> finalyearproject
```

### Step 2: Install PHP Dependencies
```bash
cd finalyearproject
php composer.phar install
```

### Step 3: Configure Environment
Copy the `.env.example` file and edit it:
```bash
copy .env.example .env
```

Edit `.env` with your settings:
```env
DB_HOST=localhost
DB_NAME=kyambogo_voting
DB_USER=root
DB_PASS=
DB_PORT=3306

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password

FROM_EMAIL=your-email@gmail.com
FROM_NAME="Kyambogo Voting System"
```

### Step 4: Set Up the Database
1. Open phpMyAdmin at `http://localhost/phpmyadmin`
2. Create a new database called `kyambogo_voting`
3. Import `database/schema.sql`

Or via command line:
```bash
mysql -u root -e "CREATE DATABASE kyambogo_voting;"
mysql -u root kyambogo_voting < database/schema.sql
```

### Step 5: Access the System
- **Homepage**: `http://localhost/finalyearproject/public/`
- **Admin Login**: Press `Shift+A` on homepage, or visit `admin_login.html`
- **Default Admin**: Username: `admin`, Password: `password`

> ⚠️ **IMPORTANT**: Change the default admin password immediately after first login!

---

## Usage Guide

### For Students
1. Navigate to the homepage
2. Click "Login As Student"
3. Enter your Student ID and password
4. If first time, click "Register here" and verify with OTP
5. Select your preferred candidates for each position
6. Click "Submit Votes" — your votes are final!
7. View results when published by the administrator

### For Administrators
1. Press `Shift+A` on the homepage to access admin login
2. Log in with admin credentials
3. Use the dashboard to:
   - Create and manage elections
   - Add/verify candidates
   - Import students via CSV
   - Monitor voting progress
   - Publish results when ready
   - View audit logs

---

## Authors

**Kyambogo University BITC Students — Class of 2023**

- Built as a Final Year Project for the Bachelor of Information Technology & Computing program
- Supervised by the Department of Computer Science, Kyambogo University

---

## License

This project is developed for academic purposes at Kyambogo University.
All rights reserved © 2026 Kyambogo University.
