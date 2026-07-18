<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EUT Restaurant — Food Delivery</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            background: #fff;
        }

        /* ── LEFT PANEL ── */
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

        /* ── RIGHT PANEL ── */
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

        /* ── BUTTONS ── */
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

        /* ── MOBILE ── */
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
        <img src="{{ asset('images/phone.png') }}" alt="EUT App" class="phone-img">
    </div>

    <!-- RIGHT: content -->
    <div class="right-panel">

        <img src="{{ asset('images/DeliveryPanda.png') }}" alt="Delivery Panda" class="panda-img">

        <h1 class="headline">Download Our App</h1>

        <p class="subtext">
            Get the best experience by installing our app.
        </p>

        <div class="btn-group">

            <a href="#" class="btn-download">
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

</body>
</html>
