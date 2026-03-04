@extends('layouts.app')

@section('title', 'Free Invoicing, Quotation & Project Management Tools for Freelancers | FinTrack')
@section('meta-description', 'FinTrack provides free invoicing, online quotation, and project management tools built for freelancers and independent professionals. Generate professional PDF invoices and quotes, manage projects with kanban boards and milestones — all in one place, completely free.')
@section('og-title', 'Free Invoicing, Quote & Project Management Tools for Freelancers | FinTrack')
@section('og-description', 'Discover FinTrack\'s free tools: professional invoice generator, online quotation builder, and project management with kanban boards. Built exclusively for freelancers and independent professionals.')

@section('content')

{{-- ── Hero ──────────────────────────────────────────────────────────── --}}
<section style="position:relative; overflow:hidden; padding:140px 0 90px;">
    <div style="position:absolute; inset:0; pointer-events:none;">
        <div style="position:absolute; inset:0;
                    background-image:radial-gradient(rgba(34,211,238,0.10) 1px, transparent 1px);
                    background-size:38px 38px;
                    mask-image:radial-gradient(ellipse 70% 60% at 50% 0%, black 20%, transparent 100%);
                    -webkit-mask-image:radial-gradient(ellipse 70% 60% at 50% 0%, black 20%, transparent 100%);">
        </div>
        <div style="position:absolute; width:600px; height:600px; border-radius:50%;
                    background:radial-gradient(circle,rgba(139,92,246,0.06) 0%,transparent 70%);
                    right:-100px; top:-120px; pointer-events:none;"></div>
        <div style="position:absolute; width:400px; height:400px; border-radius:50%;
                    background:radial-gradient(circle,rgba(34,211,238,0.05) 0%,transparent 70%);
                    left:-60px; bottom:-80px; pointer-events:none;"></div>
    </div>

    <div class="container text-center" style="max-width:800px; position:relative; z-index:2;">
        <span class="chip mb-4"><i class="fa-solid fa-toolbox"></i> Free Tools for Freelancers</span>
        <h1 style="font-size:clamp(34px,5.5vw,62px); font-weight:800; letter-spacing:-0.03em; margin-bottom:24px; line-height:1.06;">
            Professional Tools.<br>
            <span class="gradient-text">Completely Free.</span>
        </h1>
        <p style="font-size:18px; color:var(--text-secondary); line-height:1.85; max-width:640px; margin:0 auto 36px;">
            Win more clients with polished quotes, get paid faster with professional invoices,
            and deliver projects on time with structured management tools —
            all built specifically for freelancers and independent business professionals.
        </p>

        {{-- Tool anchor pills --}}
        <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap; margin-bottom:36px;">
            <a href="#invoicing"         style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; background:var(--bg-card); border:1px solid var(--border-subtle); border-radius:50px; font-size:13.5px; font-weight:600; color:var(--text-primary); text-decoration:none; transition:border-color .2s;" onmouseover="this.style.borderColor='var(--cyan)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
                <i class="fa-solid fa-file-invoice-dollar" style="color:var(--cyan);"></i> Free Invoicing
            </a>
            <a href="#quotations"        style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; background:var(--bg-card); border:1px solid var(--border-subtle); border-radius:50px; font-size:13.5px; font-weight:600; color:var(--text-primary); text-decoration:none; transition:border-color .2s;" onmouseover="this.style.borderColor='var(--violet)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
                <i class="fa-solid fa-file-signature" style="color:var(--violet);"></i> Free Quotations
            </a>
            <a href="#project-management" style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; background:var(--bg-card); border:1px solid var(--border-subtle); border-radius:50px; font-size:13.5px; font-weight:600; color:var(--text-primary); text-decoration:none; transition:border-color .2s;" onmouseover="this.style.borderColor='var(--emerald)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
                <i class="fa-solid fa-diagram-project" style="color:var(--emerald);"></i> Project Management
            </a>
        </div>

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

