@extends('layouts.app')

@section('title', 'How FinTrack Works — Features & Walkthrough')
@section('meta-description', 'Discover how FinTrack works. A complete walkthrough of every feature — income tracking, invoicing, expense management, savings goals, client management, and financial reports.')

@section('content')

{{-- ── Page header ── --}}
<section style="position:relative; overflow:hidden; padding:140px 0 80px;">
    <div style="position:absolute; inset:0; pointer-events:none;">
        <div style="position:absolute; inset:0;
                    background-image:radial-gradient(rgba(34,211,238,0.10) 1px, transparent 1px);
                    background-size:38px 38px;
                    mask-image:radial-gradient(ellipse 60% 60% at 50% 0%, black 20%, transparent 100%);
                    -webkit-mask-image:radial-gradient(ellipse 60% 60% at 50% 0%, black 20%, transparent 100%);">
        </div>
        <div style="position:absolute; width:500px; height:500px; border-radius:50%;
                    background:radial-gradient(circle,rgba(139,92,246,0.06) 0%,transparent 70%);
                    left:-60px; top:-80px;"></div>
    </div>

    <div class="container text-center" style="max-width:720px; position:relative; z-index:2;">
        <span class="chip mb-4"><i class="fa-solid fa-circle-question"></i> How It Works</span>
        <h1 style="font-size:clamp(32px,5vw,56px); font-weight:800; letter-spacing:-0.03em; margin-bottom:22px; line-height:1.1;">
            Every Feature, Explained
        </h1>
        <p style="font-size:18px; color:var(--text-secondary); line-height:1.8; max-width:580px; margin:0 auto 36px;">
            FinTrack is designed to work the way a freelancer actually thinks about money.
            Here's a detailed look at every feature and how it fits into your workflow.
        </p>
        @guest
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Get Started Free <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        @endguest
    </div>
</section>

{{-- ── Step-by-step features ── --}}

