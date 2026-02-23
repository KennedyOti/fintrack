@extends('layouts.app')

@section('title', 'FinTrack - Financial Management for Freelancers')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="badge-dot"></span>
                        <span>100% Free for Freelancers</span>
                    </div>
                    <h1 class="hero-title">
                        Stop Worrying About <span class="highlight">Irregular Income</span><br>
                        Start <span class="highlight-green">Mastering Your Finances</span>
                    </h1>
                    <p class="hero-description">
                        As a freelancer, you know the struggle of unpredictable income. 
                        FinTrack helps you track every shilling, plan your expenses, 
                        and build financial security — no accounting degree required.
                    </p>
                    <div class="hero-buttons">
                        @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-th-large"></i> Go to Dashboard
                        </a>
                        @else
                        <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-rocket"></i> Start Free Forever
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        @endauth
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat-item">
                            <div class="hero-stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="hero-stat-content">
                                <span class="hero-stat-value">10,000+</span>
                                <span class="hero-stat-label">Freelancers</span>
                            </div>
                        </div>
                        <div class="hero-stat-item">
                            <div class="hero-stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="hero-stat-content">
                                <span class="hero-stat-value">4.9/5</span>
                                <span class="hero-stat-label">User Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&q=80" 
                         alt="FinTrack Dashboard - Financial Management" 
                         class="img-fluid hero-main-image">
                    
                    <!-- Floating Cards -->
                    <div class="floating-card floating-card-income">
                        <div class="floating-card-content">
                            <div class="floating-card-icon income">
                                <i class="fas fa-arrow-trend-up"></i>
                            </div>
                            <div class="floating-card-text">
                                <small>Monthly Income</small>
                                <strong class="positive">+KES 125,000</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="floating-card floating-card-savings">
                        <div class="floating-card-content">
                            <div class="floating-card-icon savings">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                            <div class="floating-card-text">
                                <small>Savings Goal</small>
                                <strong class="positive">65% Complete</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="floating-card floating-card-invoice">
                        <div class="floating-card-content">
                            <div class="floating-card-icon invoice">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="floating-card-text">
                                <small>Pending Invoices</small>
                                <strong>3 Active</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Problem & Solution Section -->