{{-- ── Product Cards Overview ───────────────────────────────────────── --}}
<section style="padding:0 0 80px;">
    <div class="container" style="max-width:1060px;">
        <div class="row g-4">

            {{-- Card 1: Invoicing --}}
            <div class="col-lg-4">
                <a href="#invoicing" style="text-decoration:none; display:block; height:100%;">
                    <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-xl); padding:32px 28px; height:100%; transition:border-color .25s, transform .25s; cursor:pointer;"
                         onmouseover="this.style.borderColor='var(--cyan)'; this.style.transform='translateY(-4px)'"
                         onmouseout="this.style.borderColor='var(--border-dim)'; this.style.transform='translateY(0)'">
                        <div style="width:52px; height:52px; border-radius:var(--r-md); background:rgba(34,211,238,0.12); display:flex; align-items:center; justify-content:center; margin-bottom:20px;">
                            <i class="fa-solid fa-file-invoice-dollar" style="font-size:22px; color:var(--cyan);"></i>
                        </div>
                        <h2 style="font-size:19px; font-weight:700; margin-bottom:10px; color:var(--text-primary); letter-spacing:-0.02em;">Free Invoice Generator</h2>
                        <p style="font-size:14px; color:var(--text-secondary); line-height:1.7; margin-bottom:18px;">
                            Create professional PDF invoices with line items, taxes, and discounts. Track payment status and auto-record income when clients pay.
                        </p>
                        <span style="font-size:13px; font-weight:600; color:var(--cyan);">Explore tool <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i></span>
                    </div>
                </a>
            </div>

            {{-- Card 2: Quotations --}}
            <div class="col-lg-4">
                <a href="#quotations" style="text-decoration:none; display:block; height:100%;">
                    <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-xl); padding:32px 28px; height:100%; transition:border-color .25s, transform .25s; cursor:pointer;"
                         onmouseover="this.style.borderColor='var(--violet)'; this.style.transform='translateY(-4px)'"
                         onmouseout="this.style.borderColor='var(--border-dim)'; this.style.transform='translateY(0)'">
                        <div style="width:52px; height:52px; border-radius:var(--r-md); background:rgba(139,92,246,0.12); display:flex; align-items:center; justify-content:center; margin-bottom:20px;">
                            <i class="fa-solid fa-file-signature" style="font-size:22px; color:var(--violet);"></i>
                        </div>
                        <h2 style="font-size:19px; font-weight:700; margin-bottom:10px; color:var(--text-primary); letter-spacing:-0.02em;">Free Quotation Builder</h2>
                        <p style="font-size:14px; color:var(--text-secondary); line-height:1.7; margin-bottom:18px;">
                            Send polished quotes to potential clients with validity dates and itemised pricing. Accept, reject, and convert accepted quotes directly to invoices in one click.
                        </p>
                        <span style="font-size:13px; font-weight:600; color:var(--violet);">Explore tool <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i></span>
                    </div>
                </a>
            </div>

            {{-- Card 3: Projects --}}
            <div class="col-lg-4">
                <a href="#project-management" style="text-decoration:none; display:block; height:100%;">
                    <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-xl); padding:32px 28px; height:100%; transition:border-color .25s, transform .25s; cursor:pointer;"
                         onmouseover="this.style.borderColor='var(--emerald)'; this.style.transform='translateY(-4px)'"
                         onmouseout="this.style.borderColor='var(--border-dim)'; this.style.transform='translateY(0)'">
                        <div style="width:52px; height:52px; border-radius:var(--r-md); background:rgba(34,197,94,0.12); display:flex; align-items:center; justify-content:center; margin-bottom:20px;">
                            <i class="fa-solid fa-diagram-project" style="font-size:22px; color:var(--emerald);"></i>
                        </div>
                        <h2 style="font-size:19px; font-weight:700; margin-bottom:10px; color:var(--text-primary); letter-spacing:-0.02em;">Project Management Tool</h2>
                        <p style="font-size:14px; color:var(--text-secondary); line-height:1.7; margin-bottom:18px;">
                            Manage client projects with milestones, kanban boards, task tracking, and time logging — all tied to your project budget for full financial visibility.
                        </p>
                        <span style="font-size:13px; font-weight:600; color:var(--emerald);">Explore tool <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i></span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- ── Tool 1: Free Invoicing ──────────────────────────────────────── --}}
