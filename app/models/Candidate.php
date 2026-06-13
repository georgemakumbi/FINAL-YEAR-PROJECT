<?php
/**
 * =============================================================================
 * CANDIDATE MODEL — All Database Operations for Candidates
 * =============================================================================
 * 
 * A candidate is a student who is running for a position in the election.
 * This Model handles all queries related to candidates.
 *
 * TABLE: candidates
 * RELATIONSHIPS:
 *   candidates.student_id → students.student_id (a candidate IS a student)
 *
 * =============================================================================
 */

class Candidate
{
    // =========================================================================
    // FIND OPERATIONS
    // =========================================================================

    /**
     * Find a candidate by their candidate ID.
     *
     * @param mysqli $conn          Database connection
     * @param int    $candidate_id  Candidate ID
     * @return array|null           Candidate data or null
     */
    public static function findById(mysqli $conn, int $candidate_id): ?array
    {
        $stmt = $conn->prepare(
            "SELECT * FROM candidates WHERE candidate_id = ?"
        );
        $stmt->bind_param("i", $candidate_id); // "i" = integer
        $stmt->execute();
        $result = $stmt->get_result();
        $candidate = $result->fetch_assoc();
        $stmt->close();
        
        return $candidate ?: null;
    }

    /**
     * Get all VERIFIED candidates, optionally filtered by voter's department.
     * 
     * This is the query used by voting.php to show candidates on the ballot.
     * 
     * FILTERING LOGIC:
     *   - University-wide positions (Guild President, VP) → shown to ALL students
     *   - Department positions (Faculty Rep) → shown only to matching department
     *
     * @param mysqli $conn        Database connection
     * @param string $department  Voter's department (for filtering)
     * @return array              Array of verified candidates
     */
    public static function getVerifiedForVoter(mysqli $conn, string $department): array
    {
        $stmt = $conn->prepare(
            "SELECT * FROM candidates 
             WHERE status = 'verified' AND (
                is_university_wide = 1 
                OR department = ? 
                OR department IS NULL
             )
             ORDER BY position, last_name"
        );
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $result = $stmt->get_result();
        $candidates = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $candidates;
    }

