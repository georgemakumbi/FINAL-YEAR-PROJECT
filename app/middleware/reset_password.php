<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

require_once APP_UTILS . '/db_connection.php';

if (!isset($_SESSION['verified_student'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['verified_student'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Kyambogo University Voting</title>
    <meta name="description" content="Create a new password for your Kyambogo University Voting account">
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <style>
        /* ── Layout ────────────────────────────────────────────────────────── */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--app-bg);
            padding: 20px;
            margin: 0;
        }

        .card {
            width: 100%;
            max-width: 460px;
            background: var(--surface);
            border-radius: var(--radius-xl, 24px);
            padding: 48px 40px 40px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        /* Premium accent bar */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--brand-accent, #ffc107), var(--brand-primary, #003366));
        }

        /* ── Logo ──────────────────────────────────────────────────────────── */
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            height: 68px;
            width: auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1));
        }

        /* ── Icon bubble ───────────────────────────────────────────────────── */
        .icon-bubble {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--brand-success-light, #d1fae5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-bubble svg {
            width: 32px;
            height: 32px;
            color: var(--brand-success, #059669);
        }
        body.dark .icon-bubble {
            background: rgba(16, 185, 129, 0.15);
        }

        /* ── Headings ──────────────────────────────────────────────────────── */
        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            text-align: center;
            margin: 0 0 6px;
            letter-spacing: -0.4px;
        }
        .card-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            text-align: center;
            margin: 0 0 28px;
            line-height: 1.5;
        }

        /* ── Success step badge ────────────────────────────────────────────── */
        .step-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: var(--brand-success-light, #d1fae5);
            color: var(--brand-success, #059669);
            border-radius: var(--radius-full);
            padding: 6px 14px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            width: fit-content;
            margin: 0 auto 20px;
        }
        body.dark .step-badge {
            background: rgba(16, 185, 129, 0.15);
        }

        /* ── Alert ─────────────────────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
            line-height: 1.4;
        }
        .alert svg { flex-shrink: 0; margin-top: 1px; }
        .alert.error {
            background: var(--brand-danger-light, #fee2e2);
            color: var(--brand-danger, #dc2626);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Form ──────────────────────────────────────────────────────────── */
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        /* Password input with toggle */
        .input-wrapper {
            position: relative;
        }
        .input-wrapper svg.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--text-muted);
            pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrapper input {
            width: 100%;
            padding: 13px 46px 13px 42px;
            font-size: 0.95rem;
            font-family: inherit;
            background: var(--input-bg);
            color: var(--text);
            border: 1.5px solid var(--input-border);
            border-radius: var(--radius);
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        .input-wrapper input::placeholder { color: var(--text-light); }
        .input-wrapper input:focus {
            outline: none;
            border-color: var(--brand-primary, #003366);
            box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.1);
        }
        .input-wrapper input.valid {
            border-color: var(--brand-success, #059669);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }
        .input-wrapper input.invalid {
            border-color: var(--brand-danger, #dc2626);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        /* show/hide eye button */
        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .toggle-pw:hover { color: var(--brand-primary); }
        .toggle-pw svg { width: 18px; height: 18px; }

        /* ── Password strength bar ─────────────────────────────────────────── */
        .strength-wrap {
            margin-top: 10px;
        }
        .strength-bar-track {
            height: 4px;
            background: var(--border);
            border-radius: 4px;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.4s ease, background 0.4s ease;
            width: 0%;
        }
        .strength-label {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 5px;
            color: var(--text-muted);
            transition: color 0.3s;
        }

        /* ── Requirements checklist ────────────────────────────────────────── */
        .requirements {
            margin-top: 12px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 12px;
        }
        .req-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            color: var(--text-muted);
            transition: color 0.2s;
        }
        .req-item.met { color: var(--brand-success, #059669); }
        .req-item svg {
            width: 13px; height: 13px;
            flex-shrink: 0;
            border: 1.5px solid currentColor;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .req-item.met svg { background: var(--brand-success, #059669); border-color: var(--brand-success, #059669); color: #fff; }

        /* ── Match indicator ───────────────────────────────────────────────── */
        .match-indicator {
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 6px;
            display: none;
        }
        .match-indicator.ok  { color: var(--brand-success); display: block; }
        .match-indicator.no  { color: var(--brand-danger);  display: block; }

        /* ── Submit Button ─────────────────────────────────────────────────── */
        .btn-submit {
            width: 100%;
            padding: 14px 16px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            background: var(--gradient-primary, linear-gradient(135deg, #003366, #004d99));
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 8px;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover {
            opacity: 0.92;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 51, 102, 0.25);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }

        .spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Divider ───────────────────────────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Back link ─────────────────────────────────────────────────────── */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--brand-primary); text-decoration: none; }
        .back-link svg { width: 15px; height: 15px; }

        /* ── Responsive ────────────────────────────────────────────────────── */
        @media (max-width: 500px) {
            .card { padding: 36px 22px 28px; border-radius: var(--radius-lg); }
            .requirements { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="card">

    <!-- Logo -->
    <div class="logo">
        <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="Kyambogo University Logo">
    </div>

    <!-- Step badge -->
    <div class="step-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Identity Verified
    </div>

    <!-- Icon bubble -->
    <div class="icon-bubble">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    </div>

    <h1 class="card-title">Create New Password</h1>
    <p class="card-subtitle">Your identity has been verified. Choose a strong new password for your account.</p>

    <!-- Error display (populated by JS from URL params) -->
    <div id="alertBox" style="display:none;" class="alert error" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span id="alertText"></span>
    </div>

    <form id="resetForm" action="reset_password.php" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <!-- New Password -->
        <div class="form-group">
            <label for="new_password">New Password</label>
            <div class="input-wrapper">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required minlength="8" autocomplete="new-password">
                <button type="button" class="toggle-pw" onclick="togglePw('new_password', this)" aria-label="Show/hide password">
                    <svg id="eye_new" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <!-- Strength bar -->
            <div class="strength-wrap">
                <div class="strength-bar-track"><div class="strength-bar" id="strengthBar"></div></div>
                <div class="strength-label" id="strengthLabel">Enter a password</div>
            </div>
            <!-- Requirements -->
            <div class="requirements" id="requirements">
                <div class="req-item" id="req-length">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    At least 8 characters
                </div>
                <div class="req-item" id="req-upper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Uppercase letter
                </div>
                <div class="req-item" id="req-lower">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Lowercase letter
                </div>
                <div class="req-item" id="req-number">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Number
                </div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <div class="input-wrapper">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required minlength="8" autocomplete="new-password">
                <button type="button" class="toggle-pw" onclick="togglePw('confirm_password', this)" aria-label="Show/hide confirm password">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <div class="match-indicator" id="matchIndicator"></div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn" disabled>
            <span class="spinner" id="spinner"></span>
            <svg id="btnIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span id="btnText">Save New Password</span>
        </button>
    </form>

    <div class="divider">or</div>
    <a href="login.php" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Cancel & return to Login
    </a>
</div>

<script src="../assets/js/theme.js" defer></script>
<script>
    // ── Show/hide password toggle ─────────────────────────────────────────────
    function togglePw(inputId, btn) {
        const input = document.getElementById(inputId);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.querySelector('svg').innerHTML = isPassword
            ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
            : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }

    // ── Password strength engine ──────────────────────────────────────────────
    const newPwInput  = document.getElementById('new_password');
    const confirmInput = document.getElementById('confirm_password');
    const bar         = document.getElementById('strengthBar');
    const label       = document.getElementById('strengthLabel');
    const matchEl     = document.getElementById('matchIndicator');
    const submitBtn   = document.getElementById('submitBtn');

    const reqs = {
        length : { el: document.getElementById('req-length'), test: p => p.length >= 8 },
        upper  : { el: document.getElementById('req-upper'),  test: p => /[A-Z]/.test(p) },
        lower  : { el: document.getElementById('req-lower'),  test: p => /[a-z]/.test(p) },
        number : { el: document.getElementById('req-number'), test: p => /[0-9]/.test(p) },
    };

    const levels = [
        { label: 'Too weak',   color: '#dc2626', width: '15%' },
        { label: 'Weak',       color: '#f97316', width: '30%' },
        { label: 'Fair',       color: '#f59e0b', width: '55%' },
        { label: 'Good',       color: '#22c55e', width: '75%' },
        { label: 'Strong 💪',  color: '#059669', width: '100%' },
    ];

    function checkStrength(pw) {
        let score = 0;
        for (const key in reqs) {
            const met = reqs[key].test(pw);
            reqs[key].el.classList.toggle('met', met);
            if (met) score++;
        }
        // Bonus for special chars
        if (/[^A-Za-z0-9]/.test(pw) && pw.length > 0) score = Math.min(score + 0.5, 4);
        const idx = Math.min(Math.floor(score), 4);
        if (pw.length === 0) {
            bar.style.width = '0';
            label.textContent = 'Enter a password';
            label.style.color = '';
        } else {
            bar.style.width = levels[idx].width;
            bar.style.background = levels[idx].color;
            label.textContent = levels[idx].label;
            label.style.color = levels[idx].color;
        }
        return score >= 4; // "Good" or above
    }

    function checkMatch() {
        const match = confirmInput.value !== '' && confirmInput.value === newPwInput.value;
        const noMatch = confirmInput.value !== '' && confirmInput.value !== newPwInput.value;
        matchEl.className = 'match-indicator' + (match ? ' ok' : noMatch ? ' no' : '');
        matchEl.textContent = match ? '✓ Passwords match' : noMatch ? '✗ Passwords do not match' : '';
        confirmInput.classList.toggle('valid', match);
        confirmInput.classList.toggle('invalid', noMatch);
        return match;
    }

    function updateSubmit() {
        const strong = checkStrength(newPwInput.value);
        const matches = checkMatch();
        submitBtn.disabled = !(strong && matches && newPwInput.value.length >= 8);
    }

    newPwInput.addEventListener('input', updateSubmit);
    confirmInput.addEventListener('input', updateSubmit);

    // ── Loading state on submit ───────────────────────────────────────────────
    document.getElementById('resetForm').addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        document.getElementById('spinner').style.display = 'block';
        document.getElementById('btnIcon').style.display = 'none';
        document.getElementById('btnText').textContent = 'Saving…';
    });

    // ── Show URL error param ──────────────────────────────────────────────────
    const params = new URLSearchParams(window.location.search);
    const err = params.get('error');
    if (err) {
        const box = document.getElementById('alertBox');
        document.getElementById('alertText').textContent = err;
        box.style.display = 'flex';
    }
</script>
</body>
</html>
<?php
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

verify_csrf_or_die();

$new_password     = $_POST['new_password']     ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($new_password) || empty($confirm_password)) {
    header("Location: reset_password.php?error=" . urlencode("All fields are required."));
    exit();
}

if ($new_password !== $confirm_password) {
    header("Location: reset_password.php?error=" . urlencode("Passwords do not match."));
    exit();
}

if (strlen($new_password) < 8) {
    header("Location: reset_password.php?error=" . urlencode("Password must be at least 8 characters."));
    exit();
}

// Hash and update
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE students SET password_hash = ? WHERE student_id = ?");
$stmt->bind_param("ss", $hashed_password, $student_id);
if (!$stmt->execute()) {
    header("Location: reset_password.php?error=" . urlencode("Failed to update password. Please try again."));
    exit();
}

// Clear session and redirect
unset($_SESSION['verified_student'], $_SESSION['otp_student'], $_SESSION['otp_sent']);
session_destroy();

header("Location: login.php?success=" . urlencode("Password updated successfully! You can now log in."));
exit();
?>
