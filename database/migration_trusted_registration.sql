-- =============================================================================
-- MIGRATION: Trusted Registration via Student ID Lookup + OTP
-- =============================================================================
-- Run this migration on your existing database to support the new
-- 3-step trusted registration flow.
--
-- What this does:
--   1. Allows students to be pre-loaded (imported) before they register
--      by making password_hash, first_name, last_name nullable.
--   2. Adds `is_registered` flag to distinguish imported-but-not-yet-registered
--      students from fully registered ones.
--
-- HOW TO APPLY:
--   In phpMyAdmin → SQL tab, paste and run this file.
--   OR via MySQL CLI: SOURCE migration_trusted_registration.sql;
--
-- SAFE TO RUN MULTIPLE TIMES: All statements use IF NOT EXISTS / MODIFY safely.
-- =============================================================================

USE kyambogo_voting;

-- Step 1: Make first_name and last_name nullable
--   Reason: Admin CSV import may load students without a name initially,
--   or we want to allow the student to confirm their own name at registration.
ALTER TABLE students
    MODIFY COLUMN first_name VARCHAR(50) NULL DEFAULT NULL,
    MODIFY COLUMN last_name  VARCHAR(50) NULL DEFAULT NULL;

-- Step 2: Make password_hash nullable
--   Reason: Admin-imported students exist in the DB before they register.
--   They won't have a password until they complete the OTP-verified registration.
ALTER TABLE students
    MODIFY COLUMN password_hash VARCHAR(255) NULL DEFAULT NULL;

-- Step 3: Add is_registered flag (if it doesn't already exist)
--   Reason: Cleanly distinguishes between:
--     FALSE = student record imported by admin, not yet self-registered
--     TRUE  = student has completed OTP-verified registration (has password)
ALTER TABLE students
    ADD COLUMN IF NOT EXISTS is_registered BOOLEAN NOT NULL DEFAULT FALSE
    AFTER has_voted;

-- Step 4: Mark all existing students who already have a password as registered
--   Reason: Existing accounts should not be required to re-register.
UPDATE students
    SET is_registered = TRUE
    WHERE password_hash IS NOT NULL AND password_hash != '';

-- =============================================================================
-- VERIFICATION
-- Check results after running:
--   DESCRIBE students;
--   SELECT student_id, first_name, is_registered FROM students LIMIT 5;
-- =============================================================================
