const CACHE_NAME = 'eut-v3';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/favicon.ico',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/DeliveryPanda.webp',
    '/images/DeliveryPanda1.webp',
    '/images/hero-burger.webp',
    '/images/hero-bg.webp',
];

// Install — cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(() => {});
        })
    );
    self.skipWaiting();
});

// Activate — clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// Fetch — network first for API/HTML, cache first for images/assets
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Skip non-GET and cross-origin
    if (event.request.method !== 'GET') return;
    if (url.origin !== location.origin) return;

    // API routes — always network, never cache
    const apiPaths = ['/orders', '/admin', '/chef', '/rider', '/auth', '/broadcasting'];
    if (apiPaths.some(p => url.pathname.startsWith(p))) {
        return;
    }

    // Images & build assets — cache first
    if (url.pathname.startsWith('/images/') || url.pathname.startsWith('/build/')) {
        event.respondWith(
            caches.match(event.request).then(cached => {
                return cached || fetch(event.request).then(response => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // HTML pages — network first, fall back to cache
    event.respondWith(
        fetch(event.request)
            .then(response => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});

// Push notifications
self.addEventListener('push', event => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'E.U.T Snack House', {
            body: data.body || 'You have a new update.',
            icon: '/images/icons/icon-192x192.png',
            badge: '/images/icons/icon-72x72.png',
            data: { url: data.url || '/' },
            vibrate: [200, 100, 200],
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data?.url || '/')
    );
});
