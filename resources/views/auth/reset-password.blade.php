@extends('layouts.app')

@section('title', 'Reset Password - FinTrack')

@section('content')
@php
    $emailErr    = $errors->first('email');
    $passwordErr = $errors->first('password');
    $tokenErr    = $errors->first('token');
    $firstErr    = $errors->first();
    $isExpired   = $firstErr && (
        str_contains(strtolower($firstErr), 'invalid') ||
        str_contains(strtolower($firstErr), 'expired') ||
        str_contains(strtolower($firstErr), 'token')
    );
    $hasErrors = $errors->any();
@endphp

<div class="auth-page auth-page-only">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="brand">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
            </div>
            <h4>New Password</h4>
            <p>Create your new password</p>
        </div>

        <div class="auth-card-body">
            <form method="POST" action="{{ route('password.store') }}" class="auth-form">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $request->email) }}"
                               placeholder="you@example.com"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
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
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </form>

            <div class="auth-footer">
                <p>Remember your password? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>

    {{-- Auth Alert Popup --}}
    @if ($hasErrors)
    <div class="auth-popup-overlay" id="authPopupOverlay" onclick="closeAuthPopupOnOverlay(event)">
        <div class="auth-popup auth-popup--{{ $isExpired ? 'warning' : 'error' }}">
            <button class="auth-popup-close" onclick="closeAuthPopup()" aria-label="Dismiss">
                <i class="fas fa-times"></i>
            </button>

            @if ($isExpired)
                <div class="auth-popup-icon-wrap"><i class="fas fa-link-slash"></i></div>
                <h5 class="auth-popup-title">Link Expired or Invalid</h5>
                <p class="auth-popup-message">
                    This password reset link is no longer valid. Reset links expire after a short time for your security.
                    Please <strong>request a new one</strong>.
                </p>
                <div class="auth-popup-actions">
                    <a href="{{ route('password.request') }}" class="auth-popup-btn auth-popup-btn-primary">
                        <i class="fas fa-paper-plane"></i> Request New Link
                    </a>
                    <button class="auth-popup-btn auth-popup-btn-secondary" onclick="closeAuthPopup()">
                        Close
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
                <h5 class="auth-popup-title">Reset Failed</h5>
                <p class="auth-popup-message">{{ $firstErr }}</p>
                <div class="auth-popup-actions">
                    <a href="{{ route('password.request') }}" class="auth-popup-btn auth-popup-btn-primary">
                        <i class="fas fa-paper-plane"></i> Request New Link
                    </a>
                    <button class="auth-popup-btn auth-popup-btn-secondary" onclick="closeAuthPopup()">
                        Close
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
