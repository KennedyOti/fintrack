@extends('layouts.app')

@section('title', 'Login - FinTrack')

@section('content')
<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <a href="{{ route('home') }}" class="d-flex align-items-center justify-content-center text-decoration-none mb-4">
                                <i class="fas fa-wallet fa-2x text-primary me-2"></i>
                                <span class="h3 mb-0 text-dark">FinTrack</span>
                            </a>
                            <h4 class="mb-1">Welcome Back</h4>
                            <p class="text-muted">Sign in to your account</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Enter your email"
                                           required 
                                           autofocus>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           required>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none">
                                    Forgot password?
                                </a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 mb-4">
                                <i class="fas fa-sign-in-alt me-2"></i> Sign In
                            </button>

                            <div class="text-center">
                                <p class="text-muted mb-0">Don't have an account?</p>
                                @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
                                    Create Account
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .auth-page {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
    }
    .auth-card {
        border-radius: 16px;
    }
    .input-group-text {
        border-radius: 8px 0 0 8px;
    }
    .form-control {
        border-radius: 0 8px 8px 0;
    }
    .form-control:focus {
        border-color: #1E3A8A;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    .btn-primary {
        background-color: #1E3A8A;
        border-color: #1E3A8A;
        font-weight: 600;
    }
    .btn-primary:hover {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }
</style>
@endsection
