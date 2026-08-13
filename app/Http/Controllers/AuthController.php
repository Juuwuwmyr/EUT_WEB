<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
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

        $user = User::create([
            'name'     => trim($request->name),
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'provider' => 'email',
            'role'     => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success'  => true,
            'message'  => 'Account created successfully.',
            'redirect' => route('shop.home'),
            'user'     => [
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar,
            ],
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

        $user->update($data);

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

        $user->update(['password' => Hash::make($request->password)]);

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
        // If already logged in, log out first so they can switch Google accounts
        if (auth()->check()) {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

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
            $user->update([
                'name'              => $googleUser->getName(),
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        } else {
            // Check if the email is already registered via email/password
            $existing = User::where('email', $googleUser->getEmail())->first();

            if ($existing) {
                // Link the Google ID to the existing account
                $existing->update([
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $existing->avatar ?? $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
                $user = $existing;
            } else {
                // Brand-new user via Google
                $user = User::create([
                    'google_id'         => $googleUser->getId(),
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'avatar'            => $googleUser->getAvatar(),
                    'provider'          => 'google',
                    'role'              => 'user',
                    'email_verified_at' => now(),
                ]);
            }
        }

        Auth::login($user, true);

        // If admin, mark as verified so they don't hit the verify gate right after login
        if ($user->isAdmin()) {
            session(['admin_verified_at' => time()]);
        }

        $redirect = $user->isAdmin()
            ? route('admin.dashboard')
            : ($user->isRider()
                ? route('rider.dashboard')
                : ($user->isChef() ? route('chef.dashboard') : route('shop.home')));

        return redirect($redirect)->with('success', 'Welcome, ' . $user->name . '!');
    }
}