<section class="problem-solution-section" id="features">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="section-badge">
                    <i class="fas fa-lightbulb"></i>
                    Why FinTrack?
                </span>
                <h2 class="section-title">Freelancers Face Real Financial Challenges</h2>
                <p class="section-description">
                    We understand the unique struggles of irregular income. Here's how FinTrack transforms your financial journey.
                </p>
            </div>
        </div>
        
        <!-- Problems -->
        <div class="row g-4 mb-5">
            <div class="col-lg-12">
                <h3 class="mb-4 fw-bold" style="color: var(--ft-text-dark);">
                    <span style="color: #EF4444;">❌</span> The Problem
                </h3>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h4>Irregular Income</h4>
                    <p>No guaranteed monthly salary means you never know how much you'll earn next month, making budgeting impossible.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h4>Lost Track of Expenses</h4>
                    <p>Business expenses get mixed with personal spending, making it hard to know what you can deduct come tax time.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h4>Chasing Payments</h4>
                    <p>Forgetting to send invoices, losing track of who owes you money, and wasting time following up with clients.</p>
                </div>
            </div>
        </div>
        
        <!-- Solutions -->
        <div class="row g-4">
            <div class="col-lg-12">
                <h3 class="mb-4 fw-bold" style="color: var(--ft-text-dark);">
                    <span style="color: var(--ft-emerald);">✓</span> The FinTrack Solution
                </h3>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="solution-card">
                    <div class="solution-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Smart Income Tracking</h4>
                    <p>Track every payment from every client. See your income patterns and plan for lean months with confidence.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="solution-card">
                    <div class="solution-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h4>Complete Expense Management</h4>
                    <p>Categorize and track all business expenses. Know exactly where your money goes and maximize deductions.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="solution-card">
                    <div class="solution-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4>Automated Invoicing</h4>
                    <p>Create professional invoices in seconds, send reminders automatically, and get paid faster without the hassle.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="section-badge">
                    <i class="fas fa-tools"></i>
                    Powerful Features
                </span>
                <h2 class="section-title">Everything You Need to Run Your Freelance Business</h2>
                <p class="section-description">
                    From tracking income to generating invoices, FinTrack gives you all the tools to manage your business finances.
                </p>
            </div>
        </div>
        
        <!-- Features Grid -->
        <div class="row g-4">
            <!-- Income Tracking -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper income">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h4>Income Tracking</h4>
                    <p>Track all your income sources, link payments to clients and projects, and get detailed reports on your earnings.</p>
                </div>
            </div>
            
            <!-- Expense Management -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper expense">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h4>Expense Management</h4>
                    <p>Record expenses by category, track vendor payments, and monitor your spending patterns to save money.</p>
                </div>
            </div>
            
            <!-- Savings Goals -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper savings">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <h4>Savings Goals</h4>
                    <p>Create savings accounts for different goals - emergency fund, equipment, vacation. Track progress visually.</p>
                </div>
            </div>
            
            <!-- Invoice Generation -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper invoice">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h4>Invoice Generator</h4>
                    <p>Create beautiful, professional invoices in minutes. Send them directly to clients and track payment status.</p>
                </div>
            </div>
            
            <!-- Quote Generation -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper project">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h4>Quote Generator</h4>
                    <p>Create compelling quotes for potential clients. Convert quotes to invoices with one click when accepted.</p>
                </div>
            </div>
            
            <!-- Client Management -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper income">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Client Management</h4>
                    <p>Keep all client information in one place. Track projects, payment history, and communicate effectively.</p>
                </div>
            </div>
            
            <!-- Project Tracking -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper project">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h4>Project Tracking</h4>
                    <p>Track projects, milestones, budgets, and profitability. Know which projects are making you money.</p>
                </div>
            </div>
            
            <!-- Debt Tracking -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper expense">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h4>Debt Management</h4>
                    <p>Track money owed to you (receivables) and money you owe (payables). Never miss a payment again.</p>
                </div>
            </div>
            
            <!-- Financial Reports -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-wrapper savings">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h4>Financial Reports</h4>
                    <p>Get real-time insights with profit/loss statements, cash flow reports, and tax-ready financial summaries.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works-section" id="how-it-works">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="section-badge">
                    <i class="fas fa-route"></i>
                    Simple Process
                </span>
                <h2 class="section-title">How FinTrack Works</h2>
                <p class="section-description">
                    Get started in minutes. No accounting knowledge required.
                </p>
            </div>
        </div>
        
        <!-- Steps -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-connector"></div>
                    <h4>Create Your Account</h4>
                    <p>Sign up for free in seconds. Add your business details and you're ready to start tracking.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-connector"></div>
                    <h4>Add Your Income & Expenses</h4>
                    <p>Record your payments, track expenses, and organize them by category for clarity.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h4>Generate Invoices & Quotes</h4>
                    <p>Create professional invoices and quotes. Send to clients and get paid faster.</p>
                </div>
            </div>
        </div>
        
        <!-- CTA Box -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-10">
                <div class="cta-box">
                    <div class="cta-content">
                        <h2>Ready to Take Control of Your Finances?</h2>
                        <p>Join thousands of freelancers who use FinTrack to manage their business finances effectively.</p>
                        <div class="cta-buttons">
                            @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-th-large"></i> Go to Dashboard
                            </a>
                            @else
                            <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-rocket"></i> Start Free Forever
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-star"></i> Learn More
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-value">10K+</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-value">KES 500M+</div>
                    <div class="stat-label">Income Tracked</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-value">50K+</div>
                    <div class="stat-label">Invoices Created</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-value">99.9%</div>
                    <div class="stat-label">Uptime</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section --->


<!-- Final CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="cta-box">
                    <div class="cta-content">
                        <h2>Start Managing Your Freelance Finances Today</h2>
                        <p>It's 100% free. No credit card required. No hidden fees. Forever.</p>
                        <div class="cta-buttons">
                            @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-th-large"></i> Go to Dashboard
                            </a>
                            @else
                            <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus"></i> Create Free Account
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                            @endauth
                        </div>
                        <p style="margin-top: 20px; font-size: 14px; color: rgba(255,255,255,0.7);">
                            <i class="fas fa-shield-alt"></i> Your data is secure and private
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    /* Additional page-specific styles */
    .problem-card,
    .solution-card,
    .feature-card,
    .step-card {
        opacity: 0;
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .problem-card:nth-child(1),
    .solution-card:nth-child(1),
    .feature-card:nth-child(1),
    .step-card:nth-child(1) { animation-delay: 0.1s; }
    
    .problem-card:nth-child(2),
    .solution-card:nth-child(2),
    .feature-card:nth-child(2),
    .step-card:nth-child(2) { animation-delay: 0.2s; }
    
    .problem-card:nth-child(3),
    .solution-card:nth-child(3),
    .feature-card:nth-child(3),
    .step-card:nth-child(3) { animation-delay: 0.3s; }
    
    .feature-card:nth-child(4) { animation-delay: 0.4s; }
    .feature-card:nth-child(5) { animation-delay: 0.5s; }
    .feature-card:nth-child(6) { animation-delay: 0.6s; }
    .feature-card:nth-child(7) { animation-delay: 0.7s; }
    .feature-card:nth-child(8) { animation-delay: 0.8s; }
    .feature-card:nth-child(9) { animation-delay: 0.9s; }
</style>
@endsection
