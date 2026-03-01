@extends('layouts.app')

@section('title', 'FinTrack — The Financial Command Center for Freelancers')
@section('meta-description', 'FinTrack gives freelancers a complete financial command center. Invoice clients, track income & expenses, set savings goals, and make smarter financial decisions.')

@section('content')

{{-- ════════════════════════════════════════════════════════════
     HERO
     ════════════════════════════════════════════════════════════ --}}
<section class="hero-section">

    <div class="hero-bg">
        <div class="hero-grid"></div>
        <div class="hero-orb-1"></div>
        <div class="hero-orb-2"></div>
    </div>

    <div class="container" style="max-width:1160px; position:relative; z-index:2;">
        <div class="row align-items-center g-5">

            {{-- Text --}}
            <div class="col-lg-6 hero-content">

                <div class="hero-badge anim-fade-up">
                    <span class="badge-dot"></span>
                    Financial Management Platform
                </div>

                <h1 class="hero-title anim-fade-up delay-1">
                    The Smarter Way to Manage Your
                    <span class="gradient-text">Freelance Finances</span>
                </h1>

                <p class="hero-desc anim-fade-up delay-2">
                    FinTrack gives you a complete picture of your financial life — income,
                    invoices, expenses, savings, and clients — all from one intelligent dashboard
                    built specifically for freelancers and independent professionals.
                </p>

                <div class="hero-ctas anim-fade-up delay-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-gauge-high"></i> Open Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            Start for Free <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="{{ route('how-it-works') }}" class="btn btn-outline btn-lg">
                            See How It Works
                        </a>
                    @endauth
                </div>

                <div class="hero-trust anim-fade-up delay-4">
                    <span class="trust-pill"><i class="fa-solid fa-check"></i> No credit card required</span>
                    <span class="trust-pill"><i class="fa-solid fa-check"></i> Setup in minutes</span>
                    <span class="trust-pill"><i class="fa-solid fa-check"></i> 36 currencies supported</span>
                </div>
            </div>

            {{-- Screenshot + floating cards --}}
            <div class="col-lg-6">
                <div class="hero-visual anim-fade-up delay-2">
                    <div class="hero-shot-wrap">
                        <img src="{{ asset('assets/images/dashboard1.png') }}"
                             alt="FinTrack Dashboard — financial overview"
                             loading="eager">
                    </div>

                    <div class="hero-float hero-float-a">
                        <div class="float-label">Invoice Status</div>
                        <div class="float-val">
                            <i class="fa-solid fa-file-invoice-dollar" style="color:var(--cyan)"></i>
                            Invoice Sent
                        </div>
                        <span class="float-tag blue">
                            <i class="fa-solid fa-share-nodes"></i> Shared with client
                        </span>
                    </div>

                    <div class="hero-float hero-float-b">
                        <div class="float-label">Expense Logged</div>
                        <div class="float-val">
                            <i class="fa-solid fa-receipt" style="color:var(--rose)"></i>
                            Office Supplies
                        </div>
                        <span class="float-tag green">
                            <i class="fa-solid fa-check"></i> Categorised
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     MARQUEE — Feature Strip
     ════════════════════════════════════════════════════════════ --}}
<div class="marquee-strip">
    <div class="marquee-track">
        @php
            $features = [
                ['fa-chart-line',         'Income Tracking'],
                ['fa-file-invoice-dollar','Invoice Management'],
                ['fa-receipt',            'Expense Tracking'],
                ['fa-users',              'Client Management'],
                ['fa-diagram-project',    'Project Tracking'],
                ['fa-piggy-bank',         'Savings Goals'],
                ['fa-hand-holding-dollar','Debt Management'],
                ['fa-chart-pie',          'Financial Reports'],
                ['fa-earth-africa',       'Multi-Currency'],
                ['fa-rotate',             'Recurring Transactions'],
                ['fa-file-export',        'Data Export'],
                ['fa-file-pdf',           'PDF Generation'],
            ];
        @endphp
        @foreach(array_merge($features, $features) as $f)
            <span class="marquee-item">
                <i class="fa-solid {{ $f[0] }}"></i> {{ $f[1] }}
            </span>
        @endforeach
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     WHY FINTRACK — Pain Points Solved
     ════════════════════════════════════════════════════════════ --}}
