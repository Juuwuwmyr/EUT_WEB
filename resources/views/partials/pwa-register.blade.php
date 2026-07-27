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
    setTimeout(() => {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner && !localStorage.getItem('pwaInstallDismissed')) {
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