<section id="invoicing" style="padding:100px 0; background:var(--bg-surface); border-top:1px solid var(--border-dim); scroll-margin-top:80px;">
    <div class="container" style="max-width:1060px;">
        <div class="row align-items-center g-5">

            {{-- Text --}}
            <div class="col-lg-6 order-lg-1">
                <span class="chip mb-3" style="color:var(--cyan); border-color:rgba(34,211,238,0.3);">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Free Invoicing Tool
                </span>
                <h2 style="font-size:clamp(26px,3.5vw,42px); font-weight:800; margin-bottom:18px; letter-spacing:-0.03em; line-height:1.15;">
                    Free Invoice Generator<br>
                    <span class="gradient-text">for Freelancers</span>
                </h2>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.85; margin-bottom:16px;">
                    Stop chasing payments with amateur-looking payment requests. FinTrack's free invoice generator lets you
                    create polished, professional invoices in minutes — complete with your business logo, itemised line items,
                    taxes, discounts, and due dates.
                </p>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.85; margin-bottom:28px;">
                    Every invoice you send is tracked through its full lifecycle: from <strong style="color:var(--text-primary);">draft</strong>
                    to <strong style="color:var(--text-primary);">sent</strong>, <strong style="color:var(--text-primary);">partial payment</strong>,
                    and <strong style="color:var(--text-primary);">fully paid</strong>. When a client pays, FinTrack automatically
                    records the income and updates your outstanding receivables — no double entry required.
                </p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:32px;">
                    @php
                    $invFeatures = [
                        ['fa-file-pdf',            'PDF download & sharing'],
                        ['fa-list-check',          'Line items, quantities & rates'],
                        ['fa-percent',             'Tax & discount calculations'],
                        ['fa-link',                'Shareable client payment links'],
                        ['fa-clock-rotate-left',   'Overdue tracking & alerts'],
                        ['fa-chart-line',          'Auto-records income on payment'],
                        ['fa-globe',               '36 currencies supported'],
                        ['fa-building',            'Custom branding & logo'],
                    ];
                    @endphp
                    @foreach($invFeatures as $f)
                    <div style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border-dim); border-radius:var(--r-md);">
                        <i class="fa-solid {{ $f[0] }}" style="color:var(--cyan); width:14px; flex-shrink:0; font-size:13px;"></i>
                        <span style="font-size:13px; color:var(--text-secondary);">{{ $f[1] }}</span>
                    </div>
                    @endforeach
                </div>

                @guest
                <a href="{{ route('register') }}" class="btn btn-primary">
                    Start Generating Free Invoices <i class="fa-solid fa-arrow-right"></i>
                </a>
                @endguest
            </div>

            {{-- Screenshot --}}
            <div class="col-lg-6 order-lg-2">
                <div style="position:relative;">
                    <div style="position:absolute; inset:-20px; background:radial-gradient(circle at 50% 50%, rgba(34,211,238,0.08) 0%, transparent 70%); border-radius:var(--r-xl); pointer-events:none;"></div>
                    <img src="{{ asset('assets/images/products/invoice.png') }}"
                         alt="FinTrack free invoice generator — create professional PDF invoices for freelancers"
                         style="width:100%; border-radius:var(--r-xl); border:1px solid var(--border-subtle); box-shadow:var(--shadow-xl); position:relative; z-index:1;">
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── Tool 2: Free Quotation Tool ─────────────────────────────────── --}}
<section id="quotations" style="padding:100px 0; scroll-margin-top:80px;">
    <div class="container" style="max-width:1060px;">
        <div class="row align-items-center g-5">

            {{-- Screenshot --}}
            <div class="col-lg-6 order-lg-1">
                <div style="position:relative;">
                    <div style="position:absolute; inset:-20px; background:radial-gradient(circle at 50% 50%, rgba(139,92,246,0.08) 0%, transparent 70%); border-radius:var(--r-xl); pointer-events:none;"></div>
                    <img src="{{ asset('assets/images/products/quote.png') }}"
                         alt="FinTrack free quotation builder — send professional quotes and convert them to invoices"
                         style="width:100%; border-radius:var(--r-xl); border:1px solid var(--border-subtle); box-shadow:var(--shadow-xl); position:relative; z-index:1;">
                </div>
            </div>

            {{-- Text --}}
            <div class="col-lg-6 order-lg-2">
                <span class="chip mb-3" style="color:var(--violet); border-color:rgba(139,92,246,0.3);">
                    <i class="fa-solid fa-file-signature"></i> Free Quotation Tool
                </span>
                <h2 style="font-size:clamp(26px,3.5vw,42px); font-weight:800; margin-bottom:18px; letter-spacing:-0.03em; line-height:1.15;">
                    Free Online Quotation Builder<br>
                    <span style="background:linear-gradient(135deg,#8B5CF6,#22D3EE); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">Win More Clients</span>
                </h2>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.85; margin-bottom:16px;">
                    Your proposal is often the first impression a potential client has of your professionalism.
                    FinTrack's free quotation tool lets you generate structured, itemised quotes that look like they
                    came from a seasoned agency — not a freelancer cobbling together a Word document.
                </p>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.85; margin-bottom:28px;">
                    Set validity periods so clients feel the urgency to decide. When they accept, convert the quote
                    to a live invoice in a single click — no re-entering data. Quotes that are rejected or expired
                    are automatically archived, keeping your pipeline clean.
                </p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:32px;">
                    @php
                    $qtFeatures = [
                        ['fa-file-pdf',              'Downloadable PDF quotes'],
                        ['fa-list-ul',               'Itemised line items & pricing'],
                        ['fa-calendar-check',        'Validity date & expiry tracking'],
                        ['fa-share-nodes',           'Shareable client-view links'],
                        ['fa-circle-check',          'Online accept / reject by client'],
                        ['fa-rotate',                'One-click convert to invoice'],
                        ['fa-tag',                   'Draft, Sent, Accepted, Expired'],
                        ['fa-building',              'Custom branding with logo'],
                    ];
                    @endphp
                    @foreach($qtFeatures as $f)
                    <div style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-md);">
                        <i class="fa-solid {{ $f[0] }}" style="color:var(--violet); width:14px; flex-shrink:0; font-size:13px;"></i>
                        <span style="font-size:13px; color:var(--text-secondary);">{{ $f[1] }}</span>
                    </div>
                    @endforeach
                </div>

                @guest
                <a href="{{ route('register') }}" class="btn btn-primary">
                    Start Sending Free Quotes <i class="fa-solid fa-arrow-right"></i>
                </a>
                @endguest
            </div>

        </div>
    </div>
