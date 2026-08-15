<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    // -------------------------------------------------------
    // STEP 1: Send OTP to email
    // -------------------------------------------------------
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Always return success to prevent email enumeration
        if (! $user) {
            return response()->json([
                'success' => true,
                'message' => 'If that email exists, a reset code has been sent.',
            ]);
        }

        // Google-only users have no password
        if ($user->provider === 'google' && ! $user->password) {
            return response()->json([
                'success' => false,
                'message' => 'This account uses Google Sign-In. Please login with Google.',
            ], 422);
        }

        // Rate limit: 1 code per 60 seconds per email
        $cooldownKey = 'pwd_reset_cooldown_' . md5($request->email);
        if (Cache::has($cooldownKey)) {
            $remaining = Cache::get($cooldownKey);
            return response()->json([
                'success'   => false,
                'message'   => "Please wait {$remaining}s before requesting another code.",
                'cooldown'  => $remaining,
            ], 429);
        }

        // Generate 6-digit OTP
        $code    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'pwd_reset_code_' . md5($request->email);

        // Store code for 15 minutes
        Cache::put($cacheKey, Hash::make($code), now()->addMinutes(15));

        // Set 60s cooldown
        Cache::put($cooldownKey, 60, now()->addSeconds(60));

        // Countdown ticker for cooldown (decrement each second via separate keys is heavy,
        // so we store the expiry timestamp and compute remaining on the client)
        Cache::put($cooldownKey . '_expires', now()->addSeconds(60)->timestamp, now()->addSeconds(65));

        try {
            $user->notify(new PasswordResetCodeNotification($code));
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
            Cache::forget($cooldownKey);

            \Log::error('Password reset email failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not send the reset email. Please try again.',
            ], 500);
        }

        \Log::info('Password reset code sent', ['email' => $request->email]);

        return response()->json([
            'success'  => true,
            'message'  => 'A 6-digit reset code has been sent to your email.',
            'cooldown' => 60,
        ]);
    }

    // -------------------------------------------------------
    // STEP 2: Verify OTP code
    // -------------------------------------------------------
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $cacheKey = 'pwd_reset_code_' . md5($request->email);
        $hashed   = Cache::get($cacheKey);

        if (! $hashed) {
            return response()->json([
                'success' => false,
                'message' => 'Code has expired. Please request a new one.',
            ], 422);
        }

        if (! Hash::check($request->code, $hashed)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code. Please try again.',
            ], 422);
        }

        // Store a short-lived verified token so reset step can confirm the user passed OTP
        $verifiedKey = 'pwd_reset_verified_' . md5($request->email);
        Cache::put($verifiedKey, true, now()->addMinutes(10));

        return response()->json([
            'success' => true,
            'message' => 'Code verified. Please enter your new password.',
        ]);
    }

    // -------------------------------------------------------
    // STEP 3: Reset password
    // -------------------------------------------------------
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'code'     => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.min'       => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // Re-check OTP is still valid
        $cacheKey    = 'pwd_reset_code_' . md5($request->email);
        $verifiedKey = 'pwd_reset_verified_' . md5($request->email);
        $hashed      = Cache::get($cacheKey);

        if (! $hashed && ! Cache::has($verifiedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Reset session expired. Please start over.',
            ], 422);
        }

        if ($hashed && ! Hash::check($request->code, $hashed)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code. Please try again.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 404);
        }

        // Update password
        $user->updateQuietly([
            'password' => Hash::make($request->password),
        ]);

        // Clean up cache
        Cache::forget($cacheKey);
        Cache::forget($verifiedKey);
        Cache::forget('pwd_reset_cooldown_' . md5($request->email));

        \App\Models\AuditLog::record(
            action:      'password_reset',
            description: "{$user->name} reset their password.",
            model:       $user,
        );

        \Log::info('Password reset successful', ['email' => $request->email]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! You can now log in.',
        ]);
    }

    // -------------------------------------------------------
    // Get cooldown remaining
    // -------------------------------------------------------
    public function cooldown(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $expiresAt = Cache::get('pwd_reset_cooldown_' . md5($request->email) . '_expires');
        $remaining = $expiresAt ? max(0, $expiresAt - now()->timestamp) : 0;

        return response()->json(['remaining' => $remaining]);
    }
}
