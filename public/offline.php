<?php
/**
 * ============================================================
 * Offline Fallback Page — Kyambogo University Voting System
 * ============================================================
 * Displayed by the service worker when the user is offline.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Offline — KU Voting System</title>
    <link rel="manifest" href="/finalyearproject/public/manifest.json">
    <meta name="theme-color" content="#1a237e">
    <link rel="icon" href="/finalyearproject/assets/images/icons/icon-32.png" type="image/png">
    <link rel="apple-touch-icon" href="/finalyearproject/assets/images/icons/icon-180.png">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --navy:    #1a237e;
            --navy-light: #283593;
            --gold:    #ffc107;
            --white:   #ffffff;
            --gray:    #90a4ae;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #1a237e 0%, #0d1b5e 50%, #050d3a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        /* Animated background blobs */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
        }
        body::before {
            width: 400px; height: 400px;
            background: var(--gold);
            top: -100px; right: -100px;
        }
        body::after {
            width: 300px; height: 300px;
            background: #5c6bc0;
            bottom: -80px; left: -80px;
            animation-delay: -4s;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(20px, -20px) scale(1.05); }
        }

        .card {
            position: relative;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 24px 64px rgba(0,0,0,0.4);
            animation: slide-up 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .icon-wrapper {
            width: 90px; height: 90px;
            margin: 0 auto 24px;
            background: rgba(255,193,7,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid rgba(255,193,7,0.3);
        }
        .icon-wrapper svg {
            width: 46px; height: 46px;
        }

        .app-logo {
            width: 56px; height: 56px;
            border-radius: 14px;
            margin: 0 auto 20px;
            display: block;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            color: rgba(255,255,255,0.65);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .tip-list {
            list-style: none;
            text-align: left;
            margin-bottom: 32px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 16px 20px;
        }
        .tip-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: rgba(255,255,255,0.75);
            font-size: 0.88rem;
            padding: 5px 0;
        }
        .tip-list li span.dot {
            color: var(--gold);
            font-size: 1rem;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .retry-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #ffc107, #ffb300);
            color: #1a237e;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(255,193,7,0.35);
        }
        .retry-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(255,193,7,0.5);
        }
        .retry-btn:active { transform: translateY(0); }

        .footer-text {
            margin-top: 24px;
            font-size: 0.78rem;
            color: rgba(255,255,255,0.35);
        }

        /* Wifi pulse animation */
        .pulse-ring {
            display: inline-block;
            width: 12px; height: 12px;
            border-radius: 50%;
            background: #ef5350;
            box-shadow: 0 0 0 0 rgba(239,83,80,0.4);
            animation: pulse 2s infinite;
            vertical-align: middle;
            margin-right: 6px;
        }
        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(239,83,80,0.5); }
            70%  { box-shadow: 0 0 0 10px rgba(239,83,80,0); }
            100% { box-shadow: 0 0 0 0 rgba(239,83,80,0); }
        }
    </style>
</head>
<body>
    <div class="card">
        <img class="app-logo"
             src="/finalyearproject/assets/images/icons/icon-96.png"
             alt="KU Voting System">

        <div class="icon-wrapper">
            <!-- Wifi Off Icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="1" y1="1" x2="23" y2="23"/>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
                <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
                <path d="M10.71 5.05A16 16 0 0 1 22.56 9"/>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                <circle cx="12" cy="20" r="1" fill="#ffc107"/>
            </svg>
        </div>

        <h1>You're Offline</h1>
        <p class="subtitle">
            <span class="pulse-ring"></span>No internet connection detected.<br>
            The voting system requires internet access to work.
        </p>

        <ul class="tip-list">
            <li><span class="dot">●</span> Check your Wi-Fi or mobile data connection</li>
            <li><span class="dot">●</span> Move closer to your router or hotspot</li>
            <li><span class="dot">●</span> Try turning airplane mode on and off</li>
            <li><span class="dot">●</span> Then tap <strong>Try Again</strong> below</li>
        </ul>

        <button class="retry-btn" onclick="window.location.reload()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            Try Again
        </button>

        <p class="footer-text">
            Kyambogo University Online Voting System &mdash; &copy; <?php echo date('Y'); ?>
        </p>
    </div>

    <script>
        // Automatically redirect to home when connectivity is restored
        window.addEventListener('online', () => {
            const btn = document.querySelector('.retry-btn');
            btn.textContent = '✓ Connected! Redirecting...';
            setTimeout(() => window.location.href = '/finalyearproject/public/', 1000);
        });
    </script>
</body>
</html>
