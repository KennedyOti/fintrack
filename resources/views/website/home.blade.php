@extends('layouts.app')

@section('title', 'FinTrack - Professional Financial Management')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <span class="badge bg-primary-subtle text-primary mb-3 px-4 py-2 rounded-pill">
                    <i class="fas fa-rocket me-1"></i> Launch Your Financial Success
                </span>
                <h1 class="display-3 fw-bold mb-4">
                    Master Your <span class="text-primary">Finances</span> & Projects
                </h1>
                <p class="lead text-muted mb-4">
                    FinTrack helps freelancers and small agencies track income, expenses, projects, 
                    invoices, and more - all in one powerful dashboard.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5 shadow-sm">
                        <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 shadow-sm">
                        <i class="fas fa-user-plus me-2"></i> Get Started Free
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-5">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </a>
                    @endauth
                </div>
                <div class="mt-5">
                    <p class="text-muted mb-2">Trusted by freelancers worldwide</p>
                    <div class="d-flex gap-3">
                        <span class="text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                        <span class="text-muted">4.9/5 rating</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-container">
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&q=80" 
                         alt="Financial Dashboard" 
                         class="img-fluid rounded-4 shadow-lg">
                    <div class="floating-card card-1">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle rounded-circle p-2 me-2">
                                <i class="fas fa-arrow-up text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Income</small>
                                <div class="fw-bold text-success">+KES 125,000</div>
                            </div>
                        </div>
                    </div>
                    <div class="floating-card card-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle rounded-circle p-2 me-2">
                                <i class="fas fa-chart-line text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Profit</small>
                                <div class="fw-bold text-primary">+45%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Everything You Need to Manage Your Business</h2>
            <p class="text-muted lead">Powerful features designed specifically for freelancers and small agencies</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-primary-subtle text-primary rounded-3 mb-3">
                            <i class="fas fa-money-bill-wave fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold">Income Tracking</h5>
                        <p class="card-text text-muted">Track all your income sources, link payments to clients and projects, and get detailed reports.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-danger-subtle text-danger rounded-3 mb-3">
                            <i class="fas fa-receipt fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold">Expense Management</h5>
                        <p class="card-text text-muted">Record expenses by category, track vendor payments, and monitor your spending patterns.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-success-subtle text-success rounded-3 mb-3">
                            <i class="fas fa-piggy-bank fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold">Savings Goals</h5>
                        <p class="card-text text-muted">Create savings accounts for different goals, track progress, and grow your emergency fund.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-warning-subtle text-warning rounded-3 mb-3">
                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold">Invoicing</h5>
                        <p class="card-text text-muted">Create professional invoices, track payment status, and send reminders automatically.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-info-subtle text-info rounded-3 mb-3">
                            <i class="fas fa-project-diagram fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold">Project Management</h5>
                        <p class="card-text text-muted">Track projects, milestones, budgets, and profitability all in one place.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body p-4">
                        <div class="feature-icon bg-secondary-subtle text-secondary rounded-3 mb-3">
                            <i class="fas fa-chart-pie fa-lg"></i>
                        </div>
                        <h5 class="card-title fw-bold">Financial Reports</h5>
                        <p class="card-text text-muted">Get real-time insights with profit/loss statements, cash flow, and tax reports.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80" 
                     alt="Analytics" 
                     class="img-fluid rounded-4 shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Why Choose FinTrack?</h2>
                <div class="d-flex gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary rounded-circle p-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-check text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="fw-bold">Real-Time Financial Position</h5>
                        <p class="text-muted mb-0">See your net worth at a glance with our comprehensive financial dashboard.</p>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary rounded-circle p-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-check text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="fw-bold">Debt & Receivables Tracking</h5>
                        <p class="text-muted mb-0">Never miss a payment with automatic overdue alerts and aging reports.</p>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="bg-primary rounded-circle p-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-check text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="fw-bold">Client Management</h5>
                        <p class="text-muted mb-0">Track client details, project history, and outstanding balances.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary">
    <div class="container text-center">
        <h2 class="fw-bold text-white mb-3">Ready to Take Control of Your Finances?</h2>
        <p class="text-white-50 mb-4">Join thousands of freelancers who trust FinTrack to manage their business</p>
        @auth
        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg px-5">
            <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
        </a>
        @else
        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
            <i class="fas fa-user-plus me-2"></i> Start Free Trial
        </a>
        @endauth
    </div>
</section>

<!-- Stats Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="fw-bold text-primary">10K+</h3>
                    <p class="text-muted mb-0">Active Users</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="fw-bold text-primary">$50M+</h3>
                    <p class="text-muted mb-0">Invoices Created</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="fw-bold text-primary">99.9%</h3>
                    <p class="text-muted mb-0">Uptime</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <h3 class="fw-bold text-primary">24/7</h3>
                    <p class="text-muted mb-0">Support</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .min-vh-75 {
        min-height: 75vh;
    }
    .hero-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 60px 0;
    }
    .hero-image-container {
        position: relative;
    }
    .floating-card {
        position: absolute;
        background: white;
        padding: 12px 16px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .card-1 {
        top: 20%;
        left: -20px;
        animation: float 3s ease-in-out infinite;
    }
    .card-2 {
        bottom: 20%;
        right: -20px;
        animation: float 3s ease-in-out infinite 1.5s;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .feature-icon {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .feature-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
