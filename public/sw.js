/**
 * ============================================================
 * Kyambogo University Voting System — Service Worker
 * ============================================================
 * Provides offline support and asset caching.
 */

const CACHE_NAME = 'ku-voting-v1';
const OFFLINE_URL = '/finalyearproject/public/offline.php';

/**
 * Static assets to pre-cache on install.
 * These are served from cache immediately on subsequent visits.
 */
const PRECACHE_URLS = [
  '/finalyearproject/public/',
  '/finalyearproject/public/index.php',
  '/finalyearproject/public/login.php',
  '/finalyearproject/public/offline.php',
  '/finalyearproject/assets/images/icons/icon-192.png',
  '/finalyearproject/assets/images/icons/icon-512.png',
];

// ── INSTALL ──────────────────────────────────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_URLS);
    }).then(() => self.skipWaiting())
  );
});

// ── ACTIVATE ─────────────────────────────────────────────────
// Remove old caches from previous versions
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    }).then(() => self.clients.claim())
  );
});

// ── FETCH ─────────────────────────────────────────────────────
/**
 * Strategy:
 *  - Navigation requests (HTML pages): Network-first → offline fallback
 *  - Static assets (CSS, JS, images, fonts): Cache-first → network fallback
 *  - API / POST requests: Network-only (never cache)
 */
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests (POST for votes, forms, etc.)
  if (request.method !== 'GET') return;

  // Skip cross-origin requests
  if (url.origin !== location.origin) return;

  // Skip API endpoints — always network only
  if (url.pathname.includes('/api/') || url.pathname.includes('processvote')) return;

  // Navigation requests: Network-first with offline fallback
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .catch(() => caches.match(OFFLINE_URL))
    );
    return;
  }

  // Static assets: Cache-first with network fallback
  if (
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'image' ||
    request.destination === 'font'
  ) {
    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached;
        return fetch(request).then((response) => {
          // Cache valid responses for future use
          if (response && response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
          }
          return response;
        });
      })
    );
    return;
  }
});
