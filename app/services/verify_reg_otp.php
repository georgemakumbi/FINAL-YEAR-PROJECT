<?php
/**
 * =============================================================================
 * VERIFY REGISTRATION OTP — Registration Step 2
 * =============================================================================
 * Verifies the 6-digit OTP the student received on their university email.
 * On success, sets a session flag that allows Step 3 (set password).
 *
 * Security:
 *   - Requires an active reg_otp_student session (can't skip Step 1)
 *   - Rate-limited to 5 attempts (blocks and clears OTP after that)
 *   - OTP is cleared immediately after successful verification (one-time use)
 *   - Session flag reg_verified_student is the only gate to Step 3
 * =============================================================================
 */

if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php?step=verify');
    exit();
}

verify_csrf_or_die();

// ── 1. Ensure Step 1 was completed (student ID in session) ───────────────────
if (empty($_SESSION['reg_otp_student'])) {
    header('Location: register.php?error=' . urlencode(
        'Session expired. Please start registration again.'
    ));
    exit();
}

$student_id = $_SESSION['reg_otp_student'];

// ── 2. Rate limiting — max 5 attempts ────────────────────────────────────────
if (!isset($_SESSION['reg_otp_attempts'])) {
    $_SESSION['reg_otp_attempts'] = 0;
}
$_SESSION['reg_otp_attempts']++;

if ($_SESSION['reg_otp_attempts'] > 5) {
    // Clear everything and force them back to Step 1
    $clear = $conn->prepare('UPDATE students SET otp = NULL, otp_expiry = NULL WHERE student_id = ?');
    $clear->bind_param('s', $student_id);
    $clear->execute();
    $clear->close();

    unset(
        $_SESSION['reg_otp_student'],
        $_SESSION['reg_otp_attempts'],
        $_SESSION['reg_otp_sent'],
        $_SESSION['reg_masked_email'],
        $_SESSION['reg_student_name'],
        $_SESSION['reg_student_faculty'],
        $_SESSION['reg_student_dept']
    );

    header('Location: register.php?error=' . urlencode(
        'Too many failed attempts. Please restart registration.'
    ));
    exit();
}

// ── 3. Get the OTP entered by the student ────────────────────────────────────
$entered_otp = trim($_POST['otp'] ?? '');

if (empty($entered_otp)) {
    header('Location: register.php?step=verify&error=' . urlencode('Please enter the 6-digit code.'));
    exit();
}

// ── 4. Fetch OTP from database ────────────────────────────────────────────────
$stmt = $conn->prepare('SELECT otp, otp_expiry FROM students WHERE student_id = ?');
$stmt->bind_param('s', $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header('Location: register.php?error=' . urlencode('Invalid session. Please restart.'));
    exit();
}

$row       = $result->fetch_assoc();
$db_otp    = $row['otp'];
$db_expiry = $row['otp_expiry'];
$stmt->close();

// ── 5. Validate OTP code ──────────────────────────────────────────────────────
if ($entered_otp !== $db_otp) {
    $remaining = 5 - $_SESSION['reg_otp_attempts'];
    header('Location: register.php?step=verify&error=' . urlencode(
        "Incorrect code. {$remaining} attempt(s) remaining."
    ));
    exit();
}

// ── 6. Check OTP not expired ──────────────────────────────────────────────────
if (strtotime($db_expiry) < time()) {
    header('Location: register.php?error=' . urlencode(
        'Your verification code has expired. Please request a new one.'
    ));
    exit();
}

// ── 7. OTP is valid — clear it immediately (one-time use) ────────────────────
$clear = $conn->prepare('UPDATE students SET otp = NULL, otp_expiry = NULL WHERE student_id = ?');
$clear->bind_param('s', $student_id);
$clear->execute();
$clear->close();

// ── 8. Set the verified flag and advance to Step 3 ───────────────────────────
$_SESSION['reg_verified_student'] = $student_id;

// Clean up OTP tracking (no longer needed)
unset($_SESSION['reg_otp_student'], $_SESSION['reg_otp_attempts'], $_SESSION['reg_otp_sent']);

header('Location: register.php?step=set_password');
exit();
?>
