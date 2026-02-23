@extends('layouts.app')

@section('title', 'Forgot Password - FinTrack')

@section('content')
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
            <!-- Session Status -->
            @if (session('status'))
            <div class="alert alert-success mb-4" style="padding: 12px 16px; border-radius: 8px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: var(--ft-emerald); font-size: 14px;">
                {{ session('status') }}
            </div>
            @endif

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
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
</div>
@endSection
