<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Display the OTP verification form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('two_factor_pending_user_id');

        if (! $userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget(['two_factor_pending_user_id', 'two_factor_remember']);
            return redirect()->route('login');
        }

        return view('auth.two-factor', [
            'maskedEmail' => $this->maskEmail($user->email),
            'expiresAt'   => $user->otp_expires_at,
        ]);
    }

    /**
     * Verify the submitted OTP code.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [
            'otp.required' => 'Please enter the verification code.',
            'otp.size'     => 'The verification code must be 6 digits.',
            'otp.regex'    => 'The verification code must contain digits only.',
        ]);

        $userId = $request->session()->get('two_factor_pending_user_id');

        if (! $userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please login again.']);
        }

        // Rate-limit by user IP
        $rateLimitKey = 'otp-verify:' . $request->ip() . ':' . $userId;

        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            throw ValidationException::withMessages([
                'otp' => "Too many verification attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget(['two_factor_pending_user_id', 'two_factor_remember']);
            return redirect()->route('login');
        }

        // Check if OTP has expired
        if (! $user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            $this->clearOtp($user);
            throw ValidationException::withMessages([
                'otp' => 'Your verification code has expired. Please request a new one.',
            ]);
        }

        // Verify the OTP
        if (! $user->otp_code || ! Hash::check($request->otp, $user->otp_code)) {
            RateLimiter::hit($rateLimitKey, 300); // 5-minute window
            $remaining = self::MAX_ATTEMPTS - RateLimiter::attempts($rateLimitKey);
            throw ValidationException::withMessages([
                'otp' => "Invalid verification code. {$remaining} attempt(s) remaining.",
            ]);
        }

        // OTP verified — clear it and log the user in
        RateLimiter::clear($rateLimitKey);
        $this->clearOtp($user);

        $remember = $request->session()->pull('two_factor_remember', false);
        $request->session()->forget('two_factor_pending_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Resend a fresh OTP code (AJAX endpoint).
     */
    public function resend(Request $request): JsonResponse
    {
        $userId = $request->session()->get('two_factor_pending_user_id');

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
            ], 401);
        }

        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Rate-limit resend requests
        $resendKey = 'otp-resend:' . $request->ip() . ':' . $userId;

        if (RateLimiter::tooManyAttempts($resendKey, 3)) {
            $seconds = RateLimiter::availableIn($resendKey);
            return response()->json([
                'success' => false,
                'message' => "Please wait {$seconds} seconds before requesting another code.",
            ], 429);
        }

        // Enforce per-send cooldown (60 seconds between resends)
        if ($user->otp_expires_at) {
            $secondsSinceSent = now()->diffInSeconds(
                $user->otp_expires_at->subMinutes(self::OTP_EXPIRY_MINUTES),
                false
            );
            if ($secondsSinceSent < self::RESEND_COOLDOWN_SECONDS && $secondsSinceSent >= 0) {
                $wait = self::RESEND_COOLDOWN_SECONDS - (int) $secondsSinceSent;
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$wait} seconds before requesting another code.",
                    'wait'    => $wait,
                ], 429);
            }
        }

        RateLimiter::hit($resendKey, 300);

        $this->generateAndSendOtp($user);

        return response()->json([
            'success'    => true,
            'message'    => 'A new verification code has been sent to your email.',
            'expires_in' => self::OTP_EXPIRY_MINUTES * 60,
        ]);
    }

    /**
     * Generate a fresh OTP, persist it, and dispatch the mail.
     */
    public static function generateAndSendOtp(User $user): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'otp_attempts'   => 0,
        ]);

        Mail::to($user->email)->send(new OtpMail($user, $otp, self::OTP_EXPIRY_MINUTES));
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    private function clearOtp(User $user): void
    {
        $user->update([
            'otp_code'       => null,
            'otp_expires_at' => null,
            'otp_attempts'   => 0,
        ]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $masked = substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 3, 2)) . substr($local, -1);
        return $masked . '@' . $domain;
    }
}
