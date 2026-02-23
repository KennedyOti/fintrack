@extends('layouts.app')

@section('title', 'Confirm Password - FinTrack')

@section('content')
<div class="auth-page auth-page-only">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="brand">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
            </div>
            <h4>Confirm Password</h4>
            <p>Please confirm your password to continue</p>
        </div>
        
        <div class="auth-card-body">
            <div class="alert alert-info mb-4" style="padding: 12px 16px; border-radius: 8px; background: rgba(14, 116, 144, 0.1); border: 1px solid rgba(14, 116, 144, 0.3); color: var(--ft-teal-blue); font-size: 14px;">
                This is a secure area. Please confirm your password before continuing.
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
                @csrf
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••"
                               required 
                               autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn auth-btn auth-btn-primary">
                    <i class="fas fa-check"></i> Confirm
                </button>
            </form>
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
