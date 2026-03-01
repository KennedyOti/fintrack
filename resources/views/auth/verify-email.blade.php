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
            <div class="text-center mb-3">
                <div style="width:52px; height:52px; background:rgba(14,116,144,0.12); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                    <i class="fas fa-envelope" style="font-size:20px; color:var(--cyan);"></i>
                </div>
                <p style="color:var(--text-secondary); font-size:13px; line-height:1.65; margin:0;">
                    Thanks for signing up! We sent a verification link to your email address.
                    Please check your inbox and click the link to verify your account.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-3">
                A new verification link has been sent to your email address.
            </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                @csrf
                <button type="submit" class="btn auth-btn auth-btn-primary w-100">
                    <i class="fas fa-paper-plane"></i> Resend Verification Email
                </button>
            </form>

            <div class="auth-footer" style="margin-top:10px;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:var(--text-muted); font-size:12.5px; cursor:pointer;">
                        <i class="fas fa-sign-out-alt"></i> Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endSection
