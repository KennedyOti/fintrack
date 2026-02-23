<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Update existing user's Google ID and avatar if not set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'google_avatar' => $googleUser->avatar,
                    ]);
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'google_avatar' => $googleUser->avatar,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }

            // Log in the user
            Auth::login($user);

            // Regenerate session
            request()->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false))->with('status', 'You are logged in successfully!');
        } catch (\Exception $e) {
Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}
