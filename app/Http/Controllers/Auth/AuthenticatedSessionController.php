<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Credentials are validated by LoginRequest::authenticate().
     * If the user has two-factor authentication enabled, we halt the
     * fully-authenticated session, generate an OTP, send it by e-mail,
     * and redirect to the OTP verification page instead.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Validate credentials (throws ValidationException on failure).
        $request->authenticate();

        // 2. Grab the now-authenticated user, then immediately log out so the
        //    session is not considered fully authenticated until OTP is verified.
        $user = Auth::user();
        Auth::guard('web')->logout();

        // 3. Two-factor check: skip OTP for Google OAuth accounts (they have
        //    no local password and Google already enforces its own 2-step).
        if ($user->two_factor_enabled) {
            // Generate OTP and send the e-mail.
            TwoFactorController::generateAndSendOtp($user);

            // Park the pending user in the session so TwoFactorController can
            // retrieve it without exposing the user ID in the URL.
            $request->session()->put('two_factor_pending_user_id', $user->id);
            $request->session()->put('two_factor_remember', $request->boolean('remember'));

            return redirect()->route('two-factor.create');
        }

        // 4. 2FA is disabled for this user — complete the login normally.
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