<section class="section">
    <div class="container" style="max-width:1160px;">

        <div class="row mb-5">
            <div class="col-lg-7">
                <span class="chip mb-3"><i class="fa-solid fa-lightbulb"></i> Why FinTrack</span>
                <h2 class="section-title">
                    Built for the Realities of<br>
                    <span class="gradient-text">Freelance Finance</span>
                </h2>
                <p class="section-lead">
                    Freelancing comes with financial complexity that traditional tools
                    weren't designed for. FinTrack addresses every challenge head-on.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="why-card">
                    <div class="why-icon wi-rose"><i class="fa-solid fa-chart-mixed"></i></div>
                    <h4>Irregular Income, Managed</h4>
                    <p>Variable monthly earnings make planning feel impossible. FinTrack lets you log every income source by client, project, and payment method — so you always know exactly what came in and when.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="why-card">
                    <div class="why-icon wi-amber"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <h4>Never Chase Payments Again</h4>
                    <p>Following up on unpaid invoices is stressful. Create professional PDF invoices, track payment status, and manage all outstanding receivables from a single, organised view.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="why-card">
                    <div class="why-icon wi-violet"><i class="fa-solid fa-eye"></i></div>
                    <h4>Total Expense Visibility</h4>
                    <p>Small expenses accumulate silently. FinTrack lets you categorise every expense with custom categories, attach receipts, and see exactly where your money goes each month.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="why-card">
                    <div class="why-icon wi-cyan"><i class="fa-solid fa-users-between-lines"></i></div>
                    <h4>Clients &amp; Projects in One Place</h4>
                    <p>Managing multiple clients across different projects and payment terms is a juggling act. FinTrack gives each client a complete financial profile — projects, invoices, quotes, and payment history.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="why-card">
                    <div class="why-icon wi-emerald"><i class="fa-solid fa-piggy-bank"></i></div>
                    <h4>Build Financial Discipline</h4>
                    <p>Without a salary, saving consistently requires intention. Set up dedicated savings accounts with goals and target amounts, and track progress as you deposit and withdraw over time.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="why-card">
                    <div class="why-icon wi-teal"><i class="fa-solid fa-chart-column"></i></div>
                    <h4>Data-Driven Decisions</h4>
                    <p>Without reports, financial decisions are guesswork. FinTrack's financial overview shows income vs expense trends, top-earning clients, category breakdowns, and net position — all filterable by month and year.</p>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     FEATURES GRID
     ════════════════════════════════════════════════════════════ --}}
