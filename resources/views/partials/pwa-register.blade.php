<script>
// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registered:', reg.scope))
            .catch(err => console.warn('SW failed:', err));
    });
}

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
