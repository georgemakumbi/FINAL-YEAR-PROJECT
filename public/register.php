<?php
/**
 * =============================================================================
 * TRUSTED REGISTRATION PAGE — 3-Step OTP-Verified Registration
 * =============================================================================
 * Step 1: Enter Student ID → lookup in university records
 * Step 2: Enter OTP sent to verified university email
 * Step 3: Set your password → account activated
 *
 * The active step is determined by URL param ?step= and session state.
 * Session guards prevent skipping steps.
 * =============================================================================
 */
require_once '../bootstrap.php';
ensure_csrf_token();

// ── Determine which step to show ─────────────────────────────────────────────
$url_step = $_GET['step'] ?? 'lookup';
$error    = htmlspecialchars($_GET['error']   ?? '');
$info     = htmlspecialchars($_GET['info']    ?? '');
$success  = htmlspecialchars($_GET['success'] ?? '');

// Enforce session gates — no step-skipping
if ($url_step === 'verify' && empty($_SESSION['reg_otp_student'])) {
    header('Location: register.php');
    exit();
}
if ($url_step === 'set_password' && empty($_SESSION['reg_verified_student'])) {
    header('Location: register.php');
    exit();
}

// Derive display data from session
$masked_email   = htmlspecialchars($_SESSION['reg_masked_email']    ?? '');
$student_name   = htmlspecialchars($_SESSION['reg_student_name']    ?? '');
$student_faculty = htmlspecialchars($_SESSION['reg_student_faculty'] ?? '');
$student_dept   = htmlspecialchars($_SESSION['reg_student_dept']    ?? '');

