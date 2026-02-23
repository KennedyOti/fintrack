@extends('layouts.app')

@section('title', 'Verify Email - FinTrack')

@section('content')
<div class="auth-page auth-page-only">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="brand">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
            </div>
            <h4>Verify Your Email</h4>
            <p>Check your inbox for the verification link</p>
        </div>
        
        <div class="auth-card-body">
            <div class="text-center mb-4">
                <div style="width: 80px; height: 80px; background: rgba(14, 116, 144, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-envelope" style="font-size: 32px; color: var(--ft-teal-blue);"></i>
                </div>
                <p style="color: var(--ft-text-muted); font-size: 14px; line-height: 1.6;">
                    Thanks for signing up! We sent a verification link to your email address. Please check your inbox and click the link to verify your account.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-4" style="padding: 12px 16px; border-radius: 8px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: var(--ft-emerald); font-size: 14px;">
                A new verification link has been sent to your email address.
            </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                @csrf
                <button type="submit" class="btn auth-btn auth-btn-primary w-100">
                    <i class="fas fa-paper-plane"></i> Resend Verification Email
                </button>
            </form>

            <div class="auth-footer" style="border: none; padding-top: 0; margin-top: 0;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: var(--ft-text-muted); font-size: 14px; cursor: pointer; text-decoration: none;">
                        <i class="fas fa-sign-out-alt"></i> Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endSection