</section>

{{-- ── Tool 3: Project Management ───────────────────────────────────── --}}
<section id="project-management" style="padding:100px 0; background:var(--bg-surface); border-top:1px solid var(--border-dim); scroll-margin-top:80px;">
    <div class="container" style="max-width:1060px;">
        <div class="row align-items-center g-5">

            {{-- Text --}}
            <div class="col-lg-6 order-lg-1">
                <span class="chip mb-3" style="color:var(--emerald); border-color:rgba(34,197,94,0.3);">
                    <i class="fa-solid fa-diagram-project"></i> Project Management Tool
                </span>
                <h2 style="font-size:clamp(26px,3.5vw,42px); font-weight:800; margin-bottom:18px; letter-spacing:-0.03em; line-height:1.15;">
                    Project Management Tool<br>
                    <span style="background:linear-gradient(135deg,#22C55E,#22D3EE); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">for Freelancers</span>
                </h2>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.85; margin-bottom:16px;">
                    Managing client projects across spreadsheets, sticky notes, and email threads is a productivity killer.
                    FinTrack's project management tool gives you a structured, visual workspace for every project — without
                    the bloat of enterprise tools that weren't built for how freelancers actually work.
                </p>
                <p style="font-size:16px; color:var(--text-secondary); line-height:1.85; margin-bottom:28px;">
                    Break each project into <strong style="color:var(--text-primary);">milestones</strong>, then manage the
                    work inside each milestone with a full <strong style="color:var(--text-primary);">kanban board</strong>.
                    Log your time, track progress percentages, and keep a close eye on the budget — because knowing
                    whether a project is profitable is just as important as delivering it on time.
                </p>

                {{-- Feature grid --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:32px;">
                    @php
                    $pmFeatures = [
                        ['fa-layer-group',      'Milestone / phase planning'],
                        ['fa-table-columns',    'Drag-and-drop kanban board'],
                        ['fa-list-check',       'Task list with priorities'],
                        ['fa-clock',            'Time logging per task'],
                        ['fa-gauge-high',       'Progress percentage tracking'],
                        ['fa-coins',            'Budget vs actual tracking'],
                        ['fa-flag',             'Task deadlines & due dates'],
                        ['fa-link',             'Linked to clients & invoices'],
                    ];
                    @endphp
                    @foreach($pmFeatures as $f)
                    <div style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border-dim); border-radius:var(--r-md);">
                        <i class="fa-solid {{ $f[0] }}" style="color:var(--emerald); width:14px; flex-shrink:0; font-size:13px;"></i>
                        <span style="font-size:13px; color:var(--text-secondary);">{{ $f[1] }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Task status badges --}}
                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:32px;">
                    @php
                    $statuses = [
                        ['To Do',       '#506080'],
                        ['In Progress', 'var(--cyan)'],
                        ['In Review',   'var(--violet)'],
                        ['Blocked',     'var(--rose)'],
                        ['Done',        'var(--emerald)'],
                    ];
                    @endphp
                    @foreach($statuses as $s)
                    <span style="padding:5px 12px; border-radius:50px; font-size:12px; font-weight:600; background:rgba(0,0,0,0.2); border:1px solid {{ $s[1] }}; color:{{ $s[1] }};">{{ $s[0] }}</span>
                    @endforeach
                </div>

                @guest
                <a href="{{ route('register') }}" class="btn btn-primary">
                    Start Managing Projects Free <i class="fa-solid fa-arrow-right"></i>
                </a>
                @endguest
            </div>

            {{-- Screenshot --}}
            <div class="col-lg-6 order-lg-2">
                <div style="position:relative;">
                    <div style="position:absolute; inset:-20px; background:radial-gradient(circle at 50% 50%, rgba(34,197,94,0.08) 0%, transparent 70%); border-radius:var(--r-xl); pointer-events:none;"></div>
                    <img src="{{ asset('assets/images/products/project.png') }}"
                         alt="FinTrack project management tool — kanban boards, milestones and budget tracking for freelancers"
                         style="width:100%; border-radius:var(--r-xl); border:1px solid var(--border-subtle); box-shadow:var(--shadow-xl); position:relative; z-index:1;">
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── Why Everything Is Free ──────────────────────────────────────── --}}
<section style="padding:96px 0;">
    <div class="container" style="max-width:1060px;">

        <div class="text-center mb-5">
            <span class="chip mb-3"><i class="fa-solid fa-gift"></i> Why It's Free</span>
            <h2 style="font-size:clamp(26px,3.5vw,40px); font-weight:700; letter-spacing:-0.025em; margin-bottom:14px;">
                Everything You Need. <span class="gradient-text">No Subscription Required.</span>
            </h2>
            <p style="font-size:16.5px; color:var(--text-secondary); max-width:580px; margin:0 auto; line-height:1.8;">
                Freelancers already deal with unpredictable income. We don't think your financial tools
                should add to that uncertainty with monthly fees or paywalled features.
            </p>
        </div>

        <div class="row g-4">
            @php
            $whys = [
                ['fa-lock-open',        'var(--cyan)',    'No Feature Gating',       'All three tools — invoicing, quotations, and project management — are fully accessible from day one. No tiers, no upgrades needed.'],
                ['fa-credit-card-alt',  'var(--emerald)', 'No Credit Card Required', 'Sign up with just your email. Start creating invoices and quotes immediately without entering payment details.'],
                ['fa-infinity',         'var(--violet)',  'Unlimited Records',        'Create as many invoices, quotes, and projects as your business demands. There are no artificial limits on your usage.'],
                ['fa-shield-halved',    'var(--amber)',   'Your Data, Yours Alone',   'Your financial data belongs to you. We don\'t sell it, share it, or use it for advertising purposes.'],
            ];
            @endphp
            @foreach($whys as $w)
            <div class="col-lg-3 col-md-6">
                <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-lg); padding:28px 22px; height:100%;">
                    <i class="fa-solid {{ $w[0] }}" style="font-size:24px; color:{{ $w[1] }}; margin-bottom:14px; display:block;"></i>
                    <h3 style="font-size:15px; font-weight:700; margin-bottom:10px; color:var(--text-primary);">{{ $w[2] }}</h3>
                    <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.65; margin:0;">{{ $w[3] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ── FAQ ──────────────────────────────────────────────────────────── --}}
