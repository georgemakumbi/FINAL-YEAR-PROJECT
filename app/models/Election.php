<?php
/**
 * =============================================================================
 * ELECTION MODEL — All Database Operations for Elections
 * =============================================================================
 * 
 * An election has a LIFECYCLE:
 *   'scheduled' → 'active' → 'closed'
 * 
 *   scheduled: Admin created it but voting hasn't started
 *   active:    Voting is currently open (countdown timer running)
 *   closed:    Voting period has ended, results can be published
 *
 * =============================================================================
 */

class Election
{
    /**
     * Find an election by its ID.
     *
     * @param mysqli $conn          Database connection
     * @param int    $election_id   Election ID
     * @return array|null           Election data or null
     */
    public static function findById(mysqli $conn, int $election_id): ?array
    {
        $stmt = $conn->prepare("SELECT * FROM elections WHERE election_id = ?");
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $election = $result->fetch_assoc();
        $stmt->close();
        
        return $election ?: null;
    }

    /**
     * Get the currently active election (if any).
     * 
     * There should only be ONE active election at a time.
     * If multiple are active, returns the one ending soonest.
     *
     * @param mysqli $conn  Database connection
     * @return array|null   Active election data or null
     */
    public static function getActive(mysqli $conn): ?array
    {
        $result = $conn->query(
            "SELECT * FROM elections 
             WHERE status = 'active' 
             ORDER BY end_date ASC 
             LIMIT 1"
        );
        $election = $result->fetch_assoc();
        
        return $election ?: null;
    }

    /**
     * Get the election end date for countdown timer.
     * 
     * Checks active elections first, then future scheduled ones.
     * This is what the homepage countdown timer uses.
     *
     * @param mysqli $conn  Database connection
     * @return string|null  End date string (e.g., "2026-07-01 17:00:00") or null
     */
    public static function getEndDate(mysqli $conn): ?string
    {
        // First: look for active elections
        $result = $conn->query(
            "SELECT end_date FROM elections 
             WHERE status = 'active' 
             ORDER BY end_date DESC LIMIT 1"
        );
        if ($result && $row = $result->fetch_assoc()) {
            return $row['end_date'];
        }
        
        // Fallback: look for future elections (not yet started)
        $result = $conn->query(
            "SELECT end_date FROM elections 
             WHERE end_date > NOW() 
             ORDER BY end_date DESC LIMIT 1"
        );
        if ($result && $row = $result->fetch_assoc()) {
            return $row['end_date'];
        }
        
        return null; // No elections found
    }

    /**
     * Check if voting is currently allowed.
     * 
     * Voting is allowed when there's an active election and
     * the current time is before the end date.
     *
     * @param mysqli $conn  Database connection
     * @return bool         true if voting is open
     */
    public static function isVotingOpen(mysqli $conn): bool
    {
        $active = self::getActive($conn);
        if ($active === null) {
            return false;
        }
        
        return strtotime($active['end_date']) > time();
    }

    /**
     * Get all elections, ordered by creation date (newest first).
     *
     * @param mysqli $conn  Database connection
     * @return array        Array of elections
     */
    public static function findAll(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT * FROM elections ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new election.
     *
     * @param mysqli $conn  Database connection
     * @param array  $data  Election data: 'election_title', 'position', 
     *                      'start_date', 'end_date', 'status'
     * @return int|false    New election ID or false on failure
     */
    public static function create(mysqli $conn, array $data)
    {
        $status = $data['status'] ?? 'scheduled';
        
        $stmt = $conn->prepare(
            "INSERT INTO elections (election_title, position, start_date, end_date, status) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssss",
            $data['election_title'],
            $data['position'],
            $data['start_date'],
            $data['end_date'],
            $status
        );
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        
        return $id > 0 ? $id : false;
    }

    /**
     * Update election status.
     *
     * @param mysqli $conn          Database connection
     * @param int    $election_id   Election to update
     * @param string $status        New status: 'scheduled', 'active', 'closed'
     * @return bool                 true if updated
     */
    public static function updateStatus(mysqli $conn, int $election_id, string $status): bool
    {
        if (!in_array($status, ['scheduled', 'active', 'closed'])) {
            return false;
        }
        
        $stmt = $conn->prepare(
            "UPDATE elections SET status = ? WHERE election_id = ?"
        );
        $stmt->bind_param("si", $status, $election_id);
        $stmt->execute();
        $success = $stmt->affected_rows >= 0;
        $stmt->close();
        
        return $success;
    }

    /**
     * Delete an election.
     *
     * @param mysqli $conn          Database connection
     * @param int    $election_id   Election to delete
     * @return bool                 true if deleted
     */
    public static function delete(mysqli $conn, int $election_id): bool
    {
        $stmt = $conn->prepare(
            "DELETE FROM elections WHERE election_id = ?"
        );
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
        
        return $success;
    }

    /**
     * Count elections by status.
     *
     * @param mysqli $conn  Database connection
     * @return array        ['active' => N, 'scheduled' => N, 'closed' => N]
     */
    public static function countByStatus(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT status, COUNT(*) as count 
             FROM elections GROUP BY status"
        );
        
        $counts = ['active' => 0, 'scheduled' => 0, 'closed' => 0];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['status']] = (int)$row['count'];
        }
        return $counts;
    }
}
