-- =============================================================================
-- KYAMBOGO UNIVERSITY ONLINE VOTING SYSTEM — Database Schema
-- =============================================================================
-- Version: 2.0
-- Database: MySQL 8.0+ / MariaDB 10.4+
-- Charset:  utf8mb4 (supports emojis and all Unicode characters)
-- 
-- HOW TO USE:
--   1. Create the database:  CREATE DATABASE kyambogo_voting;
--   2. Select it:            USE kyambogo_voting;
--   3. Run this file:        SOURCE schema.sql;
--
-- WHAT THIS FILE DOES:
--   This file creates all the tables your voting system needs.
--   Think of tables like spreadsheets — each one stores a different
--   type of data (students, candidates, votes, etc.)
-- =============================================================================


-- =============================================================================
-- TABLE 1: students
-- =============================================================================
-- PURPOSE: Stores every registered student's information.
-- 
-- WHY EACH COLUMN EXISTS:
--   student_id    → The student's university ID (e.g., "23/U/001")
--                   This is the PRIMARY KEY — unique identifier for each row.
--   first_name    → Student's first name (used in "Welcome, George")
--   last_name     → Student's surname
--   email         → Must be unique — used for OTP and notifications
--   password_hash → NEVER store plain passwords! This stores bcrypt hash
--                   Example: "$2y$10$92IXUNpk..." (one-way, can't be reversed)
--   faculty       → Student's faculty (e.g., "Engineering")
--   department    → Student's department — used to filter which candidates
--                   they can vote for (department-specific positions)
--   has_voted     → Boolean flag: FALSE = hasn't voted, TRUE = already voted
--                   Prevents double-voting at the application level
--   otp           → 6-digit one-time password for registration/password reset
--   otp_expiry    → When the OTP expires (usually 10 minutes after creation)
--   reset_token   → Random token for password reset links
--   reset_expires → When the reset token expires
--   registration_date → When the student registered (auto-filled)
-- =============================================================================
CREATE TABLE IF NOT EXISTS students (
    student_id        VARCHAR(20)   PRIMARY KEY,
    first_name        VARCHAR(50)   NULL DEFAULT NULL,   -- Nullable: admin imports may omit name
    last_name         VARCHAR(50)   NULL DEFAULT NULL,
    email             VARCHAR(100)  UNIQUE NOT NULL,
    password_hash     VARCHAR(255)  NULL DEFAULT NULL,   -- Nullable: set only after OTP-verified registration
    faculty           VARCHAR(100)  NOT NULL,
    department        VARCHAR(100)  NOT NULL,
    has_voted         BOOLEAN       DEFAULT FALSE,
    is_registered     BOOLEAN       DEFAULT FALSE,       -- TRUE once student completes OTP registration
    otp               VARCHAR(6)    DEFAULT NULL,
    otp_expiry        DATETIME      DEFAULT NULL,
    reset_token       VARCHAR(255)  DEFAULT NULL,
    reset_expires     DATETIME      DEFAULT NULL,
    registration_date DATETIME      DEFAULT CURRENT_TIMESTAMP,

    -- INDEXES: Make searches faster (like a book's index)
    INDEX idx_email (email),
    INDEX idx_otp (otp),
    INDEX idx_faculty (faculty),
    INDEX idx_department (department),
    INDEX idx_is_registered (is_registered)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLE 2: admin
-- =============================================================================
-- PURPOSE: Stores administrator accounts who manage the voting system.
--
-- WHY EACH COLUMN EXISTS:
--   admin_id      → Auto-incrementing unique ID
--   username      → Login username (must be unique)
--   password_hash → bcrypt-hashed password (same as students)
--   email         → Admin's email address
--   role          → 'admin' (normal) or 'super_admin' (full privileges)
--                   Super admins can delete elections, manage other admins, etc.
-- =============================================================================
CREATE TABLE IF NOT EXISTS admin (
    admin_id      INT           AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)   UNIQUE NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,
    email         VARCHAR(100)  UNIQUE NOT NULL,
    role          ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at    DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLE 3: elections
-- =============================================================================
-- PURPOSE: Stores each election event with its schedule and status.
--
-- LIFECYCLE: scheduled → active → closed
--   'scheduled' = Election created but not yet open for voting
--   'active'    = Voting is currently open
--   'closed'    = Voting period has ended
--
-- WHY EACH COLUMN EXISTS:
--   election_id    → Unique ID for each election
--   election_title → Human-readable name (e.g., "2026 Guild Elections")
--   position       → Which position this election is for
--   start_date     → When voting opens
--   end_date       → When voting closes (countdown timer uses this)
--   status         → Current state of the election
--   created_at     → When admin created this election
-- =============================================================================
CREATE TABLE IF NOT EXISTS elections (
    election_id    INT           AUTO_INCREMENT PRIMARY KEY,
    election_title VARCHAR(200)  NOT NULL,
    position       VARCHAR(100)  NOT NULL,
    start_date     DATETIME      NOT NULL,
    end_date       DATETIME      NOT NULL,
    status         ENUM('scheduled', 'active', 'closed') DEFAULT 'scheduled',
    created_at     DATETIME      DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_status (status),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLE 4: candidates
-- =============================================================================
-- PURPOSE: Stores candidate information — students running for positions.
--
-- WHY EACH COLUMN EXISTS:
--   candidate_id     → Unique ID for each candidate entry
--   student_id       → Links to students table (FK) — who is this candidate?
--   first_name       → Candidate's display name
--   last_name        → Candidate's surname
--   position         → Which position they're running for
--   faculty          → Candidate's faculty
--   department       → Candidate's department
--   manifesto        → Their campaign promises/vision (TEXT = long content)
--   image_path       → Path to their photo (e.g., "storage/uploads/candidates/photo.jpg")
--   votes            → Running count of votes received
--                      (Also tracked in votes table for audit, this is for quick display)
--   is_university_wide → 1 = all students can vote (e.g., Guild President)
--                        0 = only students in same department can vote
--   status           → 'pending' = awaiting admin verification
--                      'verified' = approved to appear on ballot
--                      'rejected' = not approved
-- =============================================================================
CREATE TABLE IF NOT EXISTS candidates (
    candidate_id     INT           AUTO_INCREMENT PRIMARY KEY,
    student_id       VARCHAR(20)   UNIQUE,
    first_name       VARCHAR(50)   NOT NULL,
    last_name        VARCHAR(50)   NOT NULL,
    position         VARCHAR(100)  NOT NULL,
    faculty          VARCHAR(100)  NOT NULL,
    department       VARCHAR(100)  DEFAULT NULL,
    manifesto        TEXT,
    image_path       VARCHAR(255),
    votes            INT           DEFAULT 0,
    is_university_wide TINYINT(1)  DEFAULT 0,
    status           ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    created_at       DATETIME      DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_position (position),
    INDEX idx_status (status),
    INDEX idx_department (department),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLE 5: votes
-- =============================================================================
-- PURPOSE: Records every individual vote cast. This is the most critical table.
--
-- INTEGRITY RULES:
--   1. Each anonymous receipt token can only vote ONCE per position
--      (enforced by UNIQUE(receipt_token, position)). This preserves
--      the property "one vote per eligible voter per position" without
--      storing the student's identity in the votes table.
--   2. The candidate must exist (enforced by FOREIGN KEY to candidates)
--
-- WHY EACH COLUMN EXISTS:
--   vote_id       → Unique ID for each vote record
--   receipt_token → Anonymous verification token representing a unique vote
--                    (not a student identifier). This preserves voter privacy
--                    while still allowing vote verification.
--   candidate_id  → Who they voted for (FK → candidates)
--   position      → Which position this vote is for
--   vote_date     → When the vote was cast (auto-filled, used for audit)
--
-- THE UNIQUE CONSTRAINT:
--   UNIQUE(receipt_token, position) means:
--   A single anonymous receipt token can be used to cast one vote per position.
--   This prevents duplicate votes from the same receipt token for a position.
--   Note: The system separately tracks which student has voted via
--   `students.has_voted` at the application level; votes remain anonymous.
-- =============================================================================
CREATE TABLE IF NOT EXISTS votes (
    vote_id       INT          AUTO_INCREMENT PRIMARY KEY,
    receipt_token VARCHAR(64)  NOT NULL,
    candidate_id  INT,
    position      VARCHAR(100),
    vote_date     DATE         NOT NULL,

    UNIQUE KEY uniq_receipt_position (receipt_token, position),
    INDEX idx_receipt (receipt_token),
    INDEX idx_position (position),
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLE 6: feedback
-- =============================================================================
-- PURPOSE: Stores student feedback about the voting experience.
--          Used for system improvement and in your final year report.
-- =============================================================================
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id   INT          AUTO_INCREMENT PRIMARY KEY,
    student_id    VARCHAR(20),
    feedback      TEXT         NOT NULL,
    feedback_date DATETIME     DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLE 7: audit_log
-- =============================================================================
-- PURPOSE: Records EVERY important action in the system for accountability.
--          This is crucial for election transparency and dispute resolution.
--
-- WHAT GETS LOGGED:
--   - Student login/logout
--   - Admin login/logout  
--   - Vote casting
--   - Candidate additions/removals
--   - Election status changes
--   - Results publishing
--
-- WHY EACH COLUMN EXISTS:
--   log_id     → Unique ID for each log entry
--   user_id    → Who performed the action (student ID or admin ID)
--   action     → What happened (e.g., 'STUDENT_LOGIN', 'VOTE_CAST')
--   details    → Human-readable description
--   ip_address → The user's IP address (for security auditing)
--   timestamp  → When it happened (auto-filled)
-- =============================================================================
CREATE TABLE IF NOT EXISTS audit_log (
    log_id     INT          AUTO_INCREMENT PRIMARY KEY,
    user_id    VARCHAR(50),
    action     VARCHAR(100) NOT NULL,
    details    TEXT,
    ip_address VARCHAR(45),
    timestamp  DATETIME     DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- TABLE: settings
-- =============================================================================
-- PURPOSE: Key-value store for runtime configuration (logo, results status, etc.)
-- =============================================================================
CREATE TABLE IF NOT EXISTS settings (
    setting_key   VARCHAR(50) PRIMARY KEY,
    setting_value TEXT         NOT NULL,
    updated_at    DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- DEFAULT DATA
-- =============================================================================
-- Insert a default admin account so you can log in for the first time.
-- 
-- Username: admin
-- Password: password  (the hash below is bcrypt of "password")
--
-- ⚠️ CHANGE THIS PASSWORD IMMEDIATELY AFTER FIRST LOGIN!
-- =============================================================================
INSERT INTO admin (username, password_hash, email, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@kyu.ac.ug', 'super_admin')
ON DUPLICATE KEY UPDATE admin_id = admin_id;
-- ON DUPLICATE KEY = If admin already exists, do nothing (safe to re-run)


-- =============================================================================
-- END OF SCHEMA
-- =============================================================================
-- You have successfully set up the database!
-- 
-- Next steps:
--   1. Verify tables: SHOW TABLES;
--   2. Check structure: DESCRIBE students;
--   3. Access the system: http://localhost/finalyearproject/public/
-- =============================================================================