<section style="padding:96px 0; background:var(--bg-surface); border-top:1px solid var(--border-dim);">
    <div class="container" style="max-width:760px;">

        <div class="text-center mb-5">
            <span class="chip mb-3"><i class="fa-solid fa-circle-question"></i> FAQ</span>
            <h2 style="font-size:clamp(26px,3.5vw,40px); font-weight:700; letter-spacing:-0.025em; margin-bottom:14px;">
                Frequently Asked Questions
            </h2>
        </div>

        <div class="accordion" id="productsFaq">

            @php
            $faqs = [
                [
                    'q' => 'Is FinTrack\'s invoicing tool really free?',
                    'a' => 'Yes — completely free. You can create unlimited professional PDF invoices, track their payment status, record client payments, and have income automatically logged in your financial records. There is no subscription, no credit card required, and no feature gating. All invoicing features are available from the moment you register.',
                ],
                [
                    'q' => 'Can I send quotes to clients and let them accept online?',
                    'a' => 'Yes. Every quote you create gets a unique shareable link. You can send this link to your client by email and they can view the quote, download a PDF, and mark it as accepted or rejected directly from their browser — no account required on their end. When a quote is accepted, you can convert it to an invoice in one click.',
                ],
                [
                    'q' => 'How does the project management tool compare to tools like Trello or Asana?',
                    'a' => 'FinTrack\'s project management tool is built specifically for freelancers who need financial context alongside their project work. Unlike Trello or Asana, every project in FinTrack is linked to a budget, a client, and your financial records. You can track whether a project is profitable in real time — not just whether tasks are being completed. It includes milestones, kanban boards, task priorities, time logging, and budget tracking in one integrated view.',
                ],
                [
                    'q' => 'Can I customise invoices and quotes with my business branding?',
                    'a' => 'Yes. You can upload your business logo, set your business name and address, and choose from three document layouts (Classic, Modern, and Minimal) with custom colour schemes. Your logo and branding details appear on every invoice and quote PDF you generate, giving clients a polished and consistent professional experience.',
                ],
                [
                    'q' => 'What currencies are supported for invoices and quotes?',
                    'a' => 'FinTrack supports 36 currencies including USD, EUR, GBP, KES (Kenyan Shilling), NGN (Nigerian Naira), ZAR (South African Rand), GHS (Ghanaian Cedi), and many more. You set your preferred currency in Settings, and it is used consistently across all your invoices, quotes, income records, and financial reports.',
                ],
                [
                    'q' => 'What happens when a client pays an invoice?',
                    'a' => 'When you record a payment against an invoice, FinTrack automatically: (1) updates the invoice status to Paid or Partial, (2) creates an Income record linked to the invoice, (3) updates your outstanding receivables balance, and (4) reflects the change in your dashboard financial totals. Everything stays in sync with no manual data entry required.',
                ],
                [
                    'q' => 'Is there a mobile app?',
                    'a' => 'FinTrack is a web application optimised for all screen sizes including mobile browsers. You can access all tools — invoicing, quotations, and project management — from any smartphone or tablet. A dedicated native mobile app is on our roadmap.',
                ],
            ];
            @endphp

            @foreach($faqs as $i => $faq)
            <div class="accordion-item" style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:var(--r-lg) !important; margin-bottom:10px; overflow:hidden;">
                <h3 class="accordion-header">
                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#faq{{ $i }}"
                            aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                            style="background:transparent; color:var(--text-primary); font-size:15px; font-weight:600; padding:20px 24px; box-shadow:none; border-radius:var(--r-lg);">
                        {{ $faq['q'] }}
                    </button>
                </h3>
                <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#productsFaq">
                    <div class="accordion-body" style="padding:0 24px 22px; color:var(--text-secondary); font-size:14.5px; line-height:1.8;">
                        {{ $faq['a'] }}
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ── CTA ──────────────────────────────────────────────────────────── --}}
<section style="padding:96px 0;">
    <div class="container" style="max-width:840px;">
        <div class="cta-box">
            <h2>
                Ready to Work Like a<br>
                <span class="gradient-text">Professional?</span>
            </h2>
            <p>
                Join thousands of freelancers using FinTrack to send better invoices,
                win more clients with polished quotes, and keep projects profitable.
                No subscription. No credit card. Just tools that work.
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
                    <a href="{{ route('about') }}" class="btn btn-outline btn-lg">Learn About FinTrack</a>
                @endauth
            </div>
        </div>
    </div>
</section>

@endsection