@php
$sections = [
    [
        'chip'  => ['fa-gauge-high', 'Dashboard'],
        'title' => 'Your Financial Command Center',
        'desc'  => 'The Dashboard is the first thing you see when you log in. It gives you an immediate, real-time overview of your financial health — no digging required.',
        'color' => 'var(--cyan)',
        'points' => [
            'Total income received in the current period',
            'Total expenses logged, broken down by category',
            'Savings account balances and goal progress',
            'Outstanding receivables — money still owed to you',
            'Net financial position (income − expenses + savings)',
            'Monthly income vs expense comparison chart',
            'Expense distribution by category (pie chart)',
            'Onboarding checklist to help you set up key items first',
        ],
        'bg' => false,
    ],
    [
        'chip'  => ['fa-chart-line', 'Income'],
        'title' => 'Income Tracking — Know Every Shilling You Earn',
        'desc'  => 'Log every payment you receive with rich context. Unlike a bank statement, FinTrack lets you understand not just what came in, but where it came from and why.',
        'color' => 'var(--emerald)',
        'points' => [
            'Record income with amount, date, client, and project',
            'Tag each entry with a payment method (bank transfer, mobile money, cash, etc.)',
            'Add a reference number for reconciliation',
            'Link income directly to an invoice when payment is received',
            'Browse your full income history with filters by date and category',
            'Income automatically flows to the dashboard totals',
        ],
        'bg' => true,
    ],
    [
        'chip'  => ['fa-file-invoice-dollar', 'Invoices'],
        'title' => 'Invoice Management — Get Paid Professionally',
        'desc'  => 'Create polished, professional invoices and manage their lifecycle from draft to fully paid. Share them with clients digitally or download them as PDFs.',
        'color' => 'var(--cyan)',
        'points' => [
            'Auto-generated invoice numbers for easy reference',
            'Add line items, subtotal, tax percentage, and discounts',
            'Download invoices as branded PDFs to attach to emails',
            'Share invoices via a unique, public link — no login required for the client',
            'Track payment status: draft, sent, partial, paid, overdue',
            'Record partial payments and track remaining balance',
            'Payments automatically create income records and update receivables',
        ],
        'bg' => false,
    ],
    [
        'chip'  => ['fa-file-signature', 'Quotes'],
        'title' => 'Quote Builder — Win Business Faster',
        'desc'  => 'Send professional quotes to prospects before the work begins. Streamline the process from proposal to invoice with a single workflow.',
        'color' => 'var(--teal)',
        'points' => [
            'Build itemised quotes with the same line-item editor as invoices',
            'Set a validity period so quotes expire automatically if not acted on',
            'Share quotes via a secure public link — clients can accept or reject online',
            'Convert an accepted quote to an invoice in one click — no re-entering data',
            'Track quote status: draft, sent, accepted, rejected, expired, converted',
        ],
        'bg' => true,
    ],
    [
        'chip'  => ['fa-receipt', 'Expenses'],
        'title' => 'Expense Tracking — See Where Your Money Goes',
        'desc'  => 'Categorise every business expense so you always know what you\'re spending and on what. Never lose a receipt again.',
        'color' => 'var(--rose)',
        'points' => [
            'Log expenses with amount, date, category, and description',
            'Create custom expense categories with your own colours',
            'Attach receipt images directly to expense records',
            'Link expenses to specific income sources when relevant',
            'Filter expenses by category and date range',
            'Expense totals flow automatically to the dashboard and reports',
        ],
        'bg' => false,
    ],
    [
        'chip'  => ['fa-users', 'Clients'],
        'title' => 'Client Management — Know Your Business Relationships',
        'desc'  => 'Every client you work with deserves a complete record. FinTrack turns your client list into a mini-CRM with full financial history.',
        'color' => 'var(--violet)',
        'points' => [
            'Store contact info: name, company, email, phone, address',
            'Add tax numbers for clients that require tax-compliant invoices',
            'See all invoices, quotes, and projects linked to each client',
            'Track total invoiced amount and outstanding balance per client',
            'Mark clients as active or inactive',
        ],
        'bg' => true,
    ],
    [
        'chip'  => ['fa-diagram-project', 'Projects'],
        'title' => 'Project Tracking — Manage the Work Behind the Revenue',
        'desc'  => 'Track the projects that generate your income. Set budgets, monitor progress, and see financials linked to each piece of work.',
        'color' => 'var(--amber)',
        'points' => [
            'Create projects with a title, start date, deadline, and budget',
            'Update project status: planned, in progress, or completed',
            'Set progress percentage to track how far along each project is',
            'Link income and invoices to the project they belong to',
            'See all projects per client for a complete picture of your engagement',
        ],
        'bg' => false,
    ],
    [
        'chip'  => ['fa-piggy-bank', 'Savings'],
        'title' => 'Savings Goals — Build Financial Resilience',
        'desc'  => 'Freelancers face income variability. Savings goals help you build a financial buffer and plan for the future — even during slow months.',
        'color' => 'var(--emerald)',
        'points' => [
            'Create multiple savings accounts, each with its own name and purpose',
            'Set a target amount per account to track progress visually',
            'Deposit money to a savings account, linked to an income record if desired',
            'Withdraw from savings when needed, with a reason and transaction record',
            'Track current balance, target amount, and remaining gap for each account',
            'Full transaction history per savings account',
        ],
        'bg' => true,
    ],
    [
        'chip'  => ['fa-hand-holding-dollar', 'Debts'],
        'title' => 'Debt Management — Stay on Top of What\'s Owed',
        'desc'  => 'Track money owed to you by clients and money you owe to vendors, so nothing slips through the cracks.',
        'color' => 'var(--amber)',
        'points' => [
            'Receivables: track debts owed to you, linked to clients and invoices',
            'Payables: track debts you owe to vendors or suppliers',
            'Monitor original amount, amount paid, and remaining balance',
            'Set due dates and track whether debts are pending, partial, paid, or overdue',
            'Receivables are created automatically when you record an invoice payment',
        ],
        'bg' => false,
    ],
    [
        'chip'  => ['fa-rotate', 'Recurring'],
        'title' => 'Recurring Transactions — Automate What Repeats',
        'desc'  => 'Retainer income, monthly software subscriptions, regular expenses — if it happens on a schedule, let FinTrack track it automatically.',
        'color' => 'var(--cyan)',
        'points' => [
            'Set up recurring income or expense rules with a frequency: daily, weekly, monthly, or yearly',
            'The system auto-generates income or expense records on the schedule',
            'Toggle rules active or inactive at any time',
            'Generate records immediately with the "Generate Now" action',
            'Recurring transactions appear in your income and expense history like any manual entry',
        ],
        'bg' => true,
    ],
    [
        'chip'  => ['fa-chart-pie', 'Reports'],
        'title' => 'Financial Overview — Understand Your Business Health',
        'desc'  => 'The Financial Overview gives you a filterable, chart-driven view of your finances — so you can spot trends, understand patterns, and plan ahead.',
        'color' => 'var(--violet)',
        'points' => [
            'Filter all data by year and month for period-specific analysis',
            'Income vs expense bar chart over the selected period',
            'Category breakdown for income and expenses',
            'Top clients by total invoiced amount',
            'Outstanding invoices list with payment status',
            'Net financial position summary',
        ],
        'bg' => false,
    ],
];
@endphp