    /**
     * Get all candidates (for admin management).
     *
     * @param mysqli $conn    Database connection
     * @param string $status  Filter by status: 'all', 'pending', 'verified', 'rejected'
     * @return array          Array of candidates
     */
    public static function findAll(mysqli $conn, string $status = 'all'): array
    {
        if ($status !== 'all' && in_array($status, ['pending', 'verified', 'rejected'])) {
            $stmt = $conn->prepare(
                "SELECT * FROM candidates WHERE status = ? ORDER BY position, last_name"
            );
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
            $candidates = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $candidates;
        }
        
        $result = $conn->query(
            "SELECT * FROM candidates ORDER BY position, last_name"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Validate that a candidate exists, matches the position, and is eligible.
     * 
     * Used during vote processing to prevent tampering.
     *
     * @param mysqli $conn          Database connection
     * @param int    $candidate_id  Candidate ID to validate
     * @return array|null           ['position', 'department', 'is_university_wide'] or null
     */
    public static function validateForVoting(mysqli $conn, int $candidate_id): ?array
    {
        $stmt = $conn->prepare(
            "SELECT position, department, is_university_wide 
             FROM candidates WHERE candidate_id = ?"
        );
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data ?: null;
    }

    // =========================================================================
    // VOTE COUNTING
    // =========================================================================

    /**
     * Increment a candidate's vote count by 1.
     * 
     * "votes = votes + 1" is an ATOMIC operation in MySQL.
     * This means even if two queries run at the EXACT same time,
     * both votes will be counted correctly. MySQL handles the locking internally.
     *
     * @param mysqli $conn          Database connection
     * @param int    $candidate_id  Candidate to increment
     * @return bool                 true if update succeeded
     */
    public static function incrementVote(mysqli $conn, int $candidate_id): bool
    {
        $stmt = $conn->prepare(
            "UPDATE candidates SET votes = votes + 1 WHERE candidate_id = ?"
        );
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
        
        return $success;
    }

    /**
     * Get election results grouped by position.
     * 
     * Returns candidates ordered by position then by votes (highest first).
     * Includes vote percentage calculation.
     *
     * @param mysqli $conn  Database connection
     * @return array        Results with percentage data
     */
    public static function getResults(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT c.position, c.candidate_id, c.first_name, c.last_name, 
                    c.faculty, c.votes, 
                    CASE 
                        WHEN total.total_votes > 0 
                        THEN ROUND((c.votes / total.total_votes * 100), 1)
                        ELSE 0
                    END as percentage
             FROM candidates c
             JOIN (
                 SELECT position, SUM(votes) as total_votes
                 FROM candidates
                 WHERE status = 'verified'
                 GROUP BY position
             ) total ON c.position = total.position
             WHERE c.status = 'verified'
             ORDER BY c.position, c.votes DESC"
        );
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // =========================================================================
    // CREATE & UPDATE OPERATIONS
    // =========================================================================

    /**
     * Add a new candidate.
     *
     * @param mysqli $conn  Database connection
     * @param array  $data  Candidate data
     * @return int|false    New candidate ID, or false on failure
     */
    public static function create(mysqli $conn, array $data)
    {
        $stmt = $conn->prepare(
            "INSERT INTO candidates (student_id, first_name, last_name, position, 
                                     faculty, department, manifesto, image_path, 
                                     is_university_wide, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $status = $data['status'] ?? 'pending';
        $is_wide = $data['is_university_wide'] ?? 0;
        
        $stmt->bind_param(
            "ssssssssis",
            $data['student_id'],
            $data['first_name'],
            $data['last_name'],
            $data['position'],
            $data['faculty'],
            $data['department'],
            $data['manifesto'],
            $data['image_path'],
            $is_wide,
            $status
        );
        $stmt->execute();
        $id = $stmt->insert_id; // Gets the auto-generated candidate_id
        $stmt->close();
        
        return $id > 0 ? $id : false;
    }

    /**
     * Update candidate status (pending → verified/rejected).
     *
     * @param mysqli $conn          Database connection
     * @param int    $candidate_id  Candidate to update
     * @param string $status        New status: 'verified' or 'rejected'
     * @return bool                 true if update succeeded
     */
    public static function updateStatus(mysqli $conn, int $candidate_id, string $status): bool
    {
        if (!in_array($status, ['pending', 'verified', 'rejected'])) {
            return false; // Invalid status — reject the operation
        }
        
        $stmt = $conn->prepare(
            "UPDATE candidates SET status = ? WHERE candidate_id = ?"
        );
        $stmt->bind_param("si", $status, $candidate_id);
        $stmt->execute();
        $success = $stmt->affected_rows >= 0; // 0 = no change needed, still OK
        $stmt->close();
        
        return $success;
    }

    /**
     * Delete a candidate.
     *
     * @param mysqli $conn          Database connection
     * @param int    $candidate_id  Candidate to delete
     * @return bool                 true if deleted
     */
    public static function delete(mysqli $conn, int $candidate_id): bool
    {
        $stmt = $conn->prepare(
            "DELETE FROM candidates WHERE candidate_id = ?"
        );
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
        
        return $success;
    }

    // =========================================================================
    // STATISTICS
    // =========================================================================

    /**
     * Count candidates by status.
     *
     * @param mysqli $conn    Database connection
     * @param string $status  Status to count ('verified', 'pending', 'rejected')
     * @return int            Count
     */
    public static function countByStatus(mysqli $conn, string $status): int
    {
        $stmt = $conn->prepare(
            "SELECT COUNT(*) as total FROM candidates WHERE status = ?"
        );
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return (int)$row['total'];
    }

    /**
     * Get unique positions that have verified candidates.
     *
     * @param mysqli $conn  Database connection
     * @return array        Array of position strings
     */
    public static function getPositions(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT DISTINCT position FROM candidates 
             WHERE status = 'verified' ORDER BY position"
        );
        
        $positions = [];
        while ($row = $result->fetch_assoc()) {
            $positions[] = $row['position'];
        }
        return $positions;
    }
}
