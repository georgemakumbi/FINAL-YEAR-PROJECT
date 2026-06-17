<?php
if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

$error   = $_GET['error']   ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Kyambogo University Voting</title>
    <meta name="description" content="Reset your password for Kyambogo University Online Voting System">
    <link rel="icon" href="<?php echo get_system_logo($conn, '../'); ?>" type="image/png">
    <style><?php include ASSETS_CSS . '/theme.css'; ?></style>
    <style>
        /* ── Layout ─────────────────────────────────────────────────────────── */
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
            background: linear-gradient(90deg, var(--brand-primary, #003366), var(--brand-accent, #ffc107));
        }

        /* ── Logo ───────────────────────────────────────────────────────────── */
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            height: 72px;
            width: auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1));
        }

        /* ── Icon bubble ────────────────────────────────────────────────────── */
        .icon-bubble {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--brand-primary-light, #e8f0fe);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: background 0.3s;
        }
        .icon-bubble svg {
            width: 34px;
            height: 34px;
            color: var(--brand-primary, #003366);
        }
        body.dark .icon-bubble {
            background: rgba(37, 99, 235, 0.15);
        }

        /* ── Headings ───────────────────────────────────────────────────────── */
        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            text-align: center;
            margin: 0 0 6px;
            letter-spacing: -0.4px;
        }
        .card-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
            margin: 0 0 28px;
            line-height: 1.5;
        }

        /* ── Alerts ─────────────────────────────────────────────────────────── */
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
        .alert.success {
            background: var(--brand-success-light, #d1fae5);
            color: var(--brand-success, #059669);
            border: 1px solid rgba(5, 150, 105, 0.2);
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Form ───────────────────────────────────────────────────────────── */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }

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
            padding: 13px 14px 13px 42px;
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
        .input-wrapper input:focus + svg.input-icon,
        .input-wrapper:focus-within svg.input-icon {
            color: var(--brand-primary, #003366);
        }

        /* ── Submit Button ──────────────────────────────────────────────────── */
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
            margin-top: 4px;
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
        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
        }
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Loading spinner */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Back to login link ─────────────────────────────────────────────── */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 22px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--brand-primary); text-decoration: none; }
        .back-link svg { width: 15px; height: 15px; }

        /* ── Info box ───────────────────────────────────────────────────────── */
        .info-box {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 0.83rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        .info-box strong { color: var(--text); }

        /* ── Responsive ─────────────────────────────────────────────────────── */
        @media (max-width: 500px) {
            .card {
                padding: 36px 22px 28px;
                border-radius: var(--radius-lg);
            }
        }
    </style>
</head>
<body>
<div class="card">

    <!-- Logo -->
    <div class="logo">
        <img src="<?php echo get_system_logo($conn, '../'); ?>" alt="Kyambogo University Logo">
    </div>

    <!-- Icon bubble -->
    <div class="icon-bubble">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
        </svg>
    </div>

    <h1 class="card-title">Forgot Password?</h1>
    <p class="card-subtitle">Enter your university email address and we'll send you an OTP code to reset your password.</p>

    <!-- Alerts -->
    <?php if ($error): ?>
    <div class="alert error" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert success" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <!-- Info hint -->
    <div class="info-box">
        <strong>Note:</strong> Enter the email address associated with your student account. You'll receive a 6-digit OTP that expires in <strong>5 minutes</strong>.
    </div>

    <!-- Form -->
    <form id="forgotForm" action="submit_forgot_password.php" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div class="form-group">
            <label for="email">University Email Address</label>
            <div class="input-wrapper">
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="e.g. student@std.kyu.ac.ug"
                    required
                    autocomplete="email"
                    autofocus
                >
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            <span class="spinner" id="spinner"></span>
            <span id="btnText">Send Reset OTP</span>
            <svg id="btnIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
        </button>
    </form>

    <a href="login.php" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Back to Login
    </a>
</div>

<script src="../assets/js/theme.js" defer></script>
<script>
    const form = document.getElementById('forgotForm');
    const btn  = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');

    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        if (!email) { e.preventDefault(); return; }

        // Loading state
        btn.disabled = true;
        spinner.style.display = 'block';
        btnText.textContent = 'Sending…';
        btnIcon.style.display = 'none';
    });
</script>
</body>
</html>
