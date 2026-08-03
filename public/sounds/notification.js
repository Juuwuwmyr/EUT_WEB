/**
 * Notification Sound Manager
 * Plays bell/notification sound on new orders
 */
const NotificationSound = (() => {
    // Create audio context for playing sound
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    let audioContext = null;
    let lastNotificationTime = 0;
    const NOTIFICATION_THROTTLE = 500; // Min time between notifications (ms)
    
    // Initialize audio context on first user interaction
    function initAudioContext() {
        if (!audioContext && AudioContext) {
            audioContext = new AudioContext();
        }
    }
    
    // Play beep sound using Web Audio API
    function playBeep() {
        try {
            initAudioContext();
            if (!audioContext) return false;
            
            // Throttle notifications
            const now = Date.now();
            if (now - lastNotificationTime < NOTIFICATION_THROTTLE) {
                return false;
            }
            lastNotificationTime = now;
            
            const ctx = audioContext;
            const now_time = ctx.currentTime;
            
            // Create oscillator for bell sound
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            // Bell-like frequency (around 1000-1200 Hz)
            osc.frequency.setValueAtTime(1200, now_time);
            osc.frequency.exponentialRampToValueAtTime(800, now_time + 0.1);
            
            // Volume envelope
            gain.gain.setValueAtTime(0.3, now_time);
            gain.gain.exponentialRampToValueAtTime(0.01, now_time + 0.5);
            
            osc.start(now_time);
            osc.stop(now_time + 0.5);
            
            return true;
        } catch (e) {
            console.error('Beep error:', e);
            return false;
        }
    }
    
    // Play notification sound
    function play() {
        try {
            // Try Web Audio API first (more reliable)
            if (playBeep()) {
                return true;
            }
            
            // Fallback to audio file
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => {
                console.log('Audio play failed:', e.message);
                // If both fail, try one more beep
                playBeep();
            });
            return true;
        } catch (e) {
            console.error('Notification sound error:', e);
            return false;
        }
    }
    
    // Enable sound on user interaction
    function enableOnUserInteraction() {
        const trigger = () => {
            initAudioContext();
            document.removeEventListener('click', trigger);
            document.removeEventListener('touchstart', trigger);
        };
        document.addEventListener('click', trigger, { once: true });
        document.addEventListener('touchstart', trigger, { once: true });
    }
    
    return {
        play: play,
        init: enableOnUserInteraction,
    };
})();

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', NotificationSound.init);
} else {
    NotificationSound.init();
}
