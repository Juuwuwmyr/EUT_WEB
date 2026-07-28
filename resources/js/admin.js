import { createIcons, icons } from 'lucide';

// Make lucide available globally for inline calls
window.lucide = { createIcons: () => createIcons({ icons }) };

// Run once on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => createIcons({ icons }));
} else {
    createIcons({ icons });
}

// Re-run after full load to catch any late-rendered icons
window.addEventListener('load', () => createIcons({ icons }));
