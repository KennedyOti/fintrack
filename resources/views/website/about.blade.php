@extends('layouts.app')

@section('title', 'About FinTrack — Built for Freelancers')
@section('meta-description', 'Learn about FinTrack — the financial management platform designed specifically for freelancers and independent professionals. Our mission, values, and what makes us different.')

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
                    background:radial-gradient(circle,rgba(34,211,238,0.05) 0%,transparent 70%);
                    right:-60px; top:-100px; pointer-events:none;"></div>
    </div>

    <div class="container text-center" style="max-width:760px; position:relative; z-index:2;">
        <span class="chip mb-4"><i class="fa-solid fa-circle-info"></i> About FinTrack</span>
        <h1 style="font-size:clamp(32px,5vw,58px); font-weight:800; letter-spacing:-0.03em; margin-bottom:22px; line-height:1.08;">
            Financial Clarity for the<br>
            <span class="gradient-text">Independent Professional</span>
        </h1>
        <p style="font-size:18px; color:var(--text-secondary); line-height:1.8; max-width:600px; margin:0 auto 36px;">
            FinTrack was built with one clear purpose: to give freelancers and self-employed
            professionals the same financial tools and visibility that growing businesses
            have — without the complexity or the cost.
        </p>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Open Dashboard <i class="fa-solid fa-arrow-right"></i></a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Get Started Free <i class="fa-solid fa-arrow-right"></i></a>
                <a href="{{ route('how-it-works') }}" class="btn btn-outline btn-lg">How It Works</a>
            @endauth
        </div>
    </div>
</section>

