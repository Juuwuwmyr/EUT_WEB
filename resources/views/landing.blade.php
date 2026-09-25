<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E.U.T Snack House &mdash; Food Delivery</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- ── If the app is already installed (running as PWA / standalone), skip this
         download page and go straight to the shop menu. Runs before any render. ── --}}
    <script>
        (function () {
            var isStandalone =
                window.matchMedia('(display-mode: standalone)').matches ||
                window.navigator.standalone === true ||          // iOS Safari
                document.referrer.startsWith('android-app://'); // TWA

            if (isStandalone) {
                window.location.replace('{{ route('shop.home') }}');
            }
        })();
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            background: #fff;
        }

        /* -- LEFT PANEL -- */
        .left-panel {
            width: 50%;
            background: linear-gradient(160deg, #7f1d1d 0%, #dc2626 40%, #b45309 80%, #f59e0b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            min-height: 100vh;
            padding: 40px 24px;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 40%, rgba(255,255,255,0.08) 0%, transparent 60%);
        }
        .phone-img {
            width: 75%;
            max-width: 340px;
            max-height: 85vh;
            object-fit: contain;
            display: block;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 30px 60px rgba(0,0,0,0.4));
        }

        /* -- RIGHT PANEL -- */
        .right-panel {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            background: #fff;
        }

        .panda-img {
            width: 80px;
            margin-bottom: 28px;
        }

        .headline {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 10px;
            text-align: center;
            letter-spacing: -0.02em;
        }

        .subtext {
            font-size: 14px;
            color: #6b7280;
            text-align: center;
            line-height: 1.6;
            margin-bottom: 32px;
            max-width: 320px;
        }

        /* -- BUTTONS -- */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            max-width: 340px;
        }

        .btn-download {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 15px 24px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
            border: none;
            background: #111827;
            color: #fff;
        }
        .btn-download:hover {
            background: #1f2937;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.18);
        }
        .btn-download svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .btn-proceed {
            display: block;
            width: 100%;
            max-width: 340px;
            padding: 15px 24px;
            border-radius: 8px;
            background: #dc2626;
            border: none;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            cursor: pointer;
            transition: all 0.18s;
            text-align: center;
            text-decoration: none;
            margin-top: 12px;
        }
        .btn-proceed:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(220,38,38,0.35);
        }

        /* -- MOBILE -- */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel {
                width: 100%;
                min-height: 55vh;
                align-items: center;
                justify-content: center;
                padding: 32px 24px;
            }
            .phone-img {
                width: 60%;
                max-width: 260px;
                max-height: 50vh;
            }
            .right-panel {
                width: 100%;
                padding: 36px 28px 48px;
            }
            .headline { font-size: 24px; }
        }
    </style>
</head>
<body>

    <!-- LEFT: gradient + phone -->
    <div class="left-panel">
        <img src="{{ asset('images/phone.webp') }}" alt="EUT App" class="phone-img">
    </div>

    <!-- RIGHT: content -->
    <div class="right-panel">

        <img src="{{ asset('images/DeliveryPanda.webp') }}" alt="Delivery Panda" class="panda-img">

        <h1 class="headline">Download Our App</h1>

        <p class="subtext">
            Get the best experience by installing our app.
        </p>

        <div class="btn-group">

            <a href="https://www.mediafire.com/file/6evfuuijuoj6qxh/app-release-signef.apk/file" target="_blank" rel="noopener noreferrer" class="btn-download">
                <!-- Android icon -->
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.523 15.341a5.03 5.03 0 0 1-1.364-.189l-1.345 2.33a.5.5 0 0 1-.866-.5l1.34-2.32a5.01 5.01 0 0 1-2.786-2.789L9.82 13.2a.5.5 0 0 1-.5-.866l2.33-1.346a5.03 5.03 0 0 1-.189-1.365c0-.186.01-.37.03-.55H6v9.434A1.09 1.09 0 0 0 7.087 19.6h9.826A1.09 1.09 0 0 0 18 18.507V9.073h-5.507a5.03 5.03 0 0 1 5.03 6.268ZM8.5 7.5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1Zm7 0a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1ZM7.5 5.006l-.87-1.506a.5.5 0 0 1 .866-.5l.9 1.558A5.978 5.978 0 0 1 12 4c.6 0 1.178.085 1.724.245l.9-1.558a.5.5 0 0 1 .866.5L14.62 4.78A5.985 5.985 0 0 1 18 10H6a5.985 5.985 0 0 1 1.5-4.994Z"/></svg>
                Download on Android
            </a>

            <a href="#" class="btn-download">
                <!-- Apple icon -->
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11Z"/></svg>
                Download on iOS
            </a>

        </div>

        <a href="{{ route('restaurant') }}" class="btn-proceed">
            Proceed Anyway
        </a>

    </div>

