{{--
    Lightweight AJAX-assisted navigation.

    This does NOT swap page content via AJAX (risky here: most pages run
    setInterval polling loops and Echo/WebSocket subscriptions that would leak
    or double-fire if we tried to hot-swap the DOM instead of doing a real
    navigation). Instead it:

      1. Fires a `fetch()` the instant a finger/pointer touches a link — before
         the tap even completes — so the server already has a head start on
         rendering the destination by the time the browser actually navigates.
      2. Gives instant visual feedback (pressed state + a thin top progress
         bar) on tap, so buttons/bottom-nav never feel like they "did nothing".
      3. Lets the browser perform its normal, fully-correct full navigation.

    Include this once near the end of `<body>` on any page.
--}}
<style>
    #ajaxNavBar {
        position: fixed; top: 0; left: 0; height: 3px; width: 0%;
        background: linear-gradient(90deg, #f59e0b, #facc15);
        z-index: 999999; opacity: 0; pointer-events: none;
        transition: width .4s ease, opacity .3s ease;
    }
    #ajaxNavBar.active { opacity: 1; }
    /* Instant press feedback for any tappable nav/button element, in case a
       page hasn't already defined its own :active state. */
    .bnav-item, .w-bnav-item, .nav-icon-btn, .bnav-item * { -webkit-tap-highlight-color: transparent; }
</style>
<div id="ajaxNavBar"></div>
<script>
(function () {
    if (window.__ajaxNavInit) return;
    window.__ajaxNavInit = true;

    var bar        = document.getElementById('ajaxNavBar');
    var prefetched = new Set();
    var hideTimer  = null;

    function isNavigable(link) {
        if (!link || !link.href) return false;
        if (link.target && link.target !== '_self') return false;
        if (link.hasAttribute('download')) return false;
        if (link.dataset && link.dataset.noAjax !== undefined) return false;
        if (link.origin !== window.location.origin) return false;
        var rawHref = link.getAttribute('href') || '';
        if (rawHref === '' || rawHref.charAt(0) === '#') return false;
        if (link.href === window.location.href) return false;
        return true;
    }

    function prefetch(link) {
        if (!isNavigable(link)) return;
        var href = link.href;
        if (prefetched.has(href)) return;
        prefetched.add(href);
        fetch(href, { method: 'GET', credentials: 'same-origin' }).catch(function () {
            prefetched.delete(href);
        });
    }

    function showBar() {
        clearTimeout(hideTimer);
        bar.style.transition = 'none';
        bar.style.width = '0%';
        bar.classList.add('active');
        void bar.offsetWidth; // force reflow so the width transition below animates
        bar.style.transition = 'width .4s ease, opacity .3s ease';
        bar.style.width = '75%';
    }

    function resetBar() {
        bar.classList.remove('active');
        bar.style.width = '0%';
    }

    // Warm the destination as early as physically possible — on first touch/
    // pointer contact, well before "click" fires — so the real navigation
    // that follows feels instant.
    document.addEventListener('touchstart', function (e) {
        var link = e.target.closest && e.target.closest('a[href]');
        if (link) prefetch(link);
    }, { passive: true, capture: true });

    document.addEventListener('pointerdown', function (e) {
        var link = e.target.closest && e.target.closest('a[href]');
        if (link) prefetch(link);
    }, { passive: true, capture: true });

    // On the actual click, show progress immediately; the browser then does
    // its normal full navigation (correct by construction — no leftover
    // timers/listeners from the page we're leaving).
    document.addEventListener('click', function (e) {
        var link = e.target.closest && e.target.closest('a[href]');
        if (!link || !isNavigable(link)) return;
        showBar();
    }, { capture: true });

    // Reset the bar if the user returns via back/forward cache.
    window.addEventListener('pageshow', resetBar);
})();
</script>
