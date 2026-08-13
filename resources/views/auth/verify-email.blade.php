<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — E.U.T Snack House</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top, #1a1208 0%, #0a0a0a 45%);
            font-family: 'Inter', system-ui, sans-serif;
            color: #e5e7eb;
            padding: 1.5rem;
        }
        .shell { width: 100%; max-width: 460px; }
        .brand {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        .brand-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .4px;
        }
        .brand-tag {
            margin-top: .35rem;
            font-size: .72rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1.4px;
        }
        .card {
            background: rgba(20,20,20,.92);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.35rem;
            padding: 2rem 1.75rem 1.5rem;
            box-shadow: 0 30px 80px rgba(0,0,0,.45);
            backdrop-filter: blur(8px);
        }
        .step {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: rgba(245,158,11,.1);
            border: 1px solid rgba(245,158,11,.18);
            color: #fbbf24;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .4px;
            margin-bottom: 1rem;
        }
        h1 {
            font-size: 1.45rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .55rem;
        }
        .sub {
            font-size: .875rem;
            color: #9ca3af;
            line-height: 1.65;
            margin-bottom: 1.35rem;
        }
        .email-pill {
            display: inline-block;
            margin-top: .35rem;
            padding: .35rem .7rem;
            border-radius: .55rem;
            background: rgba(245,158,11,.08);
            border: 1px solid rgba(245,158,11,.16);
            color: #f59e0b;
            font-size: .82rem;
            font-weight: 600;
            word-break: break-all;
        }
        .alert {
            border-radius: .85rem;
            padding: .8rem 1rem;
            font-size: .8rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .alert-success {
            background: rgba(16,185,129,.1);
            border: 1px solid rgba(16,185,129,.22);
            color: #6ee7b7;
        }
        .alert-error {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.22);
            color: #fca5a5;
        }
        .otp-row {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: .55rem;
            margin: 1.25rem 0 1rem;
        }
        .otp-box {
            width: 100%;
            aspect-ratio: 1;
            max-height: 58px;
            border-radius: .85rem;
            border: 1px solid rgba(255,255,255,.12);
            background: #0a0a0a;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 700;
            text-align: center;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .otp-box:focus {
            border-color: rgba(245,158,11,.55);
            box-shadow: 0 0 0 3px rgba(245,158,11,.12);
        }
        .otp-hidden {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            width: 0;
            height: 0;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: .95rem 1rem;
            border-radius: .85rem;
            font-size: .9rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: transform .12s, opacity .12s;
        }
        .btn:active { transform: scale(.985); }
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #111;
            box-shadow: 0 10px 24px rgba(245,158,11,.18);
        }
        .btn-primary:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none;
        }
        .footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 1.15rem;
            padding-top: 1.15rem;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .link-btn {
            background: none;
            border: none;
            color: #9ca3af;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
        }
        .link-btn:hover { color: #f59e0b; }
        .link-btn:disabled {
            color: #4b5563;
            cursor: not-allowed;
        }
        .link-btn:disabled:hover { color: #4b5563; }
        .card { position: relative; }
        .card-loading {
            display: none;
            position: absolute;
            inset: 0;
            z-index: 10;
            background: rgba(10,10,10,.78);
            backdrop-filter: blur(3px);
            border-radius: inherit;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
        }
        .card-loading.active { display: flex; }
        .spinner {
            width: 32px;
            height: 32px;
            border: 3px solid rgba(255,255,255,.12);
            border-top-color: #f59e0b;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: .82rem; color: #d1d5db; font-weight: 600; }
        .hint {
            margin-top: 1rem;
            font-size: .74rem;
            color: #6b7280;
            line-height: 1.55;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="brand">
        <div class="brand-name">E.U.T Snack House</div>
        <div class="brand-tag">Secure account verification</div>
    </div>

    <div class="card">
        <div class="card-loading" id="cardLoading">
            <div class="spinner"></div>
            <div class="loading-text" id="cardLoadingText">Please wait…</div>
        </div>
        <div class="step">Step 2 of 2 · Verify email</div>
        <h1>Enter your code</h1>
        <p class="sub">
            @if ($pending ?? false)
                We sent a 6-digit code to complete your signup.
            @else
                Enter the code we sent to verify your email.
            @endif
            <span class="email-pill">{{ $email }}</span>
        </p>

        @if (session('resent'))
        <div class="alert alert-success">A new code has been sent. Check your inbox and spam folder.</div>
        @endif

        @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('verification.verify') }}" id="verifyForm">
            @csrf
            <input type="hidden" name="code" id="codeValue" value="{{ old('code') }}">
            <div class="otp-row" id="otpRow">
                @for ($i = 0; $i < 6; $i++)
                <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" data-index="{{ $i }}" aria-label="Digit {{ $i + 1 }}">
                @endfor
            </div>
            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Complete signup</button>
        </form>

        <div class="footer-actions">
            <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                @csrf
                <button type="submit" class="link-btn" id="resendBtn">Resend code</button>
            </form>
            <form method="POST" action="{{ route('verification.cancel') }}">
                @csrf
                <button type="submit" class="link-btn">Wrong email? Start over</button>
            </form>
        </div>

        <p class="hint">Codes expire in 15 minutes. Search your inbox for <strong style="color:#9ca3af;">E.U.T Snack House</strong>.</p>
    </div>
</div>

<script>
(function () {
    const boxes = Array.from(document.querySelectorAll('.otp-box'));
    const hidden = document.getElementById('codeValue');
    const submitBtn = document.getElementById('submitBtn');
    const verifyForm = document.getElementById('verifyForm');
    const resendForm = document.getElementById('resendForm');
    const resendBtn = document.getElementById('resendBtn');
    const cardLoading = document.getElementById('cardLoading');
    const cardLoadingText = document.getElementById('cardLoadingText');
    let resendSeconds = {{ (int) ($resendCooldown ?? 0) }};
    let resendTimer = null;

    function setCardLoading(on, text) {
        cardLoadingText.textContent = text || 'Please wait…';
        cardLoading.classList.toggle('active', on);
        verifyForm.querySelectorAll('input, button').forEach(el => { el.disabled = on; });
        if (!on) syncHidden();
    }

    function updateResendBtn() {
        if (resendSeconds > 0) {
            resendBtn.disabled = true;
            resendBtn.textContent = 'Resend code (' + resendSeconds + 's)';
        } else {
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend code';
        }
    }

    function startResendCooldown(seconds) {
        resendSeconds = seconds;
        updateResendBtn();
        if (resendTimer) clearInterval(resendTimer);
        resendTimer = setInterval(() => {
            resendSeconds -= 1;
            if (resendSeconds <= 0) {
                resendSeconds = 0;
                clearInterval(resendTimer);
            }
            updateResendBtn();
        }, 1000);
    }

    const oldCode = (hidden.value || '').replace(/\D/g, '').slice(0, 6);

    if (oldCode.length) {
        oldCode.split('').forEach((digit, i) => { boxes[i].value = digit; });
    }

    function syncHidden() {
        const code = boxes.map(b => b.value.replace(/\D/g, '')).join('').slice(0, 6);
        hidden.value = code;
        submitBtn.disabled = code.length !== 6;
    }

    boxes.forEach((box, index) => {
        box.addEventListener('input', () => {
            box.value = box.value.replace(/\D/g, '').slice(-1);
            if (box.value && index < boxes.length - 1) boxes[index + 1].focus();
            syncHidden();
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !box.value && index > 0) {
                boxes[index - 1].focus();
            }
        });

        box.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((digit, i) => { boxes[i].value = digit; });
            boxes[Math.min(pasted.length, 5)].focus();
            syncHidden();
        });
    });

    verifyForm.addEventListener('submit', () => {
        setCardLoading(true, 'Verifying code…');
    });

    resendForm.addEventListener('submit', (e) => {
        if (resendSeconds > 0) {
            e.preventDefault();
            return;
        }
        setCardLoading(true, 'Sending new code…');
        startResendCooldown(60);
    });

    syncHidden();
    if (resendSeconds > 0) startResendCooldown(resendSeconds);
    else updateResendBtn();
    boxes[0].focus();
})();
</script>
</body>
</html>
