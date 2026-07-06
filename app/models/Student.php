<?php
/**
 * =============================================================================
 * STUDENT MODEL — All Database Operations for Students
 * =============================================================================
 * 
 * WHAT IS A MODEL CLASS?
 *   A Model class is a PHP class that handles ALL database interactions
 *   for ONE table. Instead of writing SQL queries scattered across many
 *   files, we put them ALL here in one organized place.
 *
 * WHY USE A CLASS?
 *   A class is like a container that groups related functions together.
 *   Think of it like a toolbox — all the "student tools" are in one box.
 *
 * WHY "STATIC" METHODS?
 *   "static" means you don't need to create an object first.
 *   You call methods directly on the class name:
 *     Student::findById($conn, '23/U/001')
 *   Instead of:
 *     $studentModel = new Student();
 *     $studentModel->findById($conn, '23/U/001');
 *   
 *   Static is simpler for our use case — we don't need to 
 *   maintain state between method calls.
 *
 * USAGE EXAMPLES:
 *   // Find a student
 *   $student = Student::findById($conn, '23/U/001');
 *   
 *   // Check if student has voted
 *   if (Student::hasVoted($conn, '23/U/001')) { ... }
 *   
 *   // Mark student as voted
 *   Student::markAsVoted($conn, '23/U/001');
 *   
 *   // Authenticate a student
 *   $student = Student::authenticate($conn, '23/U/001', 'mypass123');
 *
 * =============================================================================
 */

class Student
{
    // =========================================================================
    // FIND OPERATIONS (Reading data from the database)
    // =========================================================================

    /**
     * Find a student by their Student ID.
     * 
     * This is the most commonly used method. It retrieves all information
     * about a single student from the database.
     *
     * HOW IT WORKS:
     *   1. Prepare a SELECT query with a ? placeholder
     *   2. Bind the student_id to the placeholder (prevents SQL injection)
     *   3. Execute the query
     *   4. Return the student data as an associative array, or null if not found
     *
     * WHAT'S AN ASSOCIATIVE ARRAY?
     *   It's an array with named keys instead of numbered indexes:
     *   [
     *       'student_id'  => '23/U/001',
     *       'first_name'  => 'George',
     *       'last_name'   => 'Liam',
     *       'email'       => 'george@kyu.ac.ug',
     *       'faculty'     => 'Engineering',
     *       'department'  => 'Computer Science',
     *       'has_voted'   => 0,
     *       ...
     *   ]
     *
     * THE "?array" RETURN TYPE:
     *   The "?" before "array" means this can return null.
     *   - Found → returns the array
     *   - Not found → returns null
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  The student ID to search for (e.g., "23/U/001")
     * @return array|null         Student data array, or null if not found
     */
    public static function findById(mysqli $conn, string $student_id): ?array
    {
        $stmt = $conn->prepare(
            "SELECT student_id, first_name, last_name, email, faculty, 
                    department, password_hash, has_voted, is_registered, registration_date
             FROM students 
             WHERE student_id = ?"
        );
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
        
        // fetch_assoc() returns null if no row found, or the row as an array
        return $student ?: null;
    }

    /**
     * Find a student by ID specifically for the registration flow.
     *
     * Returns the student only if they exist in the DB (pre-loaded by admin)
     * regardless of whether they have registered yet. Used in Step 1 of
     * the trusted registration flow to validate the entered student ID.
     *
     * Returns:
     *   - The student array (with is_registered flag) if found
     *   - null if not found
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID to look up
     * @return array|null         Student data or null
     */
    public static function findByIdForRegistration(mysqli $conn, string $student_id): ?array
    {
        $stmt = $conn->prepare(
            "SELECT student_id, first_name, last_name, email, faculty, 
                    department, is_registered
             FROM students 
             WHERE student_id = ?"
        );
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();

        return $student ?: null;
    }

    /**
     * Check if a student has completed OTP-verified self-registration.
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID to check
     * @return bool               true if fully registered
     */
    public static function isRegistered(mysqli $conn, string $student_id): bool
    {
        $stmt = $conn->prepare(
            "SELECT is_registered FROM students WHERE student_id = ?"
        );
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->bind_result($is_registered);
        $found = $stmt->fetch();
        $stmt->close();

        return $found ? (bool)$is_registered : false;
    }

