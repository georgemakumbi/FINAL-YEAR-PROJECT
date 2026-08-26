<?php
require_once '../bootstrap.php';
ensure_csrf_token();

// Must have a pending reset session to access this page
if (empty($_SESSION['admin_reset_id'])) {
    header('Location: admin_forgot_password.php?error=' . urlencode(
        'Please request a reset code first.'
    ));
    exit();
}

$error = htmlspecialchars($_GET['error'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Reset Password | Kyambogo Voting</title>
    <link rel="icon" href="<?= get_system_logo($conn, '../') ?>">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: var(--app-bg, #f0f4ff);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            padding: 24px 16px;
        }
        .card {
            width: 100%; max-width: 440px;
            background: var(--surface, #fff);
            border-radius: 20px;
            padding: 40px 36px 32px;
            box-shadow: 0 16px 48px rgba(0,51,102,0.12);
            position: relative; overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 5px;
            background: linear-gradient(90deg, #003366, #ffc107);
        }
        .icon { text-align: center; font-size: 2.8rem; margin-bottom: 10px; }
        h1 { text-align: center; font-size: 1.4rem; font-weight: 800;
             color: var(--text, #1a1d23); margin-bottom: 6px; }
        .subtitle { text-align: center; font-size: 0.85rem; color: var(--text-muted, #6b7280);
                    margin-bottom: 28px; line-height: 1.6; }
        .alert {
            padding: 12px 14px; border-radius: 10px; font-size: 0.88rem; font-weight: 500;
            margin-bottom: 20px; display: flex; gap: 8px; align-items: flex-start;
        }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.85rem; font-weight: 700;
                color: var(--text, #374151); margin-bottom: 7px; }
        /* OTP cells */
        .otp-row { display: flex; gap: 8px; justify-content: center; margin-bottom: 4px; }
        .otp-digit {
            width: 48px; height: 56px; text-align: center;
            font-size: 1.4rem; font-weight: 800; border-radius: 10px;
            border: 2px solid var(--input-border, #d1d5db);
            background: var(--input-bg, #fafafa);
            color: var(--text, #111); font-family: monospace;
            transition: border-color 0.2s, box-shadow 0.15s, transform 0.1s;
            outline: none;
        }
        .otp-digit:focus {
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0,51,102,0.12);
            transform: scale(1.06);
        }
        .otp-digit.filled { border-color: #22c55e; background: #f0fdf4; }
        input[type="hidden"]#otp_combined { display: none; }

        /* Password inputs */
        .pw-wrap { position: relative; }
        .pw-wrap input {
            width: 100%; padding: 13px 46px 13px 15px; border-radius: 10px;
            border: 1.5px solid var(--input-border, #d1d5db);
            background: var(--input-bg, #fafafa);
            color: var(--text, #111827); font-size: 0.95rem; font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s; outline: none;
        }
        .pw-wrap input:focus {
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0,51,102,0.10);
            background: var(--surface, #fff);
        }
        .pw-toggle {
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; font-size: 1.1rem;
            color: var(--text-muted, #9ca3af); padding: 4px;
        }
        /* Strength bar */
        .strength-bar { height: 4px; border-radius: 2px; margin-top: 7px;
                        background: var(--border, #e5e7eb); overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 2px;
                         transition: width 0.3s, background 0.3s; width: 0; }
        .strength-label { font-size: 0.75rem; font-weight: 600; margin-top: 4px; }
        .match-hint { font-size: 0.78rem; margin-top: 5px; }

        .btn {
            width: 100%; padding: 13px; border: none; border-radius: 10px;
            background: linear-gradient(135deg, #003366, #004d99);
            color: #fff; font-size: 0.95rem; font-weight: 800;
            font-family: inherit; cursor: pointer; margin-top: 8px;
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn:not(:disabled):hover {
            transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,51,102,0.25);
        }
        .divider { border: none; border-top: 1px solid var(--border, #e5e7eb);
                   margin: 24px 0; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 0.84rem;
                       color: var(--text-muted, #6b7280); }
        .footer-link a { color: #003366; font-weight: 700; text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }
        .section-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
                         letter-spacing: 0.05em; color: var(--text-muted, #9ca3af);
                         margin-bottom: 14px; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🔐</div>
    <h1>Reset Password</h1>
    <p class="subtitle">
        Enter the 6-digit code sent to your registered email, then choose a new password.
    </p>

    <?php if ($error): ?>
    <div class="alert alert-error">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form action="submit_admin_reset_password.php" method="POST" id="resetForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="otp" id="otp_combined">

        <!-- OTP -->
        <div class="section-label">Step 1 — Enter the code from your email</div>
        <div class="otp-row" aria-label="6-digit reset code">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <input type="text" class="otp-digit" id="otp_<?= $i ?>"
                   maxlength="1" inputmode="numeric" pattern="[0-9]"
                   autocomplete="<?= $i === 1 ? 'one-time-code' : 'off' ?>"
                   aria-label="Digit <?= $i ?>">
            <?php endfor; ?>
        </div>
        <div style="font-size:0.78rem;color:var(--text-muted,#6b7280);text-align:center;margin-bottom:20px;">
            Code expires in 10 minutes. Check your spam folder too.
        </div>

        <hr class="divider">

        <!-- New password -->
        <div class="section-label">Step 2 — Choose a new password</div>

        <div class="form-group">
            <label for="new_password">New Password</label>
            <div class="pw-wrap">
                <input type="password" id="new_password" name="new_password"
                       placeholder="At least 8 characters" required minlength="8"
                       autocomplete="new-password">
                <button type="button" class="pw-toggle" onclick="togglePw('new_password',this)">👁️</button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
            <div class="strength-label" id="strength-label"></div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="pw-wrap">
                <input type="password" id="confirm_password" name="confirm_password"
                       placeholder="Re-enter your password" required minlength="8"
                       autocomplete="new-password">
                <button type="button" class="pw-toggle" onclick="togglePw('confirm_password',this)">👁️</button>
            </div>
            <div class="match-hint" id="match-hint"></div>
        </div>

        <button type="submit" class="btn" id="submitBtn" disabled>
            🔐 Reset My Password
        </button>
    </form>

    <div class="footer-link">
        <a href="admin_forgot_password.php">← Request a new code</a>
        &nbsp;·&nbsp;
        <a href="admin_login.php">Back to Login</a>
    </div>
</div>

<script src="../assets/js/theme.js" defer></script>
<script>
/* ── OTP digit navigation ── */
(function () {
    const digits   = Array.from(document.querySelectorAll('.otp-digit'));
    const combined = document.getElementById('otp_combined');
    const submitBtn = document.getElementById('submitBtn');

    function sync() {
        const code = digits.map(d => d.value).join('');
        combined.value = code;
        digits.forEach(d => d.classList.toggle('filled', d.value !== ''));
        checkSubmit();
    }

    function checkSubmit() {
        const code = digits.map(d => d.value).join('');
        const pw   = document.getElementById('new_password').value;
        const cpw  = document.getElementById('confirm_password').value;
        submitBtn.disabled = !(code.length === 6 && pw.length >= 8 && pw === cpw);
    }

    digits.forEach((input, idx) => {
        input.addEventListener('input', function () {
            const val = this.value.replace(/\D/g, '');
            if (val.length > 1) {
                val.split('').forEach((ch, i) => { if (digits[idx + i]) digits[idx + i].value = ch; });
                const next = digits[Math.min(idx + val.length, digits.length - 1)];
                next.focus();
            } else {
                this.value = val.slice(0, 1);
                if (val && idx < digits.length - 1) digits[idx + 1].focus();
            }
            sync();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                digits[idx - 1].focus(); digits[idx - 1].value = ''; sync();
            }
            if (e.key === 'ArrowLeft'  && idx > 0)               digits[idx - 1].focus();
            if (e.key === 'ArrowRight' && idx < digits.length - 1) digits[idx + 1].focus();
        });
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const p = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            p.split('').forEach((ch, i) => { if (digits[idx + i]) digits[idx + i].value = ch; });
            digits[Math.min(idx + p.length, digits.length - 1)].focus();
            sync();
        });
    });

    document.getElementById('new_password').addEventListener('input', checkSubmit);
    document.getElementById('confirm_password').addEventListener('input', checkSubmit);
})();

/* ── Password strength ── */
(function () {
    const pw    = document.getElementById('new_password');
    const cpw   = document.getElementById('confirm_password');
    const fill  = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');
    const hint  = document.getElementById('match-hint');

    const levels = [
        {pct:'0%',  bg:'#e5e7eb', text:'',          col:''},
        {pct:'20%', bg:'#ef4444', text:'Weak',       col:'#ef4444'},
        {pct:'40%', bg:'#f97316', text:'Fair',       col:'#f97316'},
        {pct:'60%', bg:'#eab308', text:'Good',       col:'#eab308'},
        {pct:'80%', bg:'#22c55e', text:'Strong',     col:'#22c55e'},
        {pct:'100%',bg:'#16a34a', text:'Excellent',  col:'#16a34a'},
    ];

    function score(v) {
        let s = 0;
        if (v.length >= 8)          s++;
        if (v.length >= 12)         s++;
        if (/[A-Z]/.test(v))        s++;
        if (/[0-9]/.test(v))        s++;
        if (/[^a-zA-Z0-9]/.test(v)) s++;
        return Math.min(s, 5);
    }

    pw.addEventListener('input', function () {
        const lv = levels[score(this.value)];
        fill.style.width      = lv.pct;
        fill.style.background = lv.bg;
        label.textContent     = lv.text;
        label.style.color     = lv.col;
        updateMatch();
    });

    function updateMatch() {
        if (!cpw.value) { hint.textContent = ''; return; }
        if (pw.value === cpw.value) {
            hint.textContent  = '✓ Passwords match';
            hint.style.color  = '#16a34a';
        } else {
            hint.textContent  = '✗ Passwords do not match';
            hint.style.color  = '#dc2626';
        }
    }
    cpw.addEventListener('input', updateMatch);
})();

/* ── Show/hide password ── */
function togglePw(id, btn) {
    const el = document.getElementById(id);
    const hidden = el.type === 'password';
    el.type = hidden ? 'text' : 'password';
    btn.textContent = hidden ? '🙈' : '👁️';
}
</script>
</body>
</html>
