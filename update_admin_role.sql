-- Add role column to admin table (run this if the column doesn't exist)
ALTER TABLE admin ADD COLUMN role ENUM('admin', 'super_admin') DEFAULT 'admin';

-- Update existing admin to be super admin (run this to make the current admin a super admin)
UPDATE admin SET role = 'super_admin' WHERE username = 'admin';

-- Insert a new super admin (example)
INSERT INTO admin (username, password_hash, email, role)VALUES ('superadmin', '$2y$10$k2V9St72f3kKBKkaerYiyO4Psx98D/M2tiph.05vZlKnDISQb7wHe', 'superadmin@kyu.ac.ug', 'super_admin');

-- To create a new password hash for a user, you can use:
-- In PHP: echo password_hash('superpassword', PASSWORD_DEFAULT);
ALTER TABLE students
ADD otp VARCHAR(6),
ADD otp_expiry DATETIME,
ADD password VARCHAR(255),
ADD is_verified BOOLEAN DEFAULT 0;

ALTER TABLE students
ADD otp_attempts INT DEFAULT 0,
ADD otp_locked_until DATETIME NULL;
ADD otp_requests INT DEFAULT 0,
ADD otp_last_request DATETIME NULL;