// Map step to numeric index for progress bar
$step_num = match($url_step) {
    'lookup'       => 1,
    'verify'       => 2,
    'set_password' => 3,
    default        => 1,
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Kyambogo University Voting System</title>
    <meta name="description" content="Register your student account on the Kyambogo University Online Voting System using your university-verified student ID.">
    <link rel="icon" href="../assets/images/image.png" type="image/png">
    <!-- PWA -->
    <link rel="manifest" href="/finalyearproject/public/manifest.json">
    <meta name="theme-color" content="#003366">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="KU Votes">
    <link rel="apple-touch-icon" href="/finalyearproject/assets/images/icons/icon-180.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--app-bg, #f0f4ff);
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle animated gradient background blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.12;
            z-index: 0;
            animation: float 8s ease-in-out infinite alternate;
        }
        body::before {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #003366, transparent);
            top: -100px; left: -100px;
        }
        body::after {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #ffc107, transparent);
            bottom: -80px; right: -80px;
            animation-delay: -4s;
        }
        @keyframes float {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.05); }
        }

        /* ── Card ── */
        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            background: var(--surface, #fff);
            border-radius: 24px;
            padding: 40px 36px 32px;
            box-shadow: 0 20px 60px rgba(0, 51, 102, 0.12), 0 4px 16px rgba(0,0,0,0.06);
            animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(32px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Top accent bar */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffc107 0%, #003366 100%);
            border-radius: 24px 24px 0 0;
        }

        /* ── Header ── */
        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
        }
        .brand-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #003366, #004d99);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(0,51,102,0.25);
        }
        .brand-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text, #1a1a2e);
        }
        .brand-sub {
            font-size: 0.82rem;
            color: var(--text-muted, #6b7280);
            font-weight: 500;
        }

        /* ── Step progress ── */
        .steps-track {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 32px;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .step-circle {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            border: 2.5px solid var(--border, #e5e7eb);
            background: var(--surface, #fff);
            color: var(--text-muted, #9ca3af);
            transition: all 0.35s ease;
        }
        .step-circle.done {
            background: #22c55e;
            border-color: #22c55e;
            color: #fff;
        }
        .step-circle.active {
            background: linear-gradient(135deg, #003366, #004d99);
            border-color: #003366;
            color: #fff;
            box-shadow: 0 4px 14px rgba(0,51,102,0.35);
            transform: scale(1.1);
        }
        .step-label {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--text-muted, #9ca3af);
            text-align: center;
            max-width: 60px;
            line-height: 1.2;
        }
        .step-label.active { color: #003366; }
        .step-connector {
            flex: 1;
            height: 2px;
            background: var(--border, #e5e7eb);
            margin: 0 4px;
            margin-bottom: 22px;
            transition: background 0.35s ease;
            min-width: 32px;
        }
        .step-connector.done { background: #22c55e; }

        /* ── Alert messages ── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 500;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }
        .alert-error   { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
        .alert-info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .alert-icon { font-size: 1.1rem; flex-shrink: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }

        /* ── Section heading ── */
        .section-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text, #1a1a2e);
            margin-bottom: 4px;
        }
        .section-sub {
            font-size: 0.83rem;
            color: var(--text-muted, #6b7280);
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* ── Form elements ── */
        .form-group { margin-bottom: 18px; }
        label {
            display: block;
            margin-bottom: 7px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text, #374151);
            letter-spacing: 0.01em;
        }
        label .req { color: #ef4444; margin-left: 2px; }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 13px 16px;
            border-radius: 12px;
            border: 1.5px solid var(--input-border, #d1d5db);
            background: var(--input-bg, #fafafa);
            color: var(--text, #111827);
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }
        input:focus {
            border-color: #003366;
            background: var(--surface, #fff);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.10);
        }
        input[readonly] {
            background: var(--surface-muted, #f3f4f6);
            color: var(--text-muted, #6b7280);
            cursor: default;
            border-style: dashed;
        }

        /* Password wrapper with toggle */
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 46px; }
        .pw-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            color: var(--text-muted, #9ca3af);
            padding: 4px;
            transition: color 0.2s;
        }
        .pw-toggle:hover { color: #003366; }

        /* Strength meter */
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            background: var(--border, #e5e7eb);
            overflow: hidden;
        }
        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease, background 0.3s ease;
            width: 0%;
        }
        .strength-label {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 5px;
            transition: color 0.3s;
        }

        /* OTP input row */
        .otp-row {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 8px;
        }
        .otp-digit {
            width: 48px; height: 56px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 800;
            border-radius: 12px;
            border: 2px solid var(--input-border, #d1d5db);
            background: var(--input-bg, #fafafa);
            color: var(--text, #111);
            font-family: 'Inter', monospace;
            caret-color: #003366;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
            outline: none;
        }
        .otp-digit:focus {
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0,51,102,0.12);
            transform: scale(1.06);
        }
        .otp-digit.filled { border-color: #22c55e; background: #f0fdf4; }

        .otp-hint {
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted, #6b7280);
            margin-bottom: 16px;
        }
        .otp-hint strong { color: #003366; }

        /* Info card (masked email, student info) */
        .info-card {
            background: linear-gradient(135deg, #f0f4ff, #e8f0fe);
            border: 1px solid #c7d7f8;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .info-card-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
        }
        .info-card-icon { font-size: 1rem; flex-shrink: 0; }
        .info-card-label { color: var(--text-muted, #6b7280); font-weight: 500; min-width: 80px; }
        .info-card-value { color: var(--text, #111827); font-weight: 700; }

        /* Profile card (Step 3) */
        .profile-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: linear-gradient(135deg, #003366, #004d99);
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 24px;
            color: #fff;
        }
        .profile-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .profile-name  { font-size: 1rem; font-weight: 800; }
        .profile-meta  { font-size: 0.78rem; opacity: 0.8; margin-top: 3px; line-height: 1.5; }

        /* ── Buttons ── */
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 800;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
            margin-top: 6px;
        }
        .btn:hover  { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,51,102,0.25); }
        .btn:active { transform: translateY(0); }

        .btn-primary {
            background: linear-gradient(135deg, #003366 0%, #004d99 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(0,51,102,0.3);
        }

        .btn-ghost {
            background: transparent;
            color: #003366;
            border: 1.5px solid #003366;
            margin-top: 10px;
            font-size: 0.88rem;
        }
        .btn-ghost:hover { background: #f0f4ff; box-shadow: none; }

        /* ── Footer link ── */
        .footer-link {
            text-align: center;
            margin-top: 18px;
            font-size: 0.84rem;
            color: var(--text-muted, #6b7280);
        }
        .footer-link a {
            color: #003366;
            font-weight: 700;
            text-decoration: none;
        }
        .footer-link a:hover { text-decoration: underline; }

        /* ── Resend timer ── */
        .resend-wrap {
            text-align: center;
            margin-top: 12px;
            font-size: 0.82rem;
            color: var(--text-muted, #6b7280);
        }
        #resend-btn {
            background: none; border: none;
            color: #003366; font-weight: 700;
            cursor: pointer; font-size: 0.82rem;
            text-decoration: underline;
            display: none;
        }
        #resend-btn.visible { display: inline; }
        #resend-timer { font-weight: 600; }

        /* ── Responsive ── */
        @media (max-width: 420px) {
            .card { padding: 28px 20px 24px; }
            .otp-digit { width: 40px; height: 50px; font-size: 1.2rem; }
        }
    </style>
</head>
<body>
<div class="card" role="main">

    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">🏛️</div>
        <div class="brand-title">Create Account</div>
        <div class="brand-sub">Kyambogo University Voting System</div>
    </div>

    <!-- Step progress tracker -->
    <div class="steps-track" aria-label="Registration progress">
        <div class="step-item">
            <div class="step-circle <?= $step_num > 1 ? 'done' : ($step_num === 1 ? 'active' : '') ?>">
                <?= $step_num > 1 ? '✓' : '1' ?>
            </div>
            <div class="step-label <?= $step_num === 1 ? 'active' : '' ?>">Student ID</div>
        </div>
        <div class="step-connector <?= $step_num > 1 ? 'done' : '' ?>"></div>
        <div class="step-item">
            <div class="step-circle <?= $step_num > 2 ? 'done' : ($step_num === 2 ? 'active' : '') ?>">
                <?= $step_num > 2 ? '✓' : '2' ?>
            </div>
            <div class="step-label <?= $step_num === 2 ? 'active' : '' ?>">Verify Email</div>
        </div>
        <div class="step-connector <?= $step_num > 2 ? 'done' : '' ?>"></div>
        <div class="step-item">
            <div class="step-circle <?= $step_num === 3 ? 'active' : '' ?>">3</div>
            <div class="step-label <?= $step_num === 3 ? 'active' : '' ?>">Set Password</div>
        </div>
    </div>

    <!-- Alert messages -->
    <?php if ($error): ?>
    <div class="alert alert-error" role="alert">
        <span class="alert-icon">⚠️</span>
        <span><?= $error ?></span>
    </div>
    <?php endif; ?>
    <?php if ($info): ?>
    <div class="alert alert-info" role="alert">
        <span class="alert-icon">ℹ️</span>
        <span><?= $info ?></span>
    </div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success" role="alert">
        <span class="alert-icon">✅</span>
        <span><?= $success ?></span>
    </div>
    <?php endif; ?>


    <!-- ================================================================
         STEP 1: Student ID Lookup
         ================================================================ -->
    <?php if ($url_step === 'lookup'): ?>

    <div class="section-title">Find Your Record</div>
    <div class="section-sub">
        Enter your student number. We'll look you up in the university registry and send a one-time code to your verified university email.
    </div>

    <form action="lookup_student.php" method="POST" id="lookupForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <label for="student_id">Student Number <span class="req">*</span></label>
            <input
                type="text"
                id="student_id"
                name="student_id"
                placeholder="e.g., 23/U/12345"
                required
                autocomplete="username"
                autofocus
                inputmode="text"
                aria-describedby="student_id_help"
            >
            <div id="student_id_help" style="font-size:0.78rem;color:var(--text-muted,#6b7280);margin-top:5px;">
                Exactly as printed on your student ID card.
            </div>
        </div>

        <button type="submit" class="btn btn-primary" id="lookupBtn">
            <span id="lookupBtnText">🔍 Look Up My Record</span>
        </button>
    </form>

    <div class="footer-link">
        Already have an account? <a href="login.php">Sign in</a>
    </div>


    <!-- ================================================================
         STEP 2: OTP Verification
         ================================================================ -->
    <?php elseif ($url_step === 'verify'): ?>

    <div class="section-title">Verify Your Identity</div>
    <div class="section-sub">
        We sent a 6-digit code to your university email address. Enter it below to confirm you own this account.
    </div>

    <!-- Masked email info -->
    <div class="info-card" role="status" aria-live="polite">
        <div class="info-card-row">
            <span class="info-card-icon">📧</span>
            <span class="info-card-label">Sent to:</span>
            <span class="info-card-value"><?= $masked_email ?: '...@std.kyu.ac.ug' ?></span>
        </div>
        <div class="info-card-row" style="font-size:0.78rem;color:var(--text-muted,#6b7280);margin-left:26px;">
            Check your inbox — the code expires in <strong>10 minutes</strong>.
        </div>
    </div>

    <form action="verify_reg_otp.php" method="POST" id="otpForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <!-- Hidden combined OTP field, auto-filled by JS -->
        <input type="hidden" name="otp" id="otp_combined">

        <label style="text-align:center;display:block;margin-bottom:12px;">
            Enter 6-Digit Code
        </label>

        <!-- 6 individual digit inputs -->
        <div class="otp-row" id="otp-row" aria-label="Enter 6-digit OTP code">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <input
                type="text"
                class="otp-digit"
                id="otp_<?= $i ?>"
                maxlength="1"
                inputmode="numeric"
                pattern="[0-9]"
                autocomplete="<?= $i === 1 ? 'one-time-code' : 'off' ?>"
                aria-label="Digit <?= $i ?>"
            >
            <?php endfor; ?>
        </div>

        <div class="otp-hint">
            Didn't get it? Check your <strong>Spam</strong> folder too.
        </div>

        <button type="submit" class="btn btn-primary" id="verifyBtn" disabled>
            ✅ Verify Code
        </button>
    </form>

    <!-- Resend OTP -->
    <div class="resend-wrap">
        <span id="resend-timer">Resend in <strong id="countdown">60</strong>s</span>
        <form action="lookup_student.php" method="POST" id="resendForm" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($_SESSION['reg_otp_student'] ?? '') ?>">
            <button type="submit" id="resend-btn" class="btn-ghost" style="width:auto;padding:0;border:none;margin:0;">
                Resend code
            </button>
        </form>
    </div>

    <button type="button" class="btn btn-ghost" onclick="window.location='register.php'" style="margin-top:16px;">
        ← Start Over
    </button>


    <!-- ================================================================
         STEP 3: Set Password
         ================================================================ -->
    <?php elseif ($url_step === 'set_password'): ?>

    <?php
        // Fetch fresh name from session (set during lookup)
        $verified_id = htmlspecialchars($_SESSION['reg_verified_student'] ?? '');
    ?>

    <!-- Student identity card -->
    <?php if ($student_name || $student_faculty): ?>
    <div class="profile-card" aria-label="Your verified student details">
        <div class="profile-avatar">🎓</div>
        <div>
            <div class="profile-name"><?= $student_name ?: $verified_id ?></div>
            <div class="profile-meta">
                <?= $verified_id ?>
                <?php if ($student_faculty): ?> · <?= $student_faculty ?><?php endif; ?>
                <?php if ($student_dept): ?><br><?= $student_dept ?><?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="info-card">
        <div class="info-card-row">
            <span class="info-card-icon">🎓</span>
            <span class="info-card-label">Student ID:</span>
            <span class="info-card-value"><?= $verified_id ?></span>
        </div>
    </div>
    <?php endif; ?>

    <div class="section-title">Set Your Password</div>
    <div class="section-sub">
        Choose a strong password for your account. You'll use it with your student ID to log in.
    </div>

    <form action="submit_register.php" method="POST" id="pwForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <label for="password">Password <span class="req">*</span></label>
            <div class="pw-wrap">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="At least 8 characters"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    autofocus
                    aria-describedby="pw_strength_label"
                >
                <button type="button" class="pw-toggle" id="toggle1" aria-label="Show/hide password" onclick="togglePw('password','toggle1')">👁️</button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
            <div class="strength-label" id="pw_strength_label"></div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password <span class="req">*</span></label>
            <div class="pw-wrap">
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Re-enter your password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                >
                <button type="button" class="pw-toggle" id="toggle2" aria-label="Show/hide confirm password" onclick="togglePw('confirm_password','toggle2')">👁️</button>
            </div>
            <div id="match-hint" style="font-size:0.78rem;margin-top:5px;"></div>
        </div>

        <button type="submit" class="btn btn-primary" id="createBtn">
            🚀 Create My Account
        </button>
    </form>

    <?php endif; ?>

</div><!-- /card -->

<script src="../assets/js/theme.js" defer></script>
<script src="/finalyearproject/assets/js/pwa.js" defer></script>
<script>
/* ── OTP digit inputs: auto-advance, paste handling, backspace ── */
(function () {
    const digits   = Array.from(document.querySelectorAll('.otp-digit'));
    const combined = document.getElementById('otp_combined');
    const verifyBtn = document.getElementById('verifyBtn');
    if (!digits.length) return;

    function getCode() { return digits.map(d => d.value).join(''); }

    function updateState() {
        const code = getCode();
        digits.forEach(d => {
            d.classList.toggle('filled', d.value !== '');
        });
        if (combined) combined.value = code;
        if (verifyBtn) verifyBtn.disabled = code.length < 6;
    }

    digits.forEach((input, idx) => {
        input.addEventListener('input', function (e) {
            // Handle paste into single cell
            const val = this.value.replace(/\D/g, '');
            if (val.length > 1) {
                // Distribute across cells
                val.split('').forEach((ch, i) => {
                    if (digits[idx + i]) digits[idx + i].value = ch;
                });
                const next = digits[Math.min(idx + val.length, digits.length - 1)];
                next.focus();
            } else {
                this.value = val.slice(0, 1);
                if (val && idx < digits.length - 1) digits[idx + 1].focus();
            }
            updateState();
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (!this.value && idx > 0) {
                    digits[idx - 1].focus();
                    digits[idx - 1].value = '';
                    updateState();
                }
            }
            if (e.key === 'ArrowLeft'  && idx > 0)               digits[idx - 1].focus();
            if (e.key === 'ArrowRight' && idx < digits.length - 1) digits[idx + 1].focus();
        });

        // Handle paste on any digit
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
            pasted.split('').forEach((ch, i) => {
                if (digits[idx + i]) digits[idx + i].value = ch;
            });
            const focusIdx = Math.min(idx + pasted.length, digits.length - 1);
            digits[focusIdx].focus();
            updateState();
        });
    });
})();

/* ── Resend OTP countdown ── */
(function () {
    const countdownEl = document.getElementById('countdown');
    const timerEl     = document.getElementById('resend-timer');
    const resendBtn   = document.getElementById('resend-btn');
    if (!countdownEl) return;

    let secs = 60;
    const tick = setInterval(() => {
        secs--;
        countdownEl.textContent = secs;
        if (secs <= 0) {
            clearInterval(tick);
            timerEl.style.display = 'none';
            resendBtn.classList.add('visible');
        }
    }, 1000);
})();

/* ── Password strength meter ── */
(function () {
    const pwInput  = document.getElementById('password');
    const confInput = document.getElementById('confirm_password');
    const fill     = document.getElementById('strength-fill');
    const label    = document.getElementById('pw_strength_label');
    const matchHint = document.getElementById('match-hint');
    if (!pwInput) return;

    function score(pw) {
        let s = 0;
        if (pw.length >= 8)  s++;
        if (pw.length >= 12) s++;
        if (/[A-Z]/.test(pw)) s++;
        if (/[0-9]/.test(pw)) s++;
        if (/[^a-zA-Z0-9]/.test(pw)) s++;
        return s;
    }

    const levels = [
        { pct: '0%',   color: '#e5e7eb', text: '',         col: '' },
        { pct: '20%',  color: '#ef4444', text: 'Weak',     col: '#ef4444' },
        { pct: '40%',  color: '#f97316', text: 'Fair',     col: '#f97316' },
        { pct: '60%',  color: '#eab308', text: 'Good',     col: '#eab308' },
        { pct: '80%',  color: '#22c55e', text: 'Strong',   col: '#22c55e' },
        { pct: '100%', color: '#16a34a', text: 'Excellent',col: '#16a34a' },
    ];

    pwInput.addEventListener('input', function () {
        const s = score(this.value);
        const lv = levels[Math.min(s, 5)];
        fill.style.width = lv.pct;
        fill.style.background = lv.color;
        label.textContent = lv.text;
        label.style.color  = lv.col;
        checkMatch();
    });

    function checkMatch() {
        if (!matchHint) return;
        if (!confInput.value) { matchHint.textContent = ''; return; }
        if (pwInput.value === confInput.value) {
            matchHint.textContent = '✓ Passwords match';
            matchHint.style.color = '#16a34a';
        } else {
            matchHint.textContent = '✗ Passwords do not match';
            matchHint.style.color = '#dc2626';
        }
    }
    if (confInput) confInput.addEventListener('input', checkMatch);
})();

/* ── Password show/hide toggle ── */
function togglePw(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn   = document.getElementById(btnId);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.textContent = isHidden ? '🙈' : '👁️';
    btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
}

/* ── Loading spinner on form submit ── */
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function () {
        const btn = this.querySelector('.btn-primary');
        if (!btn) return;
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
        const span = btn.querySelector('span') || btn;
        const oldHTML = btn.innerHTML;
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 0.8s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Processing…';
    });
});
</script>
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
</body>
</html>
