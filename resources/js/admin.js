import { createIcons, icons } from 'lucide';

// Make lucide available globally for inline calls
window.lucide = { createIcons: () => createIcons({ icons }) };

// Run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => createIcons({ icons }));
} else {
    createIcons({ icons });
}

// Re-run after full load (catches dynamically injected icons)
window.addEventListener('load', () => createIcons({ icons }));

// Observer to catch icons added dynamically (modals, ajax content)
const observer = new MutationObserver(() => createIcons({ icons }));
observer.observe(document.body, { childList: true, subtree: true });
