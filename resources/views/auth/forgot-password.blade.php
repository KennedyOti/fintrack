@extends('layouts.app')

@section('title', 'Forgot Password - FinTrack')

@section('content')
@php
    $statusMsg = session('status');
    $emailErr  = $errors->first('email');
@endphp

<div class="auth-page auth-page-only">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="brand">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
            </div>
            <h4>Reset Password</h4>
            <p>Enter your email to get a reset link</p>
        </div>

        <div class="auth-card-body">
            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                @csrf

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
                               required
                               autofocus>
                    </div>
                    @if ($emailErr && !$statusMsg)
                    <div class="invalid-feedback d-block">{{ $emailErr }}</div>
                    @endif
                </div>

                <button type="submit" class="btn auth-btn auth-btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>

            <div class="auth-footer">
                <p>Remember your password? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>

    {{-- Success popup: reset link sent --}}
    @if ($statusMsg)
    <div class="auth-popup-overlay" id="authPopupOverlay" onclick="closeAuthPopupOnOverlay(event)">
        <div class="auth-popup auth-popup--success">
            <button class="auth-popup-close" onclick="closeAuthPopup()" aria-label="Dismiss">
                <i class="fas fa-times"></i>
            </button>
            <div class="auth-popup-icon-wrap"><i class="fas fa-paper-plane"></i></div>
            <h5 class="auth-popup-title">Check Your Inbox</h5>
            <p class="auth-popup-message">
                {{ $statusMsg }}<br>
                <strong>Tip:</strong> Check your spam or junk folder if it doesn't arrive within a few minutes.
            </p>
            <div class="auth-popup-actions">
                <a href="{{ route('login') }}" class="auth-popup-btn auth-popup-btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Back to Sign In
                </a>
                <button class="auth-popup-btn auth-popup-btn-secondary" onclick="closeAuthPopup()">
                    Send Again
                </button>
            </div>
        </div>
    </div>

    {{-- Error popup: email not found or other error --}}
    @elseif ($emailErr)
    <div class="auth-popup-overlay" id="authPopupOverlay" onclick="closeAuthPopupOnOverlay(event)">
        <div class="auth-popup auth-popup--error">
            <button class="auth-popup-close" onclick="closeAuthPopup()" aria-label="Dismiss">
                <i class="fas fa-times"></i>
            </button>
            <div class="auth-popup-icon-wrap"><i class="fas fa-envelope-circle-check"></i></div>
            <h5 class="auth-popup-title">Email Not Found</h5>
            <p class="auth-popup-message">
                {{ $emailErr }}<br>
                Double-check the address or <strong>create a new account</strong> to get started.
            </p>
            <div class="auth-popup-actions">
                <a href="{{ route('register') }}" class="auth-popup-btn auth-popup-btn-primary">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
                <button class="auth-popup-btn auth-popup-btn-secondary" onclick="closeAuthPopup()">
                    Try Again
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
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