{{-- ── Mission ── --}}
<section style="padding:80px 0; background:var(--bg-surface); border-top:1px solid var(--border-dim);">
    <div class="container" style="max-width:1060px;">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <span class="chip mb-3"><i class="fa-solid fa-bullseye-arrow"></i> Our Mission</span>
                <h2 style="font-size:clamp(26px,3.5vw,40px); font-weight:700; margin-bottom:18px; letter-spacing:-0.025em; line-height:1.2;">
                    Empowering Freelancers with<br>
                    <span class="gradient-text">Real Financial Intelligence</span>
                </h2>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.8; margin-bottom:20px;">
                    The freelance economy is growing — but the financial tools available to
                    independent workers have not kept pace. Spreadsheets are error-prone.
                    Generic accounting software is overkill. Most freelancers end up managing
                    their finances reactively rather than proactively.
                </p>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.8; margin-bottom:0;">
                    FinTrack changes that. We believe every freelancer deserves a clear,
                    real-time view of their financial health — income, expenses, invoices,
                    savings goals, and client activity — all in one place.
                </p>
            </div>

            <div class="col-lg-6">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    @php
                    $pillars = [
                        ['fa-shield-halved',      'var(--cyan)',    'Transparency',  'See every shilling — where it came from, where it went, and what\'s still outstanding.'],
                        ['fa-bolt',               'var(--emerald)', 'Efficiency',    'Save hours every month on invoicing, expense tracking, and financial reporting.'],
                        ['fa-chart-line-up',      'var(--violet)',  'Growth',        'Make informed decisions about pricing, clients, and savings based on real data.'],
                        ['fa-lock',               'var(--amber)',   'Security',      'Your financial data is private, secure, and accessible only to you.'],
                    ];
                    @endphp
                    @foreach($pillars as $p)
                    <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-lg); padding:24px 20px;">
                        <i class="fa-solid {{ $p[0] }}" style="font-size:22px; color:{{ $p[1] }}; margin-bottom:12px; display:block;"></i>
                        <h5 style="font-size:14px; font-weight:700; margin-bottom:8px; color:var(--text-primary);">{{ $p[2] }}</h5>
                        <p style="font-size:13px; color:var(--text-secondary); line-height:1.6; margin:0;">{{ $p[3] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── Who It's For ── --}}
<section style="padding:96px 0;">
    <div class="container" style="max-width:1060px;">

        <div class="text-center mb-5">
            <span class="chip mb-3"><i class="fa-solid fa-user-check"></i> Who It's For</span>
            <h2 style="font-size:clamp(26px,3.5vw,40px); font-weight:700; letter-spacing:-0.025em; margin-bottom:14px;">
                Built for Independent Professionals
            </h2>
            <p style="font-size:16.5px; color:var(--text-secondary); max-width:540px; margin:0 auto; line-height:1.75;">
                If you work for yourself, FinTrack was designed for you.
            </p>
        </div>

        <div class="row g-4">
            @php
            $personas = [
                ['fa-code',             'var(--cyan)',     'Freelance Developers',  'Bill clients by project or hourly, track software subscriptions as expenses, and manage multiple concurrent project budgets.'],
                ['fa-pen-nib',          'var(--violet)',   'Designers & Creatives',  'Send polished quotes, convert them to invoices, manage retainer clients, and track the tools and software you rely on.'],
                ['fa-megaphone',        'var(--amber)',    'Marketers & Consultants','Track consulting income by client, manage ad spend as categorised expenses, and monitor which clients drive the most revenue.'],
                ['fa-camera',           'var(--rose)',     'Photographers & Videographers', 'Invoice for shoot packages, track equipment purchases, manage assistant costs, and see net income per project.'],
                ['fa-chalkboard-user',  'var(--emerald)',  'Coaches & Educators',    'Log session income, manage course revenue, track material expenses, and keep savings goals for slow seasons.'],
                ['fa-briefcase',        'var(--text-secondary)', 'Any Independent Professional', 'If you issue invoices, earn variable income, or manage your own business finances — FinTrack works for you.'],
            ];
            @endphp
            @foreach($personas as $p)
            <div class="col-lg-4 col-md-6">
                <div class="why-card">
                    <div class="why-icon" style="background:rgba(99,142,210,0.1); color:{{ $p[1] }}; width:46px; height:46px; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; font-size:19px; margin-bottom:16px;">
                        <i class="fa-solid {{ $p[0] }}"></i>
                    </div>
                    <h4>{{ $p[2] }}</h4>
                    <p>{{ $p[3] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ── Features at a Glance ── --}}
<section style="padding:96px 0; background:var(--bg-surface);">
    <div class="container" style="max-width:1060px;">

        <div class="text-center mb-5">
            <span class="chip mb-3"><i class="fa-solid fa-sparkles"></i> What You Get</span>
            <h2 style="font-size:clamp(26px,3.5vw,40px); font-weight:700; letter-spacing:-0.025em; margin-bottom:14px;">
                A Complete Financial Toolkit
            </h2>
        </div>

        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:16px; max-width:780px; margin:0 auto;">
            @php
            $features = [
                ['fa-chart-line',         'Income tracking with client & project tagging'],
                ['fa-file-invoice-dollar','PDF invoice generation & payment tracking'],
                ['fa-receipt',            'Expense management with custom categories'],
                ['fa-file-signature',     'Quote builder with client-side acceptance'],
                ['fa-address-book',       'Client CRM with full financial history'],
                ['fa-diagram-project',    'Project management with budget tracking'],
                ['fa-piggy-bank',         'Savings accounts with goal setting'],
                ['fa-hand-holding-dollar','Debt receivable & payable tracking'],
                ['fa-rotate',             'Recurring transaction automation'],
                ['fa-chart-pie',          'Financial reports & overview dashboard'],
                ['fa-earth-africa',       '36 currencies supported'],
                ['fa-file-export',        'Data export for accounting'],
            ];
            @endphp
            @foreach($features as $f)
            <div style="display:flex; align-items:flex-start; gap:12px; padding:14px 16px; background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-md);">
                <i class="fa-solid {{ $f[0] }}" style="color:var(--cyan); width:16px; margin-top:2px; flex-shrink:0;"></i>
                <span style="font-size:13.5px; color:var(--text-secondary); line-height:1.5;">{{ $f[1] }}</span>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ── CTA ── --}}
<section style="padding:96px 0;">
    <div class="container" style="max-width:840px;">
        <div class="cta-box">
            <h2>
                Ready to Gain Financial<br>
                <span class="gradient-text">Clarity and Control?</span>
            </h2>
            <p>
                Join FinTrack and stop guessing about your financial health.
                Get a complete, real-time view of everything that matters to your business.
            </p>
            <div class="cta-btns">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-gauge-high"></i> Open Your Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        Get Started Free <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('how-it-works') }}" class="btn btn-outline btn-lg">See How It Works</a>
                @endauth
            </div>
        </div>
    </div>
</section>

@endsection
