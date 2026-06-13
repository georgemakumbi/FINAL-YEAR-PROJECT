<?php
/**
 * =============================================================================
 * VOTE PROCESSOR — The Heart of the Voting System
 * =============================================================================
 * 
 * WHAT THIS FILE DOES:
 *   This is the MOST CRITICAL file in the entire system. It handles the
 *   actual vote casting when a student submits their ballot.
 *
 * THE COMPLETE FLOW:
 *   
 *   Student clicks "Submit Votes" on voting.php
 *           │
 *           ▼
 *   ┌─────────────────────────────────────┐
 *   │  1. Is user logged in?              │──No──→ Redirect to login
 *   │  2. Is this a POST request?         │──No──→ Redirect to voting
 *   │  3. Is CSRF token valid?            │──No──→ 403 Forbidden
 *   │  4. Is election still active?       │──No──→ "Election ended"
 *   │  5. BEGIN TRANSACTION               │
 *   │  6. Lock student row (FOR UPDATE)   │
 *   │  7. Has student already voted?      │──Yes─→ Redirect to results
 *   │  8. Validate each vote:             │
 *   │     - Candidate exists?             │
 *   │     - Position matches?             │
 *   │     - Department eligible?          │
 *   │  9. Insert votes into votes table   │
 *   │ 10. Increment candidate vote counts │
 *   │ 11. Mark student as has_voted       │
 *   │ 12. COMMIT TRANSACTION              │
 *   │ 13. Log audit event                 │
 *   │ 14. Redirect to results             │
 *   └─────────────────────────────────────┘
 *           │ (if ANY step fails)
 *           ▼
 *   ROLLBACK — Undo everything, redirect with error
 *
 * KEY SECURITY FEATURES:
 *   - CSRF token verification (prevents cross-site form submissions)
 *   - Database transaction (all votes saved or none — atomic operation)
 *   - Row-level locking (prevents race conditions / double-voting)
 *   - Server-side validation (never trust the browser!)
 *   - Department eligibility checks
 *   - Audit logging
 *
 * =============================================================================
 */

// ─── Load Dependencies ───────────────────────────────────────────────────────
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';
require_once APP_MIDDLEWARE . '/admin_security.php';
require_once VIEWS_COMPONENTS . '/includes/audit_logger.php';

// =============================================================================
// SECURITY CHECK 1: Is the user logged in?
// =============================================================================
// We check if student_id exists in the session.
// Without this, anyone could submit votes without being a student!
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

// =============================================================================
// SECURITY CHECK 2: Is this a POST request?
// =============================================================================
// Votes must be submitted via POST (form submission).
// If someone tries to access this URL directly (GET request), reject it.
// This prevents accidental or bookmarked access to the vote processor.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: voting.php?error=Invalid request");
    exit();
}

// =============================================================================
// SECURITY CHECK 3: Is the CSRF token valid?
// =============================================================================
// This function (from admin_security.php) compares the token from the
// form with the token stored in the session. If they don't match,
// the request was forged by a malicious website → block it!
verify_csrf_or_die();

// =============================================================================
// SECURITY CHECK 4: Is the election still active?
// =============================================================================
// Query the database for the active election's end date.
// If voting time has passed, don't accept any more votes.
date_default_timezone_set('Africa/Kampala');
$election_check = $conn->query(
    "SELECT end_date FROM elections WHERE status = 'active' ORDER BY end_date DESC LIMIT 1"
);
if ($election_check && $election_row = $election_check->fetch_assoc()) {
    $election_end = strtotime($election_row['end_date']);
    if (time() > $election_end) {
        header("Location: voting.php?error=The election has ended. Voting is no longer allowed.");
        exit();
    }
}

// ─── Get the Student ID from Session ─────────────────────────────────────────
$student_id = $_SESSION['student_id'];

// Generate a cryptographically secure 32-character hexadecimal token for this ballot
$receipt_token = bin2hex(random_bytes(16));

