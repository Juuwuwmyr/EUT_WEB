<script>
// Register Service Worker + Web Push subscription
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => {
                console.log('SW registered:', reg.scope);
                @auth
                // Subscribe to push notifications for logged-in users
                if ('PushManager' in window) {
                    reg.pushManager.getSubscription().then(existingSub => {
                        if (!existingSub) {
                            // Ask for permission only once — don't nag
                            if (Notification.permission === 'default') {
                                Notification.requestPermission().then(perm => {
                                    if (perm === 'granted') subscribeUserToPush(reg);
                                });
                            } else if (Notification.permission === 'granted') {
                                subscribeUserToPush(reg);
                            }
                        }
                    });
                }
                @endauth
            })
            .catch(err => console.warn('SW failed:', err));
    });
}

@auth
async function subscribeUserToPush(reg) {
    try {
        // Fetch VAPID public key
        const res = await fetch('/push/vapid-key');
        const { vapid_public_key } = await res.json();
        if (!vapid_public_key) return; // VAPID not configured yet

        const applicationServerKey = urlBase64ToUint8Array(vapid_public_key);
        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey,
        });

        // Send subscription to server
        await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(sub.toJSON()),
        });
        console.log('[Push] Subscribed to notifications');
    } catch (e) {
        console.warn('[Push] Subscription failed:', e);
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    const arr = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
    return arr;
}
@endauth

// PWA Install prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredPrompt = e;

    // Show install banner after 3 seconds if not already installed
    // BUT suppress it when the user arrived via a table QR code (URL has ?table=...)
    setTimeout(() => {
        const banner = document.getElementById('pwaInstallBanner');
        const hasTable = new URLSearchParams(window.location.search).get('table');
        if (banner && !localStorage.getItem('pwaInstallDismissed') && !hasTable) {
            banner.style.display = 'flex';
        }
    }, 3000);
});

function installPWA() {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(result => {
        deferredPrompt = null;
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) banner.style.display = 'none';
    });
}

function dismissPWABanner() {
    localStorage.setItem('pwaInstallDismissed', '1');
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) banner.style.display = 'none';
}
</script>
