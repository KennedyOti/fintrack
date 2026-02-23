@extends('layouts.app')

@section('title', 'Reset Password - FinTrack')

@section('content')
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
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
</div>
@endSection

@push('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const btn = input.nextElementSibling;
        const icon = btn.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
