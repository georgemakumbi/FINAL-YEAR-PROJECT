CREATE DATABASE finalyearproject;

USE finalyearproject;

-- Students table
CREATE TABLE students (
    student_id VARCHAR(20) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    faculty VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    has_voted BOOLEAN DEFAULT FALSE,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Candidates table
CREATE TABLE candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    position VARCHAR(100) NOT NULL,
    faculty VARCHAR(100) NOT NULL,
    manifesto TEXT,
    image_path VARCHAR(255),
    votes INT DEFAULT 0,
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Votes table
CREATE TABLE votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_token VARCHAR(64) NOT NULL,
    candidate_id INT,
    position VARCHAR(100),
    vote_date DATE NOT NULL,
    UNIQUE KEY uniq_receipt_position (receipt_token, position),
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id)
);

-- Admin table
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin'
);

INSERT INTO admin (username, password_hash, email)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@kyu.ac.ug');
-- Password: "password"
-- Add elections table to the database if it doesn't exist
CREATE TABLE IF NOT EXISTS elections (
    election_id INT AUTO_INCREMENT PRIMARY KEY,
    election_title VARCHAR(200) NOT NULL,
    position VARCHAR(100) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('scheduled', 'active', 'closed') DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Add feedback table if it doesn't exist
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    feedback TEXT NOT NULL,
    feedback_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Sample feedback data (optional)
-- INSERT INTO feedback (student_id, feedback) VALUES ('2023/CS/001', 'Great voting system!');

-- Add audit_log table for better tracking (optional)
CREATE TABLE IF NOT EXISTS audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50),
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

--  Helpful index for vote validation and counting queries.
CREATE INDEX idx_votes_position ON votes(position);

-- Add OTP columns to the students table for password reset functionality
ALTER TABLE students 
ADD otp VARCHAR(6) DEFAULT NULL;

ALTER TABLE students 
ADD otp_expiry DATETIME DEFAULT NULL;

-- Create index for faster OTP lookups
ALTER TABLE students 
ADD INDEX idx_otp (otp);

-- Add department column to candidates table
ALTER TABLE candidates ADD COLUMN department VARCHAR(100) DEFAULT NULL;

-- Add is_university_wide column to candidates table
ALTER TABLE candidates ADD COLUMN is_university_wide TINYINT(1) DEFAULT 0;

-- Add index for faster department-based queries
ALTER TABLE candidates ADD INDEX idx_department (department);

-- Optional: Update existing candidates to set is_university_wide = 1 
-- (for Guild President and Guild Vice President positions)
UPDATE candidates 
SET is_university_wide = 1 
WHERE position IN ('Guild President', 'Guild Vice President');

