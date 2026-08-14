<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\PendingSignup;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $redirectTo = '/shop';

    // -------------------------------------------------------
    // EMAIL LOGIN
    // -------------------------------------------------------
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $loggedIn = Auth::user();

            if ($loggedIn->provider === 'email' && ! $loggedIn->hasVerifiedEmail()
                && ! $loggedIn->isChef() && ! $loggedIn->isAdmin() && ! $loggedIn->isRider()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Please verify your email address to continue.',
                    'redirect' => route('verification.notice'),
                ]);
            }

            AuditLog::record(
                action:      'login',
                description: "{$loggedIn->name} logged in.",
                model:       $loggedIn,
            );

            $redirect = $loggedIn->isAdmin()
                ? route('admin.dashboard')
                : ($loggedIn->isRider()
                    ? route('rider.dashboard')
                    : ($loggedIn->isChef() ? route('chef.dashboard') : route('shop.home')));

            return response()->json([
                'success'  => true,
                'message'  => 'Login successful.',
                'redirect' => $redirect,
                'user'     => [
                    'name'   => $loggedIn->name,
                    'email'  => $loggedIn->email,
                    'avatar' => $loggedIn->avatar,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.',
        ], 401);
    }

    // -------------------------------------------------------
    // EMAIL SIGN UP
    // -------------------------------------------------------
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:200',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'required|string|min:10|max:20',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'name.required'     => 'Please enter your full name.',
            'email.unique'      => 'An account with this email already exists.',
            'phone.required'    => 'Please enter your mobile number.',
            'phone.min'         => 'Please enter a valid mobile number.',
            'password.min'      => 'Password must be at least 6 characters.',
            'password.confirmed'=> 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $code = PendingSignup::generateCode();

        PendingSignup::put([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => $request->password,
        ], $code);

        try {
            PendingSignup::sendCodeEmail($request->email, trim($request->name), $code);
        } catch (\Throwable $e) {
            PendingSignup::forget();

            \Log::error('Signup verification email failed', [
                'email'  => $request->email,
                'mailer' => config('mail.default'),
                'error'  => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not send the verification email. Please try again.',
            ], 500);
        }

        \Log::info('Pending signup verification code sent', [
            'email'  => $request->email,
            'mailer' => config('mail.default'),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Check your email for a 6-digit code to complete signup.',
            'redirect' => route('verification.notice'),
        ]);
    }

    // -------------------------------------------------------
    // EMAIL VERIFICATION
    // -------------------------------------------------------
    public function showVerificationNotice(Request $request)
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('shop.home');
        }

        $email = PendingSignup::email() ?? $request->user()?->email;

        if (! $email) {
            return redirect()->route('restaurant')
                ->with('error', 'No signup in progress. Please create an account first.');
        }

        return view('auth.verify-email', [
            'email'          => $email,
            'pending'        => PendingSignup::has(),
            'resendCooldown' => PendingSignup::resendCooldownRemaining(),
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        $code = preg_replace('/\D/', '', (string) $request->input('code'));

        if (strlen($code) !== 6) {
            return back()->with('error', 'Please enter the 6-digit code from your email.');
        }

        if (PendingSignup::has()) {
            if (PendingSignup::isExpired()) {
                PendingSignup::forget();

                return redirect()->route('restaurant')
                    ->with('error', 'Your verification code expired. Please sign up again.');
            }

            if (! PendingSignup::verifyCode($code)) {
                return back()->with('error', 'Invalid code. Check your email and try again.');
            }

            $pending = PendingSignup::get();

            $user = User::withoutEvents(function () use ($pending) {
                return User::create([
                    'name'              => $pending['name'],
                    'email'             => $pending['email'],
                    'phone'             => $pending['phone'],
                    'password'          => $pending['password'],
                    'provider'          => 'email',
                    'role'              => 'user',
                    'email_verified_at' => now(),
                ]);
            });

            PendingSignup::forget();

            Auth::login($user);
            $request->session()->regenerate();

            event(new Verified($user));

            AuditLog::record(
                action:      'signup',
                description: "{$user->name} signed up with email.",
                model:       $user,
            );

            return redirect()
                ->route('shop.home')
                ->with('success', 'Welcome to E.U.T Snack House! Your account is ready.');
        }

        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('shop.home');
        }

        if (! $request->user()) {
            return redirect()->route('restaurant')
                ->with('error', 'No signup in progress. Please create an account first.');
        }

        if (! $request->user()->verifyEmailWithCode($code)) {
            return back()->with('error', 'Invalid or expired code. Request a new one and try again.');
        }

        event(new Verified($request->user()));

        $verified = $request->user();
        $redirect = $verified->isAdmin()
            ? route('admin.dashboard')
            : ($verified->isRider()
                ? route('rider.dashboard')
                : ($verified->isChef() ? route('chef.dashboard') : route('shop.home')));

        return redirect($redirect)
            ->with('success', 'Email verified! Welcome to E.U.T Snack House.');
    }

    public function resendVerificationEmail(Request $request)
    {
        $cooldown = PendingSignup::resendCooldownRemaining();

        if ($cooldown > 0) {
            return back()->with('error', "Please wait {$cooldown}s before requesting a new code.");
        }

        if (PendingSignup::has()) {
            if (PendingSignup::isExpired()) {
                PendingSignup::forget();

                return redirect()->route('restaurant')
                    ->with('error', 'Your verification session expired. Please sign up again.');
            }

            $pending = PendingSignup::get();
            $code    = PendingSignup::generateCode();
            PendingSignup::refreshCode($code);

            try {
                PendingSignup::sendCodeEmail($pending['email'], $pending['name'], $code);
                PendingSignup::markCodeSent();
            } catch (\Throwable $e) {
                \Log::error('Resend pending signup code failed', [
                    'email' => $pending['email'],
                    'error' => $e->getMessage(),
                ]);

                return back()->with(
                    'error',
                    config('mail.default') === 'log'
                        ? 'Email is not configured on the server. Please contact support.'
                        : 'Could not send the code right now. Please try again in a minute.'
                );
            }

            return back()->with('resent', true);
        }

        if (! $request->user()) {
            return redirect()->route('restaurant')
                ->with('error', 'No signup in progress. Please create an account first.');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('shop.home');
        }

        try {
            $this->dispatchVerificationEmail($request->user());
            PendingSignup::markCodeSent();
        } catch (\Throwable $e) {
            \Log::error('Resend verification email failed', [
                'email'  => $request->user()->email,
                'mailer' => config('mail.default'),
                'error'  => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                config('mail.default') === 'log'
                    ? 'Email is not configured on the server (MAIL_MAILER=log). Ask the admin to fix SMTP settings.'
                    : 'Could not send the email right now. Please try again in a minute or check spam.'
            );
        }

        return back()->with('resent', true);
    }

    public function cancelPendingSignup(Request $request)
    {
        PendingSignup::forget();

        if ($request->user() && ! $request->user()->hasVerifiedEmail()) {
            $request->user()->delete();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('restaurant')
            ->with('success', 'Signup cancelled. You can create a new account anytime.');
    }

    /**
     * Send verification email via SMTP and log delivery attempt.
     *
     * @throws \RuntimeException
     */
    protected function dispatchVerificationEmail(User $user): void
    {
        if (config('mail.default') === 'log') {
            throw new \RuntimeException('MAIL_MAILER is log — emails are written to the log file only.');
        }

        $user->sendEmailVerificationNotification();

        \Log::info('Verification email dispatched', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'mailer'  => config('mail.default'),
            'host'    => config('mail.mailers.smtp.host'),
        ]);
    }

    // -------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------
    public function logout(Request $request)
    {
        $user = auth()->user();

        AuditLog::record(
            action:      'logout',
            description: ($user?->name ?? 'User') . ' logged out.',
            model:       $user,
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // -------------------------------------------------------
    // UPDATE PROFILE (name + avatar)
    // -------------------------------------------------------
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'   => 'required|string|max:200',
            'phone'  => 'required|string|min:10|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'  => trim($request->name),
            'phone' => $request->phone,
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = asset('storage/' . $path);
        }

        $user->updateQuietly($data);

        AuditLog::record(
            action:      'profile_updated',
            description: "{$user->name} updated their profile.",
            model:       $user,
        );

        return response()->json([
            'success' => true,
            'user'    => [
                'name'   => $user->name,
                'email'  => $user->email,
                'phone'  => $user->phone,
                'avatar' => $user->avatar,
            ],
        ]);
    }

    // -------------------------------------------------------
    // UPDATE PASSWORD
    // -------------------------------------------------------
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'New passwords do not match.',
            'password.min'       => 'Password must be at least 6 characters.',
        ]);

        // Google-only users have no password
        if ($user->provider === 'google' && !$user->password) {
            return response()->json(['success' => false, 'message' => 'Google accounts cannot set a password here.'], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 422);
        }

        $user->updateQuietly(['password' => $request->password]); // model cast handles hashing

        AuditLog::record(
            action:      'password_changed',
            description: "{$user->name} changed their password.",
            model:       $user,
        );

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }

    // -------------------------------------------------------
    // GOOGLE REDIRECT
    // -------------------------------------------------------
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // -------------------------------------------------------
    // GOOGLE CALLBACK
    // -------------------------------------------------------
    public function handleGoogleCallback()
    {
        // Fix cURL SSL cert issue on local WAMP dev environment
        if (app()->environment('local')) {
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $provider = Socialite::driver('google')->stateless()->setHttpClient($client);
        } else {
            $provider = Socialite::driver('google')->stateless();
        }

        try {
            $googleUser = $provider->user();
        } catch (\Exception $e) {
            return redirect()->route('restaurant')->with('error', 'Google login failed. Please try again.');
        }

        // First try to find by google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // Existing Google user — refresh their info
            $user->updateQuietly([
                'name'              => $googleUser->getName(),
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        } else {
            // Check if the email is already registered via email/password
            $existing = User::where('email', $googleUser->getEmail())->first();

            if ($existing) {
                // Link the Google ID to the existing account
                $existing->updateQuietly([
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $existing->avatar ?? $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
                $user = $existing;
            } else {
                // Brand-new user via Google
                $user = User::withoutEvents(function () use ($googleUser) {
                    return User::create([
                        'google_id'         => $googleUser->getId(),
                        'name'              => $googleUser->getName(),
                        'email'             => $googleUser->getEmail(),
                        'avatar'            => $googleUser->getAvatar(),
                        'provider'          => 'google',
                        'role'              => 'user',
                        'email_verified_at' => now(),
                    ]);
                });
            }
        }

        Auth::login($user, true);

        AuditLog::record(
            action:      'login',
            description: "{$user->name} logged in via Google.",
            model:       $user,
        );

        $redirect = $user->isAdmin()
            ? route('admin.dashboard')
            : ($user->isRider()
                ? route('rider.dashboard')
                : ($user->isChef() ? route('chef.dashboard') : route('shop.home')));

        return redirect($redirect)->with('success', 'Welcome, ' . $user->name . '!');
    }
}