    /**
     * Return a privacy-masked version of an email address.
     * 
     * Examples:
     *   230123456@std.kyu.ac.ug → 2*******6@std.kyu.ac.ug
     *   george@kyu.ac.ug        → g*****e@kyu.ac.ug
     *
     * @param string $email  Full email address
     * @return string        Masked email
     */
    public static function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $len = strlen($local);
        if ($len <= 2) {
            $masked = $local; // too short to mask meaningfully
        } else {
            $masked = $local[0] . str_repeat('*', $len - 2) . $local[$len - 1];
        }
        return $masked . '@' . $domain;
    }

    /**
     * Find a student by their email address.
     * 
     * Used for:
     *   - Password reset (find student by email to send OTP)
     *   - Checking if email is already registered
     *
     * @param mysqli $conn   Database connection
     * @param string $email  Email address to search for
     * @return array|null    Student data or null
     */
    public static function findByEmail(mysqli $conn, string $email): ?array
    {
        $stmt = $conn->prepare(
            "SELECT student_id, first_name, last_name, email, faculty, department
             FROM students 
             WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
        
        return $student ?: null;
    }

    /**
     * Get ALL students from the database.
     * 
     * Used by the admin dashboard to display the student list.
     * Returns students ordered by registration date (newest first).
     *
     * WHAT IS fetch_all()?
     *   While fetch_assoc() returns ONE row at a time,
     *   fetch_all(MYSQLI_ASSOC) returns ALL rows at once as an array of arrays:
     *   [
     *       ['student_id' => '23/U/001', 'first_name' => 'George', ...],
     *       ['student_id' => '23/U/002', 'first_name' => 'Jane', ...],
     *       ...
     *   ]
     *
     * @param mysqli $conn  Database connection
     * @return array        Array of all students (may be empty)
     */
    public static function findAll(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT student_id, first_name, last_name, email, faculty, 
                    department, has_voted, registration_date
             FROM students 
             ORDER BY registration_date DESC"
        );
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // =========================================================================
    // AUTHENTICATION OPERATIONS
    // =========================================================================

    /**
     * Authenticate a student with their ID and password.
     * 
     * This combines "find the student" and "verify the password" into
     * one convenient method. Returns the student data if credentials
     * are valid, or null if login fails.
     *
     * WHY RETURN NULL INSTEAD OF AN ERROR MESSAGE?
     *   The Model should only handle DATA operations.
     *   Deciding what error message to show is the Controller's job.
     *   This is called "Separation of Concerns."
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID from login form
     * @param string $password    Plain-text password from login form
     * @return array|null         Student data if valid, null if invalid
     */
    public static function authenticate(mysqli $conn, string $student_id, string $password): ?array
    {
        // Find the student
        $student = self::findById($conn, $student_id);
        
        // "self::" calls another method in the SAME class
        // It's like saying "hey Student class, run your findById method"
        
        if ($student === null) {
            return null; // Student doesn't exist
        }
        
        // Verify password against the stored bcrypt hash
        if (!password_verify($password, $student['password_hash'])) {
            return null; // Wrong password
        }
        
        // Remove password_hash from the returned data — never expose hashes!
        unset($student['password_hash']);
        
        return $student; // Login successful!
    }

    // =========================================================================
    // VOTING STATUS OPERATIONS
    // =========================================================================

    /**
     * Check if a student has already voted.
     * 
     * This is called on multiple pages:
     *   - voting.php → Redirect to results if already voted
     *   - results.php → Show "thank you" message or redirect to vote
     *   - processvote.php → Final check before recording votes
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID to check
     * @return bool               true if voted, false if not
     */
    public static function hasVoted(mysqli $conn, string $student_id): bool
    {
        $stmt = $conn->prepare(
            "SELECT has_voted FROM students WHERE student_id = ?"
        );
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->bind_result($has_voted);
        $found = $stmt->fetch();
        $stmt->close();
        
        // Cast to bool: 1 → true, 0 → false, null → false
        return $found ? (bool)$has_voted : false;
    }

    /**
     * Check voting status with row lock (for use inside transactions).
     * 
     * This is a special version of hasVoted() that includes "FOR UPDATE".
     * It LOCKS the student's row so no other process can read or modify it
     * until the current transaction completes.
     *
     * WHEN TO USE THIS vs hasVoted():
     *   - hasVoted()           → Just checking, no transaction
     *   - hasVotedForUpdate()  → Inside a transaction, about to modify data
     *
     * @param mysqli $conn        Database connection (must be in a transaction!)
     * @param string $student_id  Student ID to check and lock
     * @return array              ['found' => bool, 'has_voted' => bool]
     */
    public static function hasVotedForUpdate(mysqli $conn, string $student_id): array
    {
        $stmt = $conn->prepare(
            "SELECT has_voted FROM students WHERE student_id = ? FOR UPDATE"
        );
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->bind_result($has_voted);
        $found = $stmt->fetch();
        $stmt->close();
        
        return [
            'found'     => (bool)$found,
            'has_voted' => $found ? (bool)$has_voted : false,
        ];
    }

    /**
     * Mark a student as having voted.
     * 
     * Called after all votes are successfully recorded in processvote.php.
     * Sets has_voted = TRUE so the student can't vote again.
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID to mark
     * @return bool               true if update succeeded
     */
    public static function markAsVoted(mysqli $conn, string $student_id): bool
    {
        $stmt = $conn->prepare(
            "UPDATE students SET has_voted = TRUE WHERE student_id = ?"
        );
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected === 1;
    }

    // =========================================================================
    // OTP (One-Time Password) OPERATIONS
    // =========================================================================

    /**
     * Store an OTP code for a student (for registration or password reset).
     * 
     * OTP = One-Time Password (a 6-digit code sent to email)
     * The OTP expires after 10 minutes for security.
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID
     * @param string $otp         The 6-digit OTP code
     * @return bool               true if stored successfully
     */
    public static function storeOtp(mysqli $conn, string $student_id, string $otp): bool
    {
        $stmt = $conn->prepare(
            "UPDATE students 
             SET otp = ?, otp_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) 
             WHERE student_id = ?"
        );
        $stmt->bind_param("ss", $otp, $student_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected === 1;
    }

    /**
     * Verify an OTP code for a student.
     * 
     * Checks that:
     *   1. The OTP matches what's stored in the database
     *   2. The OTP hasn't expired (within 10 minutes)
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID
     * @param string $otp         OTP code to verify
     * @return bool               true if OTP is valid and not expired
     */
    public static function verifyOtp(mysqli $conn, string $student_id, string $otp): bool
    {
        $stmt = $conn->prepare(
            "SELECT student_id FROM students 
             WHERE student_id = ? AND otp = ? AND otp_expiry > NOW()"
        );
        $stmt->bind_param("ss", $student_id, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        $valid = $result->num_rows === 1;
        $stmt->close();
        
        // Clear the OTP after verification (one-time use!)
        if ($valid) {
            self::clearOtp($conn, $student_id);
        }
        
        return $valid;
    }

    /**
     * Clear a student's OTP (after successful verification or expiry).
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID
     * @return void
     */
    public static function clearOtp(mysqli $conn, string $student_id): void
    {
        $stmt = $conn->prepare(
            "UPDATE students SET otp = NULL, otp_expiry = NULL WHERE student_id = ?"
        );
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->close();
    }

    // =========================================================================
    // CREATE & UPDATE OPERATIONS
    // =========================================================================

    /**
     * Register a new student.
     * 
     * Called when a student completes OTP verification for the first time.
     * The password is hashed with bcrypt before storing.
     *
     * @param mysqli $conn   Database connection
     * @param array  $data   Student data with keys:
     *                       'student_id', 'first_name', 'last_name',
     *                       'email', 'password', 'faculty', 'department'
     * @return bool          true if registration succeeded
     */
    public static function create(mysqli $conn, array $data): bool
    {
        // Hash the password using bcrypt (PASSWORD_DEFAULT = bcrypt in PHP 8)
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare(
            "INSERT INTO students (student_id, first_name, last_name, email, 
                                   password_hash, faculty, department) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssssss",
            $data['student_id'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $password_hash,
            $data['faculty'],
            $data['department']
        );
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
        
        return $success;
    }

    /**
     * Activate a pre-imported student account after OTP-verified registration.
     *
     * Instead of INSERT (which create() does), this UPDATEs an existing
     * student record that was pre-loaded by the admin via CSV import.
     * Sets the password and marks the account as fully registered.
     *
     * @param mysqli $conn        Database connection
     * @param string $student_id  Student ID (already in DB)
     * @param string $password    Plain-text password chosen by student
     * @return bool               true if activation succeeded
     */
    public static function activate(mysqli $conn, string $student_id, string $password): bool
    {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "UPDATE students
             SET password_hash = ?, is_registered = TRUE
             WHERE student_id = ? AND is_registered = FALSE"
        );
        $stmt->bind_param("ss", $password_hash, $student_id);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();

        return $success;
    }

    /**
     * Update a student's password.
     * 
     * Used during password reset flow. Hashes the new password before storing.
     *
     * @param mysqli $conn          Database connection
     * @param string $student_id    Student ID
     * @param string $new_password  New plain-text password (will be hashed)
     * @return bool                 true if update succeeded
     */
    public static function updatePassword(mysqli $conn, string $student_id, string $new_password): bool
    {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare(
            "UPDATE students SET password_hash = ? WHERE student_id = ?"
        );
        $stmt->bind_param("ss", $password_hash, $student_id);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
        
        return $success;
    }

    // =========================================================================
    // STATISTICS (For Admin Dashboard)
    // =========================================================================

    /**
     * Count total registered students.
     *
     * @param mysqli $conn  Database connection
     * @return int          Total number of students
     */
    public static function countAll(mysqli $conn): int
    {
        $result = $conn->query("SELECT COUNT(*) as total FROM students");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Count students who have voted.
     *
     * @param mysqli $conn  Database connection
     * @return int          Number of students who voted
     */
    public static function countVoted(mysqli $conn): int
    {
        $result = $conn->query(
            "SELECT COUNT(*) as total FROM students WHERE has_voted = TRUE"
        );
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Get voter turnout percentage.
     * 
     * Calculated as: (students who voted / total students) × 100
     *
     * @param mysqli $conn  Database connection
     * @return float        Turnout percentage (0-100)
     */
    public static function getVoterTurnout(mysqli $conn): float
    {
        $total = self::countAll($conn);
        if ($total === 0) {
            return 0.0;
        }
        $voted = self::countVoted($conn);
        return round(($voted / $total) * 100, 1);
    }
}
