<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — E.U.T Snack House</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0a0a;
            font-family: 'Inter', system-ui, sans-serif;
            color: #e5e7eb;
            padding: 1.5rem;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: #141414;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.25rem;
            padding: 2rem 1.75rem;
            box-shadow: 0 24px 60px rgba(0,0,0,.45);
            text-align: center;
        }
        .icon {
            width: 3.5rem;
            height: 3.5rem;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            background: rgba(245,158,11,.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        h1 { font-size: 1.35rem; font-weight: 700; color: #fff; margin-bottom: .5rem; }
        p { font-size: .875rem; color: #9ca3af; line-height: 1.6; margin-bottom: 1rem; }
        .email { color: #f59e0b; font-weight: 600; word-break: break-all; }
        .alert {
            background: rgba(16,185,129,.12);
            border: 1px solid rgba(16,185,129,.25);
            color: #6ee7b7;
            border-radius: .75rem;
            padding: .75rem 1rem;
            font-size: .8rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            color: #fca5a5;
        }
        .code-input {
            width: 100%;
            margin: 1rem 0 .5rem;
            padding: 1rem;
            border-radius: .85rem;
            border: 1px solid rgba(255,255,255,.12);
            background: #0a0a0a;
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: .75rem;
            text-align: center;
            outline: none;
        }
        .code-input:focus {
            border-color: rgba(245,158,11,.55);
            box-shadow: 0 0 0 3px rgba(245,158,11,.12);
        }
        .actions { display: flex; flex-direction: column; gap: .75rem; margin-top: 1rem; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .85rem 1rem;
            border-radius: .75rem;
            font-size: .875rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #000;
        }
        .btn-ghost {
            background: transparent;
            color: #9ca3af;
            border: 1px solid rgba(255,255,255,.12);
        }
        .hint { font-size: .75rem; color: #6b7280; margin-top: 1rem; }
        .field-error { color: #fca5a5; font-size: .75rem; margin-bottom: .75rem; text-align: left; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">✉️</div>
    <h1>Enter verification code</h1>
    <p>
        We sent a 6-digit code to<br>
        <span class="email">{{ auth()->user()->email }}</span>
    </p>
    <p>Enter the code below to activate your account.</p>

    @if (session('resent'))
    <div class="alert">A new code has been sent. Check inbox and spam — it can take 1–2 minutes.</div>
    @endif

    @if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}">
        @csrf
        <input
            type="text"
            name="code"
            id="code"
            class="code-input"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="6"
            autocomplete="one-time-code"
            placeholder="000000"
            value="{{ old('code') }}"
            required
        >
        @error('code')
        <div class="field-error">{{ $message }}</div>
        @enderror
        <button type="submit" class="btn btn-primary">Verify email</button>
    </form>

    <div class="actions">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-ghost">Resend code</button>
        </form>
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost">Sign out</button>
        </form>
        <a href="{{ route('restaurant') }}" class="btn btn-ghost">Back to home</a>
    </div>

    <p class="hint">Search for <strong style="color:#9ca3af;">E.U.T Snack House</strong> in Gmail. Codes expire after 15 minutes.</p>
</div>
<script>
document.getElementById('code').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 6);
});
</script>
</body>
</html>