@foreach($sections as $i => $sec)
<section style="padding:80px 0; {{ $sec['bg'] ? 'background:var(--bg-surface);' : '' }} {{ $i === 0 ? 'border-top:1px solid var(--border-dim);' : '' }}">
    <div class="container" style="max-width:1060px;">
        <div style="display:flex; gap:6px; align-items:center; margin-bottom:20px;">
            <span class="chip"><i class="fa-solid {{ $sec['chip'][0] }}"></i> {{ $sec['chip'][1] }}</span>
        </div>
        <div class="row align-items-start g-5">
            <div class="col-lg-5">
                <h2 style="font-size:clamp(22px,2.8vw,36px); font-weight:700; letter-spacing:-0.025em; margin-bottom:16px; line-height:1.2;">
                    {{ $sec['title'] }}
                </h2>
                <p style="font-size:15.5px; color:var(--text-secondary); line-height:1.78; margin:0;">
                    {{ $sec['desc'] }}
                </p>
            </div>
            <div class="col-lg-7">
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @foreach($sec['points'] as $j => $point)
                    <div style="display:flex; gap:12px; align-items:flex-start;
                                padding:14px 16px;
                                background:var(--bg-card);
                                border:1px solid var(--border-dim);
                                border-radius:var(--r-md);
                                font-size:14px;
                                color:var(--text-secondary);
                                line-height:1.55;">
                        <span style="width:22px; height:22px; border-radius:50%;
                                     background:rgba(34,211,238,0.1);
                                     border:1px solid rgba(34,211,238,0.22);
                                     display:flex; align-items:center; justify-content:center;
                                     font-size:10px; font-weight:800;
                                     color:{{ $sec['color'] }};
                                     flex-shrink:0; margin-top:1px;
                                     font-family:'Space Grotesk', sans-serif;">
                            {{ $j + 1 }}
                        </span>
                        {{ $point }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endforeach

{{-- ── Multi-currency & settings note ── --}}
<section style="padding:80px 0; background:var(--bg-surface);">
    <div class="container" style="max-width:1060px;">
        <div class="row g-4">
            <div class="col-lg-6">
                <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-lg); padding:32px;">
                    <i class="fa-solid fa-earth-africa" style="font-size:24px; color:var(--cyan); margin-bottom:16px; display:block;"></i>
                    <h3 style="font-size:20px; font-weight:700; margin-bottom:10px;">36 Currencies, One Platform</h3>
                    <p style="font-size:14px; color:var(--text-secondary); line-height:1.7; margin:0;">
                        Set your preferred currency in Settings and FinTrack will display all amounts, invoices,
                        and reports in your chosen currency. Supported currencies include USD, EUR, GBP, KES, NGN,
                        ZAR, GHS, and 29 others — covering freelancers across Africa, Europe, and beyond.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-lg); padding:32px;">
                    <i class="fa-solid fa-user-gear" style="font-size:24px; color:var(--violet); margin-bottom:16px; display:block;"></i>
                    <h3 style="font-size:20px; font-weight:700; margin-bottom:10px;">Personalised Settings</h3>
                    <p style="font-size:14px; color:var(--text-secondary); line-height:1.7; margin:0;">
                        Configure your profile, business name, logo, and tax number for branded PDF documents.
                        Control your notification preferences, dark mode setting, and timezone. Everything
                        in FinTrack is scoped to your account — your data stays private.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Final CTA ── --}}
<section style="padding:96px 0;">
    <div class="container" style="max-width:840px;">
        <div class="cta-box">
            <h2>
                Everything You Need.<br>
                <span class="gradient-text">Nothing You Don't.</span>
            </h2>
            <p>
                FinTrack is purpose-built for freelancers — not adapted from a
                tool designed for accountants. Start using it today and experience
                what financial clarity actually feels like.
            </p>
            <div class="cta-btns">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-gauge-high"></i> Open Your Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        Get Started — It's Free <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline btn-lg">About FinTrack</a>
                @endauth
            </div>
        </div>
    </div>
</section>

@endsection
