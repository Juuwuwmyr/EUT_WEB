<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity — EUT Admin</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0d0e1a;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #e5e7eb;
        }

        .card {
            background: #13141f;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,.5);
        }

        .lock-icon {
            width: 3.5rem;
            height: 3.5rem;
            background: rgba(245,158,11,.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        h1 {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .35rem;
        }

        .subtitle {
            text-align: center;
            font-size: .82rem;
            color: #6b7280;
            margin-bottom: 1.75rem;
            line-height: 1.5;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: .75rem;
            padding: .6rem .9rem;
            margin-bottom: 1.5rem;
        }

        .avatar {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background: #f59e0b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .8rem;
            color: #000;
            flex-shrink: 0;
        }

        .user-info p { font-size: .82rem; font-weight: 600; color: #e5e7eb; }
        .user-info span { font-size: .72rem; color: #6b7280; }

        label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .45rem;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            background: #0d0e1a;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: .65rem;
            padding: .75rem 2.75rem .75rem .9rem;
            font-size: .9rem;
            color: #e5e7eb;
            outline: none;
            transition: border-color .2s;
        }

        input:focus { border-color: #f59e0b; }

        .toggle-pw {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .error {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: .6rem;
            padding: .65rem .9rem;
            font-size: .8rem;
            color: #f87171;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .btn-verify {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            border-radius: .75rem;
            color: #000;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: opacity .2s;
        }

        .btn-verify:hover { opacity: .9; }
        .btn-verify:disabled { opacity: .5; cursor: not-allowed; }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            font-size: .78rem;
            color: #6b7280;
            text-decoration: none;
            transition: color .2s;
        }

        .back-link:hover { color: #f59e0b; }
    </style>
</head>
<body>

<div class="card">
    {{-- Lock icon --}}
    <div class="lock-icon">
        <svg width="22" height="22" fill="none" stroke="#f59e0b" stroke-width="2.2" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
    </div>

    <h1>Confirm Your Identity</h1>
    <p class="subtitle">Enter your password to access {{ \App\Http\Middleware\RequireAdminVerification::scopeLabel($scope) }}.<br>Your password is required every time you open this page.</p>

    {{-- Logged-in user badge --}}
    <div class="user-badge">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <p>{{ auth()->user()->name }}</p>
            <span>{{ auth()->user()->email }}</span>
        </div>
    </div>

    {{-- Error message --}}
    @if($errors->has('password'))
    <div class="error">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ $errors->first('password') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.verify.submit') }}" onsubmit="handleSubmit(this)">
        @csrf
        <input type="hidden" name="scope" value="{{ $scope }}">
        <label for="password">Password</label>
        <div class="input-wrap">
            <input type="password" id="password" name="password" placeholder="Enter your password" autofocus required>
            <button type="button" class="toggle-pw" onclick="togglePw()" title="Show/hide password">
                <svg id="eyeIcon" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>

        <button type="submit" class="btn-verify" id="submitBtn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Verify &amp; Continue
        </button>
    </form>

    <a href="{{ route('admin.dashboard') }}" class="back-link">← Back to Dashboard</a>
</div>

<script>
function togglePw() {
    var input = document.getElementById('password');
    var icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}

function handleSubmit(form) {
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-opacity=".25"/><path d="M12 2a10 10 0 0110 10" stroke-linecap="round" style="animation:spin .8s linear infinite;transform-origin:center;"/></svg> Verifying…';
}
</script>

</body>
</html>