@include('partials.pwa-register')

{{-- ══════════ MAINTENANCE MODE (Landing) ══════════ --}}
@php $maintenance = true; @endphp
@if($maintenance)
<style>
#maint-screen{position:fixed;inset:0;z-index:99999;background:#0a0a0a;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.25rem;padding:2rem;text-align:center;font-family:'Inter',sans-serif;}
#maint-screen .m-icon{width:80px;height:48px;border-radius:.75rem;background:#111827;border:1px solid #1f2937;display:flex;align-items:center;justify-content:center;margin-bottom:.25rem;padding:0 .75rem;}
#maint-screen .m-icon svg{display:block;}
#maint-screen .m-brand{font-size:.7rem;color:#374151;letter-spacing:.1em;text-transform:uppercase;font-weight:500;}
#maint-screen h1{margin:0;font-size:1.5rem;font-weight:700;color:#f9fafb;letter-spacing:-.02em;line-height:1.2;}
#maint-screen p{margin:0;font-size:.875rem;color:#6b7280;max-width:320px;line-height:1.7;}
#maint-screen .m-timer{font-family:monospace;font-size:2.75rem;font-weight:800;color:#f59e0b;line-height:1;letter-spacing:.02em;}
#maint-screen .m-note{font-size:.72rem;color:#1f2937;}
</style>
<div id="maint-screen">
    <div class="m-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 304 182" width="54" height="32">
            <path d="M86.4 66.4c0 3.7.4 6.7 1.1 8.9.8 2.2 1.8 4.6 3.2 7.2.5.8.7 1.6.7 2.3 0 1-.6 2-1.9 3l-6.3 4.2c-.9.6-1.8.9-2.6.9-1 0-2-.5-3-1.4-1.4-1.5-2.6-3.1-3.6-4.7-1-1.7-2-3.6-3.1-5.9-7.8 9.2-17.6 13.8-29.4 13.8-8.4 0-15.1-2.4-20-7.2-4.9-4.8-7.4-11.2-7.4-19.2 0-8.5 3-15.4 9.1-20.6 6.1-5.2 14.2-7.8 24.5-7.8 3.4 0 6.9.3 10.6.8 3.7.5 7.5 1.3 11.5 2.2v-7.3c0-7.6-1.6-12.9-4.7-16-3.2-3.1-8.6-4.6-16.3-4.6-3.5 0-7.1.4-10.8 1.3-3.7.9-7.3 2-10.8 3.4-1.6.7-2.8 1.1-3.5 1.3-.7.2-1.2.3-1.6.3-1.4 0-2.1-1-2.1-3.1v-4.9c0-1.6.2-2.8.7-3.5.5-.7 1.4-1.4 2.8-2.1 3.5-1.8 7.7-3.3 12.6-4.5 4.9-1.3 10.1-1.9 15.6-1.9 11.9 0 20.6 2.7 26.2 8.1 5.5 5.4 8.3 13.6 8.3 24.6v32.4zm-40.6 15.2c3.3 0 6.7-.6 10.3-1.8 3.6-1.2 6.8-3.4 9.5-6.4 1.6-1.9 2.8-4 3.4-6.4.6-2.4 1-5.3 1-8.7v-4.2c-2.9-.7-6-1.3-9.2-1.7-3.2-.4-6.3-.6-9.4-.6-6.7 0-11.6 1.3-14.9 4-3.3 2.7-4.9 6.5-4.9 11.5 0 4.7 1.2 8.2 3.7 10.6 2.4 2.5 5.9 3.7 10.5 3.7zm80.3 10.8c-1.8 0-3-.3-3.8-1-.8-.6-1.5-2-2.1-3.9L96.7 10.2c-.6-2-.9-3.3-.9-4 0-1.6.8-2.5 2.4-2.5h9.8c1.9 0 3.2.3 3.9 1 .8.6 1.4 2 2 3.9l19.4 76.5 18-76.5c.5-2 1.1-3.3 1.9-3.9.8-.6 2.2-1 4-1h8c1.9 0 3.2.3 4.1 1 .8.6 1.5 2 1.9 3.9l18.2 77.5 20-77.5c.6-2 1.3-3.3 2-3.9.8-.6 2.1-1 3.9-1h9.3c1.6 0 2.5.8 2.5 2.5 0 .5-.1 1-.2 1.6-.1.6-.3 1.4-.7 2.5l-23.7 77.3c-.6 2-1.3 3.3-2.1 3.9-.8.6-2.1 1-3.8 1h-8.6c-1.9 0-3.2-.3-4.1-1-.8-.7-1.5-2-1.9-4L156 23l-17.9 68.4c-.5 2-1.1 3.3-1.9 4-.8.6-2.2 1-4 1h-8.1zm126.4 2.7c-5.2 0-10.4-.6-15.4-1.8-5-1.2-8.9-2.5-11.5-4-1.6-.9-2.7-1.9-3.1-2.8-.4-.9-.6-1.9-.6-2.8v-5.1c0-2.1.8-3.1 2.3-3.1.6 0 1.2.1 1.8.3.6.2 1.5.6 2.5 1 3.4 1.5 7.1 2.7 11 3.5 4 .8 7.9 1.2 11.9 1.2 6.3 0 11.2-1.1 14.6-3.3 3.4-2.2 5.2-5.4 5.2-9.5 0-2.8-.9-5.1-2.7-7-1.8-1.9-5.2-3.6-10.1-5.2l-14.5-4.5c-7.3-2.3-12.7-5.7-16-10.2-3.3-4.4-5-9.3-5-14.5 0-4.2.9-7.9 2.7-11.1 1.8-3.2 4.2-6 7.2-8.2 3-2.3 6.4-4 10.4-5.2 4-1.2 8.2-1.7 12.6-1.7 2.2 0 4.5.1 6.7.4 2.3.3 4.4.7 6.5 1.1 2 .5 3.9 1 5.7 1.6 1.8.6 3.2 1.2 4.2 1.8 1.4.8 2.4 1.6 3 2.5.6.8.9 1.9.9 3.3v4.7c0 2.1-.8 3.2-2.3 3.2-.8 0-2.1-.4-3.8-1.2-5.7-2.6-12.1-3.9-19.2-3.9-5.7 0-10.2 1-13.4 2.9-3.2 1.9-4.8 4.9-4.8 9 0 2.8 1 5.2 3 7.1 2 1.9 5.7 3.8 11 5.5l14.2 4.5c7.2 2.3 12.4 5.5 15.5 9.6 3.1 4.1 4.6 8.8 4.6 14 0 4.3-.9 8.2-2.6 11.6-1.8 3.4-4.2 6.4-7.3 8.8-3.1 2.5-6.8 4.3-11.1 5.6-4.5 1.4-9.2 2.1-14.3 2.1z" fill="#232F3E"/>
            <path d="M273.5 143.7c-32.9 24.3-80.7 37.2-121.8 37.2-57.6 0-109.5-21.3-148.7-56.7-3.1-2.8-.3-6.6 3.4-4.4 42.4 24.6 94.7 39.5 148.8 39.5 36.5 0 76.6-7.6 113.5-23.2 5.5-2.5 10.2 3.6 4.8 7.6z" fill="#FF9900"/>
            <path d="M287.2 128.1c-4.2-5.4-27.8-2.6-38.5-1.3-3.2.4-3.7-2.4-.8-4.5 18.8-13.2 49.7-9.4 53.3-5 3.6 4.5-1 35.4-18.6 50.2-2.7 2.3-5.3 1.1-4.1-1.9 3.9-9.9 12.9-32.2 8.7-37.5z" fill="#FF9900"/>
        </svg>
    </div>
    <span class="m-brand">AWS Server</span>
    <h1>We'll be right back.</h1>
    <p>Our AWS server is currently down for scheduled maintenance.<br>Estimated back online in:</p>
    <div class="m-timer" id="maint-timer-land">5:00</div>
    <p class="m-note" id="maint-note-land">This page refreshes automatically.</p>
</div>
<script>
(function(){
    var end=Date.now()+5*60*1000;
    function fmt(ms){var s=Math.max(0,Math.ceil(ms/1000));return Math.floor(s/60)+':'+(('0'+(s%60)).slice(-2));}
    var timer=document.getElementById('maint-timer-land');
    var note=document.getElementById('maint-note-land');
    var desc=timer&&timer.previousElementSibling;
    var done=false;
    setInterval(function(){
        if(done)return;
        var rem=end-Date.now();
        if(rem>0){if(timer)timer.textContent=fmt(rem);}
        else{done=true;if(timer){timer.style.fontSize='.875rem';timer.style.color='#ef4444';timer.style.fontFamily='inherit';timer.style.fontWeight='600';timer.textContent='Service Disrupted';}if(desc)desc.textContent='The AWS server is taking longer than expected to recover.';if(note){note.style.color='#6b7280';note.style.fontSize='.8rem';note.textContent='This may take up to 24 hours. We apologize for the inconvenience — please check back later.';}}
    },500);
})();
</script>
@endif
</body>
</html>
