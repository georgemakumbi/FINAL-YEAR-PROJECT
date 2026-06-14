<?php
/**
 * =============================================================================
 * VOTE MODEL — All Database Operations for Votes
 * =============================================================================
 * 
 * The votes table is the most important table in the system.
 * It records every single vote cast.
 *
 * INTEGRITY RULES (enforced by the database):
 *   1. UNIQUE(receipt_token, position) → One vote per anonymous receipt token per position
 *      (preserves one-vote-per-voter property while keeping votes anonymous).
 *   2. FOREIGN KEY → candidate_id must exist in candidates table
 *
 * =============================================================================
 */

class Vote
{
    /**
     * Record a vote.
     * 
     * Inserts a new vote record into the votes table.
     * This should ONLY be called inside a transaction (from processvote.php).
     *
    * @param mysqli $conn          Database connection (should be in a transaction)
    * @param string $receipt_token Anonymous receipt token representing the vote
     * @param int    $candidate_id  Who they're voting for
     * @param string $position      Which position
     * @return bool                 true if vote was recorded
     */
    public static function cast(mysqli $conn, string $receipt_token, int $candidate_id, string $position): bool
    {
        $stmt = $conn->prepare(
            "INSERT INTO votes (receipt_token, candidate_id, position, vote_date) VALUES (?, ?, ?, CURDATE())"
        );
        $stmt->bind_param("sis", $receipt_token, $candidate_id, $position);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
        
        return $success;
    }

    /**
     * Get all votes cast under a specific anonymous receipt token.
     * 
     * Used for anonymous vote verification.
     *
     * @param mysqli $conn           Database connection
     * @param string $receipt_token  The anonymous verification token
     * @return array                 Array of vote records
     */
    public static function getByReceiptToken(mysqli $conn, string $receipt_token): array
    {
        $stmt = $conn->prepare(
            "SELECT v.vote_id, v.position, v.vote_date, 
                    c.first_name, c.last_name
             FROM votes v
             JOIN candidates c ON v.candidate_id = c.candidate_id
             WHERE v.receipt_token = ?
             ORDER BY v.position"
        );
        $stmt->bind_param("s", $receipt_token);
        $stmt->execute();
        $result = $stmt->get_result();
        $votes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $votes;
    }

    /**
     * Count total votes cast across all positions.
     *
     * @param mysqli $conn  Database connection
     * @return int          Total votes
     */
    public static function countAll(mysqli $conn): int
    {
        $result = $conn->query("SELECT COUNT(*) as total FROM votes");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Count votes for a specific position.
     *
     * @param mysqli $conn      Database connection
     * @param string $position  Position name
     * @return int              Vote count for that position
     */
    public static function countByPosition(mysqli $conn, string $position): int
    {
        $stmt = $conn->prepare(
            "SELECT COUNT(*) as total FROM votes WHERE position = ?"
        );
        $stmt->bind_param("s", $position);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return (int)$row['total'];
    }

    /**
     * Get voting statistics by position.
     * 
     * Returns vote counts grouped by position — useful for analytics dashboard.
     *
     * @param mysqli $conn  Database connection
     * @return array        Array of ['position' => '...', 'vote_count' => N]
     */
    public static function getStatsByPosition(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT position, COUNT(*) as vote_count 
             FROM votes 
             GROUP BY position 
             ORDER BY position"
        );
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get voting timeline (votes per hour).
     * 
     * Useful for showing when most voting activity happened.
     * Great for your final year report charts!
     *
     * @param mysqli $conn  Database connection
     * @return array        Array of ['hour' => '2026-05-04 14:00', 'count' => N]
     */
    public static function getTimeline(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT DATE_FORMAT(vote_date, '%Y-%m-%d %H:00') as hour, 
                    COUNT(*) as count
             FROM votes 
             GROUP BY hour 
             ORDER BY hour"
        );
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