<section class="section" style="background:var(--bg-surface); padding:100px 0;">
    <div class="container" style="max-width:1160px;">

        <div class="row mb-5">
            <div class="col-lg-8">
                <span class="chip mb-3"><i class="fa-solid fa-grid-2"></i> Features</span>
                <h2 class="section-title">
                    Everything You Need to<br>
                    <span class="gradient-text">Run Your Finances</span>
                </h2>
                <p class="section-lead">
                    From landing a client to getting paid — and everything in between —
                    FinTrack has a purpose-built tool for it.
                </p>
            </div>
        </div>

        <div class="ft-grid">

            <div class="ft-cell fc-emerald">
                <div class="ft-num">01</div>
                <i class="fa-solid fa-chart-line ft-icon"></i>
                <h4>Income Tracking</h4>
                <p>Log every payment received, tagged by client, project, payment method, and reference. Build a complete, searchable income history over time.</p>
            </div>

            <div class="ft-cell fc-cyan">
                <div class="ft-num">02</div>
                <i class="fa-solid fa-file-invoice-dollar ft-icon"></i>
                <h4>Invoice Management</h4>
                <p>Generate professional PDF invoices with tax and discount support. Track payment status, record partial payments, and share invoices via a unique client link.</p>
            </div>

            <div class="ft-cell fc-rose">
                <div class="ft-num">03</div>
                <i class="fa-solid fa-receipt ft-icon"></i>
                <h4>Expense Tracking</h4>
                <p>Record and categorise every business expense. Attach receipts, link expenses to income sources, and get a clear view of your spending patterns by category.</p>
            </div>

            <div class="ft-cell fc-teal">
                <div class="ft-num">04</div>
                <i class="fa-solid fa-file-signature ft-icon"></i>
                <h4>Quote Builder</h4>
                <p>Send professional quotes to prospects. Clients can accept or reject online. Accepted quotes convert to invoices instantly — no re-entering data needed.</p>
            </div>

            <div class="ft-cell fc-violet">
                <div class="ft-num">05</div>
                <i class="fa-solid fa-address-book ft-icon"></i>
                <h4>Client Management</h4>
                <p>Maintain a complete client directory with contact details, tax numbers, company info, and a full financial history — invoices, projects, and payment records.</p>
            </div>

            <div class="ft-cell fc-amber">
                <div class="ft-num">06</div>
                <i class="fa-solid fa-diagram-project ft-icon"></i>
                <h4>Project Tracking</h4>
                <p>Manage projects with budgets, deadlines, progress percentages, and status. Instantly see which projects are on track and which need your attention.</p>
            </div>

            <div class="ft-cell fc-emerald">
                <div class="ft-num">07</div>
                <i class="fa-solid fa-piggy-bank ft-icon"></i>
                <h4>Savings Goals</h4>
                <p>Create multiple savings accounts with individual target amounts. Deposit, withdraw, and monitor progress toward each goal independently over time.</p>
            </div>

            <div class="ft-cell fc-pink">
                <div class="ft-num">08</div>
                <i class="fa-solid fa-hand-holding-dollar ft-icon"></i>
                <h4>Debt Management</h4>
                <p>Track money owed to you (receivables) and money you owe to vendors (payables). Monitor due dates and payment statuses to avoid surprises.</p>
            </div>

            <div class="ft-cell fc-indigo">
                <div class="ft-num">09</div>
                <i class="fa-solid fa-chart-pie ft-icon"></i>
                <h4>Financial Reports</h4>
                <p>Filter data by month and year. See income vs expense charts, category breakdowns, top clients by revenue, and outstanding invoice lists at a glance.</p>
            </div>

        </div>

        <div class="text-center mt-5">
            <a href="{{ route('how-it-works') }}" class="btn btn-outline btn-lg">
                Explore All Features <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     SCREENSHOTS — App in Action
     ════════════════════════════════════════════════════════════ --}}
<section class="section">
    <div class="container" style="max-width:1160px;">

        <div class="row mb-5">
            <div class="col-lg-7">
                <span class="chip mb-3"><i class="fa-solid fa-display"></i> See It in Action</span>
                <h2 class="section-title">
                    Designed for Clarity,<br>
                    <span class="gradient-text">Built for Speed</span>
                </h2>
                <p class="section-lead">
                    Every screen in FinTrack is designed to surface the information you need
                    fast, with a clean dark interface that reduces cognitive load.
                </p>
            </div>
        </div>

        <div class="ss-tabs">
            <button class="ss-tab active" data-tab="dashboard">Dashboard</button>
            <button class="ss-tab" data-tab="invoices">Invoices</button>
            <button class="ss-tab" data-tab="expenses">Expenses</button>
            <button class="ss-tab" data-tab="share">Invoice Sharing</button>
            <button class="ss-tab" data-tab="recurring">Recurring</button>
        </div>

        <div class="ss-display">
            <div class="ss-pane active" id="tab-dashboard">
                <img src="{{ asset('assets/images/dashbaord.png') }}"
                     alt="FinTrack Dashboard — complete financial overview"
                     loading="lazy">
            </div>
            <div class="ss-pane" id="tab-invoices">
                <img src="{{ asset('assets/images/invoices-darkmode.png') }}"
                     alt="FinTrack Invoice Management — professional invoicing"
                     loading="lazy">
            </div>
            <div class="ss-pane" id="tab-expenses">
                <img src="{{ asset('assets/images/expenses.png') }}"
                     alt="FinTrack Expense Tracking — categorise all business expenses"
                     loading="lazy">
            </div>
            <div class="ss-pane" id="tab-share">
                <img src="{{ asset('assets/images/shareinvoicewithdark.png') }}"
                     alt="FinTrack Invoice Sharing — share invoices via a unique link"
                     loading="lazy">
            </div>
            <div class="ss-pane" id="tab-recurring">
                <img src="{{ asset('assets/images/recurring-darkmode.png') }}"
                     alt="FinTrack Recurring Transactions — automate regular records"
                     loading="lazy">
            </div>
        </div>

    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     HOW IT WORKS — Steps
     ════════════════════════════════════════════════════════════ --}}
