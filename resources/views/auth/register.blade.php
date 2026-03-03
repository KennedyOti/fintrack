@extends('layouts.app')

@section('title', 'Register - FinTrack')

@section('content')
@php
    $emailErr    = $errors->first('email');
    $passwordErr = $errors->first('password');
    $nameErr     = $errors->first('name');
    $firstErr    = $errors->first();
    $emailTaken  = $emailErr && str_contains(strtolower($emailErr), 'taken');
    $hasErrors   = $errors->any();
@endphp

<div class="auth-page auth-page-only">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="brand">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
            </div>
            <h4>Create Account</h4>
            <p>Start managing your finances today</p>
        </div>

        <div class="auth-card-body">
            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="John Doe"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="you@example.com"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="password-hint">Must be at least 8 characters</p>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="••••••••"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn auth-btn auth-btn-primary">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <!-- Social Login Divider -->
            <div class="social-divider">
                <span>or continue with</span>
            </div>

            <!-- Google Login Button -->
            <a href="{{ route('google.redirect') }}" class="btn google-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20">
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.001-0.001,0.001-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                </svg>
                Continue with Google
            </a>

            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>

    {{-- Auth Alert Popup --}}
    @if ($hasErrors)
    <div class="auth-popup-overlay" id="authPopupOverlay" onclick="closeAuthPopupOnOverlay(event)">
        <div class="auth-popup auth-popup--error">
            <button class="auth-popup-close" onclick="closeAuthPopup()" aria-label="Dismiss">
                <i class="fas fa-times"></i>
            </button>

            @if ($emailTaken)
                <div class="auth-popup-icon-wrap"><i class="fas fa-user-xmark"></i></div>
                <h5 class="auth-popup-title">Email Already Registered</h5>
                <p class="auth-popup-message">
                    This email address is already linked to an account.
                    Try <strong>signing in</strong> instead, or use a different email address.
                </p>
                <div class="auth-popup-actions">
                    <a href="{{ route('login') }}" class="auth-popup-btn auth-popup-btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Sign In Instead
                    </a>
                    <button class="auth-popup-btn auth-popup-btn-secondary" onclick="closeAuthPopup()">
                        Use Different Email
                    </button>
                </div>

            @elseif ($passwordErr)
                <div class="auth-popup-icon-wrap"><i class="fas fa-lock"></i></div>
                <h5 class="auth-popup-title">Password Issue</h5>
                <p class="auth-popup-message">{{ $passwordErr }}</p>
                <div class="auth-popup-actions">
                    <button class="auth-popup-btn auth-popup-btn-primary" onclick="closeAuthPopup()">
                        <i class="fas fa-pen"></i> Fix Password
                    </button>
                </div>

            @else
                <div class="auth-popup-icon-wrap"><i class="fas fa-triangle-exclamation"></i></div>
                <h5 class="auth-popup-title">Registration Error</h5>
                <p class="auth-popup-message">{{ $firstErr }}</p>
                <div class="auth-popup-actions">
                    <button class="auth-popup-btn auth-popup-btn-primary" onclick="closeAuthPopup()">
                        <i class="fas fa-rotate-left"></i> Try Again
                    </button>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const btn   = input.nextElementSibling;
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function closeAuthPopup() {
        var overlay = document.getElementById('authPopupOverlay');
        if (!overlay) return;
        overlay.style.transition = 'opacity 0.18s ease';
        overlay.style.opacity = '0';
        setTimeout(function () { overlay.remove(); }, 190);
    }

    function closeAuthPopupOnOverlay(event) {
        if (event.target === event.currentTarget) closeAuthPopup();
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAuthPopup();
    });
</script>
@endpush