// =============================================================================
// MAIN VOTE PROCESSING (Inside a try-catch block)
// =============================================================================
// Everything below is wrapped in try-catch because:
//   - If ANYTHING goes wrong, we catch the error
//   - ROLLBACK the transaction (undo partial changes)
//   - Show a user-friendly error message
//   - Log the error for debugging
try {
    // ─── Enable Strict Error Reporting ───────────────────────────────────────
    // By default, MySQL errors in PHP might fail silently.
    // This setting makes PHP THROW an exception on any MySQL error,
    // which our try-catch block will handle properly.
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    // ─── BEGIN TRANSACTION ───────────────────────────────────────────────────
    // A transaction groups multiple database operations into ONE atomic unit.
    //
    // WHAT DOES "ATOMIC" MEAN?
    //   Like an atom (indivisible) — either ALL operations succeed,
    //   or NONE of them do. There's no "half-done" state.
    //
    // WHY IS THIS CRITICAL FOR VOTING?
    //   Without a transaction, imagine this scenario:
    //   1. ✅ INSERT vote for Guild President     → Saved!
    //   2. ✅ UPDATE candidate vote count         → Updated!
    //   3. ❌ INSERT vote for Secretary            → FAILS (server crash!)
    //   4. ❓ Student marked as has_voted         → Never runs
    //   
    //   Result: Student voted for President but not Secretary.
    //   They can't try again because their President vote is already recorded.
    //   But they're not marked as has_voted either — inconsistent data!
    //
    //   With a transaction:
    //   1. INSERT vote for President     → Pending...
    //   2. UPDATE candidate count        → Pending...
    //   3. INSERT vote for Secretary     → FAILS!
    //   4. ROLLBACK → Steps 1 & 2 are UNDONE
    //   5. Student can try again — clean state!
    $conn->begin_transaction();

    // ─── Lock Student Row to Prevent Double-Voting ───────────────────────────
    // "FOR UPDATE" is a row-level lock that says:
    // "Nobody else can modify this row until my transaction finishes."
    //
    // WHY DO WE NEED THIS?
    //   Imagine two browser tabs submitting votes at the EXACT same time:
    //   
    //   Tab 1: SELECT has_voted → 0 (not voted yet)
    //   Tab 2: SELECT has_voted → 0 (not voted yet — same moment!)
    //   Tab 1: INSERT votes... UPDATE has_voted = 1
    //   Tab 2: INSERT votes... UPDATE has_voted = 1  ← DOUBLE VOTE!
    //   
    //   With "FOR UPDATE":
    //   Tab 1: SELECT has_voted FOR UPDATE → 0 (locks the row)
    //   Tab 2: SELECT has_voted FOR UPDATE → WAITS... (row is locked)
    //   Tab 1: INSERT votes... COMMIT (releases lock)
    //   Tab 2: SELECT has_voted FOR UPDATE → 1 (now it reads the updated value)
    //   Tab 2: Already voted! → Redirect to results
    $check_voted = $conn->prepare(
        "SELECT has_voted FROM students WHERE student_id = ? FOR UPDATE"
    );
    $check_voted->bind_param("s", $student_id);
    $check_voted->execute();
    $check_voted->bind_result($has_voted);
    $student_found = $check_voted->fetch();
    $check_voted->close();

    // Verify the student exists in the database
    if (!$student_found) {
        throw new Exception("Student record not found.");
    }

    // If student has already voted, redirect to results
    if ($has_voted) {
        $conn->rollback(); // Release the lock
        header("Location: results.php");
        exit();
    }

    // ─── Extract Votes from POST Data ────────────────────────────────────────
    // The form in voting.php submits data like:
    //   $_POST = [
    //       'csrf_token'      => 'a8f2b4c6...',   ← Skip this
    //       'Guild President'  => '5',             ← Candidate ID 5
    //       'Guild Secretary'  => '12',            ← Candidate ID 12
    //   ]
    //
    // We loop through all POST data and collect the votes,
    // skipping 'submit' and 'csrf_token' which aren't votes.
    $submitted_votes = [];
    foreach ($_POST as $position => $candidate_id) {
        if ($position === 'submit' || $position === 'csrf_token') {
            continue; // Skip non-vote fields
        }
        $submitted_votes[$position] = $candidate_id;
    }

    // Must have at least one vote
    if (empty($submitted_votes)) {
        throw new Exception("No votes submitted.");
    }

    // ─── Prepare SQL Statements ──────────────────────────────────────────────
    // We prepare statements ONCE and reuse them in the loop.
    // This is more efficient than preparing inside the loop.
    //
    // validate_candidate → Check if the candidate exists and matches position
    // insert_vote        → Record the vote
    // update_candidate   → Increment the candidate's vote count
    $validate_candidate = $conn->prepare(
        "SELECT position, department, is_university_wide 
         FROM candidates WHERE candidate_id = ?"
    );
    $insert_vote = $conn->prepare(
        "INSERT INTO votes (receipt_token, candidate_id, position, vote_date) VALUES (?, ?, ?, CURDATE())"
    );
    $update_candidate = $conn->prepare(
        "UPDATE candidates SET votes = votes + 1 WHERE candidate_id = ?"
    );
    $seen_positions = []; // Track positions to prevent duplicate submissions

    // Get voter's department for eligibility checks
    $voter_department = isset($_SESSION['department']) ? $_SESSION['department'] : '';

    // ─── Process Each Vote ───────────────────────────────────────────────────
    foreach ($submitted_votes as $position_from_form => $candidate_id_raw) {
        
        // --- Normalize the position name ---
        // PHP automatically converts spaces in POST field names to underscores!
        // So "Guild President" becomes "Guild_President" in $_POST.
        // We need to convert it back to match the database value.
        $position_from_form = (string)$position_from_form;
        $position_from_form = str_replace('_', ' ', $position_from_form);
        $position_key = strtolower(trim($position_from_form));
        
        // --- Validate the candidate ID ---
        // filter_var with FILTER_VALIDATE_INT ensures the value is a real integer.
        // This prevents injection of non-numeric values like "5; DROP TABLE votes"
        $candidate_id = filter_var($candidate_id_raw, FILTER_VALIDATE_INT);

        if ($position_key === '' || $candidate_id === false || $candidate_id <= 0) {
            throw new Exception("Invalid vote payload.");
        }
        
        // --- Check for duplicate position votes ---
        // A student should only vote once per position.
        // If the form somehow submitted two votes for "Guild President", reject it.
        if (isset($seen_positions[$position_key])) {
            throw new Exception("Duplicate vote position submitted.");
        }
        $seen_positions[$position_key] = true;

        // --- Validate the candidate exists and matches the position ---
        $validate_candidate->bind_param("i", $candidate_id);
        $validate_candidate->execute();
        $validate_candidate->store_result();
        if ($validate_candidate->num_rows !== 1) {
            throw new Exception("Selected candidate was not found.");
        }
        $validate_candidate->bind_result(
            $candidate_position, 
            $candidate_department, 
            $is_university_wide
        );
        $validate_candidate->fetch();
        $validate_candidate->free_result();

        // --- Verify position matches ---
        // The position from the form MUST match the candidate's position in the DB.
        // This prevents tampering where someone changes the form to assign
        // a candidate to a different position.
        $candidate_position_normalized = strtolower(trim((string)$candidate_position));
        
        if ($candidate_position_normalized === '' || $candidate_position_normalized !== $position_key) {
            throw new Exception(
                "Candidate does not match selected position. " .
                "Form: '$position_from_form' vs DB: '$candidate_position'"
            );
        }

        // --- Department eligibility check ---
        // University-wide positions (Guild President, Vice President):
        //   → ALL students can vote, regardless of department
        // Department positions (Faculty Rep, Class Rep):
        //   → Only students in the SAME department can vote
        if ($is_university_wide != 1) {
            if ($candidate_department !== $voter_department) {
                throw new Exception(
                    "You can only vote for candidates in your department " .
                    "($voter_department) for this position."
                );
            }
        }

        // --- Record the vote ---
        // INSERT into the votes table: who voted, for whom, for which position
        $insert_vote->bind_param("sis", $receipt_token, $candidate_id, $candidate_position);
        $insert_vote->execute();

        // --- Update the candidate's running vote count ---
        // votes = votes + 1 is an atomic increment — safe even with concurrent access
        $update_candidate->bind_param("i", $candidate_id);
        $update_candidate->execute();
        if ($update_candidate->affected_rows !== 1) {
            throw new Exception("Failed to update candidate vote count.");
        }
    }

    // ─── Clean Up Prepared Statements ────────────────────────────────────────
    $validate_candidate->close();
    $insert_vote->close();
    $update_candidate->close();

    // ─── Mark Student as "Has Voted" ─────────────────────────────────────────
    // This prevents the student from voting again.
    // Combined with the UNIQUE constraint in the votes table,
    // this provides TWO layers of double-vote prevention.
    $mark_voted = $conn->prepare(
        "UPDATE students SET has_voted = TRUE WHERE student_id = ?"
    );
    $mark_voted->bind_param("s", $student_id);
    $mark_voted->execute();
    $mark_voted->close();
    
    // ─── COMMIT TRANSACTION ──────────────────────────────────────────────────
    // All operations succeeded! Make them permanent.
    // Before this point, all changes were "pending" — they could be undone.
    // After commit(), the changes are saved permanently in the database.
    $conn->commit();
    
    // ─── Log the Vote Event ──────────────────────────────────────────────────
    // Record that this student voted (but NOT who they voted for!)
    // We only log the NUMBER of positions voted in, not specific choices.
    // This preserves vote secrecy while maintaining accountability.
    log_audit_event(
        $conn,
        (string)$student_id,
        'VOTE_CAST',
        'Student submitted votes for ' . count($submitted_votes) . ' position(s)'
    );
    
    // ─── Update Session and Redirect ─────────────────────────────────────────
    $_SESSION['has_voted'] = 1;
    $_SESSION['latest_receipt_token'] = $receipt_token;
    header("Location: results.php");
    exit();
    
} catch (Throwable $e) {
    // ==========================================================================
    // ERROR HANDLING — Something went wrong!
    // ==========================================================================
    // Throwable catches both Exception and Error types.
    //
    // 1. ROLLBACK the transaction → Undo all pending database changes
    //    This ensures no partial votes are saved.
    // 2. LOG the error → For debugging (only admins see error logs)
    // 3. REDIRECT with error message → User-friendly feedback
    $conn->rollback();
    
    // error_log() writes to PHP's error log file (not visible to users)
    // Located at: c:\wamp64\logs\php_error.log
    error_log("Vote processing error for student {$student_id}: " . $e->getMessage());
    
    // Redirect back to voting page with the error message
    $reason = rawurlencode($e->getMessage());
    header("Location: voting.php?error=Error processing your vote. " . $reason);
    exit();
}
?>