<section class="section" style="background:var(--bg-surface); padding:100px 0;">
    <div class="container" style="max-width:1160px;">
        <div class="row align-items-start g-5">

            <div class="col-lg-5">
                <span class="chip mb-3"><i class="fa-solid fa-list-check"></i> How It Works</span>
                <h2 class="section-title">
                    Up and Running in<br>
                    <span class="gradient-text">Minutes, Not Days</span>
                </h2>
                <p class="section-lead">
                    FinTrack is built to be intuitive from day one.
                    No accountant required. No complex setup.
                </p>
                <div class="mt-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            Go to Dashboard <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            Get Started Free <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-7">
                <div class="step-row">
                    <div class="step-circle">1</div>
                    <div class="step-body">
                        <h4>Create Your Account &amp; Set Up Your Profile</h4>
                        <p>Register in seconds. Add your business name, logo, and preferred currency. FinTrack supports 36 currencies out of the box — work in any market, anywhere in the world.</p>
                    </div>
                </div>
                <div class="step-row">
                    <div class="step-circle">2</div>
                    <div class="step-body">
                        <h4>Add Your Clients &amp; Projects</h4>
                        <p>Build your client directory and create projects to organise your work. Each client becomes a hub for all related financial activity — invoices, income, quotes, and more.</p>
                    </div>
                </div>
                <div class="step-row">
                    <div class="step-circle">3</div>
                    <div class="step-body">
                        <h4>Track Income, Send Invoices &amp; Log Expenses</h4>
                        <p>Record every payment, generate professional invoices, send quotes, and log every business expense. Everything is connected so your numbers are always up to date and accurate.</p>
                    </div>
                </div>
                <div class="step-row">
                    <div class="step-circle">4</div>
                    <div class="step-body">
                        <h4>Gain Clarity &amp; Make Better Financial Decisions</h4>
                        <p>Use the dashboard and financial overview to understand your net position, spot income trends, identify your most valuable clients, and plan for growth with confidence.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     FINAL CTA
     ════════════════════════════════════════════════════════════ --}}
<section class="section">
    <div class="container" style="max-width:900px;">
        <div class="cta-box">
            <h2>
                Take Control of Your<br>
                <span class="gradient-text">Freelance Finances Today</span>
            </h2>
            <p>
                Stop making financial decisions based on gut feeling.
                FinTrack gives you the tools, the data, and the clarity to
                run your freelance business like a professional.
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
                    <a href="{{ route('login') }}" class="btn btn-outline btn-lg">
                        Log In
                    </a>
                @endauth
            </div>
            <p style="font-size:13px; color:var(--text-muted); margin-top:20px; margin-bottom:0;">
                No credit card required &nbsp;·&nbsp; Free to get started &nbsp;·&nbsp; Built for freelancers
            </p>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
/* Screenshot tab switcher */
(function () {
    const tabs  = document.querySelectorAll('.ss-tab');
    const panes = document.querySelectorAll('.ss-pane');
    const idMap = {
        dashboard: 'tab-dashboard',
        invoices:  'tab-invoices',
        expenses:  'tab-expenses',
        share:     'tab-share',
        recurring: 'tab-recurring',
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const el = document.getElementById(idMap[key]);
            if (el) el.classList.add('active');
        });
    });
})();
</script>
@endsection
