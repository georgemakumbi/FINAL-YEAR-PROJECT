/**
 * ============================================================
 * Kyambogo University Voting System — PWA Registration
 * ============================================================
 * - Registers the service worker
 * - Handles install prompt (Add to Home Screen banner)
 */

(function () {
  'use strict';

  // ── Service Worker Registration ───────────────────────────
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker
        .register('/finalyearproject/public/sw.js', { scope: '/finalyearproject/' })
        .then((registration) => {
          console.log('[PWA] Service Worker registered. Scope:', registration.scope);

          // Check for updates periodically
          registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            newWorker.addEventListener('statechange', () => {
              if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                showUpdateBanner();
              }
            });
          });
        })
        .catch((error) => {
          console.warn('[PWA] Service Worker registration failed:', error);
        });
    });
  }

  // ── Install Prompt (Add to Home Screen) ──────────────────
  let deferredPrompt = null;

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Only show banner if user hasn't dismissed or installed
    const dismissed = localStorage.getItem('ku-pwa-install-dismissed');
    if (!dismissed) {
      showInstallBanner();
    }
  });

  window.addEventListener('appinstalled', () => {
    hideInstallBanner();
    deferredPrompt = null;
    localStorage.setItem('ku-pwa-install-dismissed', 'installed');
    console.log('[PWA] App installed successfully');
  });

  // ── Install Banner UI ────────────────────────────────────
  function showInstallBanner() {
    if (document.getElementById('pwa-install-banner')) return;

    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.innerHTML = `
      <div style="
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
        color: #fff;
        padding: 14px 20px;
        border-radius: 14px;
        box-shadow: 0 8px 32px rgba(26,35,126,0.45);
        display: flex;
        align-items: center;
        gap: 14px;
        z-index: 99999;
        max-width: 92vw;
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 14px;
        animation: pwa-slide-up 0.4s ease;
      ">
        <img src="/finalyearproject/assets/images/icons/icon-72.png"
             alt="KU Voting" 
             style="width:40px;height:40px;border-radius:10px;flex-shrink:0;">
        <div style="flex:1">
          <div style="font-weight:700;font-size:15px">Install KU Votes App</div>
          <div style="opacity:0.85;font-size:12px;margin-top:2px">Add to your home screen for quick access</div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
          <button id="pwa-install-btn" style="
            background:#ffc107;color:#1a237e;border:none;padding:8px 16px;
            border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;
            white-space:nowrap;
          ">Install</button>
          <button id="pwa-dismiss-btn" style="
            background:rgba(255,255,255,0.15);color:#fff;border:none;
            padding:8px 10px;border-radius:8px;cursor:pointer;font-size:18px;
            line-height:1;
          ">✕</button>
        </div>
      </div>
      <style>
        @keyframes pwa-slide-up {
          from { opacity:0; transform: translateX(-50%) translateY(20px); }
          to   { opacity:1; transform: translateX(-50%) translateY(0); }
        }
      </style>
    `;
    document.body.appendChild(banner);

    document.getElementById('pwa-install-btn').addEventListener('click', () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            console.log('[PWA] User accepted install');
          } else {
            localStorage.setItem('ku-pwa-install-dismissed', 'dismissed');
          }
          deferredPrompt = null;
          hideInstallBanner();
        });
      }
    });

    document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
      localStorage.setItem('ku-pwa-install-dismissed', 'dismissed');
      hideInstallBanner();
    });
  }

  function hideInstallBanner() {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.remove();
  }

  // ── Update Banner UI ─────────────────────────────────────
  function showUpdateBanner() {
    const banner = document.createElement('div');
    banner.innerHTML = `
      <div style="
        position: fixed;
        top: 16px;
        left: 50%;
        transform: translateX(-50%);
        background: #283593;
        color: #fff;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 99999;
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 14px;
      ">
        <span>🔄 App update available!</span>
        <button onclick="location.reload()" style="
          background:#ffc107;color:#1a237e;border:none;padding:6px 14px;
          border-radius:6px;font-weight:700;cursor:pointer;
        ">Refresh</button>
      </div>
    `;
    document.body.appendChild(banner);
  }

})();
