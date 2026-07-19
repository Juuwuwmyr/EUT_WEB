<?php

namespace App\Http\Controllers;

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

            $redirect = Auth::user()->isAdmin()
                ? route('admin.dashboard')
                : (Auth::user()->isRider() ? route('rider.dashboard') : route('shop.home'));

            return response()->json([
                'success'  => true,
                'message'  => 'Login successful.',
                'redirect' => $redirect,
                'user'     => [
                    'name'   => Auth::user()->name,
                    'email'  => Auth::user()->email,
                    'avatar' => Auth::user()->avatar,
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
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'name.required'     => 'Please enter your full name.',
            'email.unique'      => 'An account with this email already exists.',
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
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

        $redirect = $user->isAdmin()
            ? route('admin.dashboard')
            : ($user->isRider() ? route('rider.dashboard') : route('shop.home'));

        return redirect($redirect)->with('success', 'Welcome, ' . $user->name . '!');
    }
}
