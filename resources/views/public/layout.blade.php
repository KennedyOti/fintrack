<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FinTrack')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        :root {
            --ft-navy:    #0B2A4A;
            --ft-blue:    #0F3A66;
            --ft-teal:    #0E7490;
            --ft-cyan:    #22D3EE;
            --ft-emerald: #22C55E;
            --ft-rose:    #F43F5E;
            --ft-amber:   #F59E0B;
            --bg-page:    #F1F5F9;
            --border:     #E2E8F0;
            --text-h:     #0F172A;
            --text-body:  #334155;
            --text-muted: #64748B;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            font-size: 14px;
            background: var(--bg-page);
            color: var(--text-body);
            min-height: 100vh;
            margin: 0;
        }

        /* ── Top bar ── */
        .pub-topbar {
            background: var(--ft-navy);
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(11,42,74,.3);
        }

        .pub-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 17px;
            letter-spacing: -.3px;
        }

        .pub-brand-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--ft-teal), var(--ft-cyan));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #fff;
        }

        .pub-brand-sub {
            font-size: 11px;
            font-weight: 400;
            color: rgba(255,255,255,.55);
            letter-spacing: 0;
        }

        .pub-topbar-right {
            font-size: 12px;
            color: rgba(255,255,255,.55);
        }

        /* ── Page content ── */
        .pub-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 16px 60px;
        }

        /* ── Document card ── */
        .doc-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 32px rgba(15,30,60,.10);
            overflow: hidden;
        }

        /* ── Doc header strip ── */
        .doc-header {
            background: linear-gradient(135deg, var(--ft-navy) 0%, var(--ft-blue) 100%);
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
        }

        @media (max-width: 600px) {
            .doc-header { flex-direction: column; padding: 24px 20px; }
            .doc-header-right { text-align: left !important; }
        }

        .doc-biz-name {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .doc-biz-detail {
            font-size: 12px;
            color: rgba(255,255,255,.65);
            line-height: 1.7;
        }

        .doc-header-right {
            text-align: right;
            flex-shrink: 0;
        }

        .doc-type-label {
            font-size: 28px;
            font-weight: 800;
            color: var(--ft-cyan);
            letter-spacing: 2px;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 4px;
        }

        .doc-number {
            font-size: 14px;
            color: rgba(255,255,255,.75);
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* Status badges */
        .doc-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .ds-draft     { background: rgba(100,116,139,.25); color: #cbd5e1; }
        .ds-sent      { background: rgba(34,211,238,.25); color: var(--ft-cyan); }
        .ds-accepted  { background: rgba(34,197,94,.25); color: #86efac; }
        .ds-rejected  { background: rgba(244,63,94,.25); color: #fda4af; }
        .ds-expired   { background: rgba(245,158,11,.25); color: #fde68a; }
        .ds-converted { background: rgba(139,92,246,.25); color: #c4b5fd; }
        .ds-paid      { background: rgba(34,197,94,.25); color: #86efac; }
        .ds-partial   { background: rgba(245,158,11,.25); color: #fde68a; }
        .ds-overdue   { background: rgba(244,63,94,.25); color: #fda4af; }
        .ds-cancelled { background: rgba(100,116,139,.25); color: #cbd5e1; }

        /* ── Body section ── */
        .doc-body { padding: 32px 40px; }
        @media (max-width: 600px) { .doc-body { padding: 20px; } }

        /* ── Info blocks ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 32px;
        }
        @media (max-width: 600px) { .info-grid { grid-template-columns: 1fr; } }

        .info-block {
            background: var(--bg-page);
            border-radius: 10px;
            padding: 16px 20px;
            border-left: 3px solid var(--border);
        }
        .info-block.ib-client { border-left-color: var(--ft-teal); }
        .info-block.ib-meta   { border-left-color: var(--ft-navy); }

        .info-block-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .info-block-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-h);
            margin-bottom: 4px;
        }

        .info-block-detail {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            margin-bottom: 6px;
        }
        .meta-row:last-child { margin-bottom: 0; }
        .meta-row .ml { color: var(--text-muted); }
        .meta-row .mv { font-weight: 600; color: var(--text-h); }
        .meta-row .mv.overdue { color: var(--ft-rose); }

        /* ── Items table ── */
        .items-section { margin-bottom: 28px; }

        .section-heading {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .items-table thead th {
            background: var(--ft-navy);
            color: rgba(255,255,255,.85);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 11px 14px;
        }

        .items-table thead th:first-child { border-radius: 8px 0 0 0; }
        .items-table thead th:last-child  { border-radius: 0 8px 0 0; text-align: right; }
        .items-table th:nth-child(2),
        .items-table th:nth-child(3) { text-align: center; }

        .items-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text-body);
        }

        .items-table tbody tr:last-child td { border-bottom: none; }
        .items-table tbody tr:nth-child(even) td { background: #FAFBFD; }

        .items-table td:nth-child(2),
        .items-table td:nth-child(3) { text-align: center; color: var(--text-muted); }
        .items-table td:last-child   { text-align: right; font-weight: 600; color: var(--text-h); }

        /* ── Summary ── */
        .summary-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 28px;
        }

        .summary-box {
            width: 300px;
            background: var(--bg-page);
            border-radius: 10px;
            padding: 16px 20px;
        }
        @media (max-width: 600px) { .summary-box { width: 100%; } }

        .sum-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 5px 0;
        }
        .sum-row .sl { color: var(--text-muted); }
        .sum-row .sv { font-weight: 600; color: var(--text-body); }
        .sum-row.sum-divider { border-top: 1px solid var(--border); margin-top: 4px; padding-top: 10px; }
        .sum-row.sum-total .sl { font-weight: 700; font-size: 14px; color: var(--text-h); }
        .sum-row.sum-total .sv { font-weight: 800; font-size: 16px; color: var(--ft-navy); }
        .sum-row.sum-paid .sv  { color: var(--ft-emerald); }
        .sum-row.sum-balance .sl { font-weight: 700; color: var(--text-h); }
        .sum-row.sum-balance .sv { font-weight: 800; font-size: 15px; color: var(--ft-rose); }
        .sum-row.sum-balance.paid .sv { color: var(--ft-emerald); }

        /* ── Notes ── */
        .notes-box {
            background: #FEFCE8;
            border-left: 3px solid var(--ft-amber);
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 28px;
        }
        .notes-box .notes-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #92400E; margin-bottom: 6px; }
        .notes-box p { font-size: 13px; color: #78350F; margin: 0; line-height: 1.6; }

        /* ── Action bar ── */
        .action-bar {
            background: var(--bg-page);
            border-top: 1px solid var(--border);
            padding: 20px 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        @media (max-width: 600px) { .action-bar { padding: 16px 20px; } }

        /* ── Flash alerts ── */
        .pub-alert {
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 16px;
            font-size: 13.5px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .pub-alert-success { background: #DCFCE7; border-left: 3px solid var(--ft-emerald); color: #166534; }
        .pub-alert-error   { background: #FEE2E2; border-left: 3px solid var(--ft-rose);    color: #991B1B; }
        .pub-alert-info    { background: #E0F2FE; border-left: 3px solid var(--ft-teal);    color: #0C4A6E; }
        .pub-alert-warning { background: #FEF3C7; border-left: 3px solid var(--ft-amber);   color: #92400E; }

        /* ── Footer ── */
        .pub-footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: var(--text-muted);
        }
        .pub-footer a { color: var(--ft-teal); text-decoration: none; }

        /* ── Buttons (override Bootstrap slightly) ── */
        .btn { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; border-radius: 8px; }
        .btn-navy   { background: var(--ft-navy); color: #fff; border: none; }
        .btn-navy:hover { background: var(--ft-blue); color: #fff; }
        .btn-teal   { background: var(--ft-teal); color: #fff; border: none; }
        .btn-teal:hover { background: #0c6882; color: #fff; }
        .btn-accept { background: var(--ft-emerald); color: #fff; border: none; font-size: 15px; padding: 10px 28px; }
        .btn-accept:hover { background: #16a34a; color: #fff; }
        .btn-reject { background: #fff; color: var(--ft-rose); border: 1px solid var(--ft-rose); font-size: 15px; padding: 10px 28px; }
        .btn-reject:hover { background: var(--ft-rose); color: #fff; }

        /* ── Quote action box ── */
        .quote-action-box {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 24px 28px;
            text-align: center;
            margin-bottom: 28px;
        }
        .quote-action-box h5 { font-weight: 700; color: #065f46; margin-bottom: 8px; }
        .quote-action-box p  { font-size: 13px; color: #047857; margin-bottom: 20px; }
        .quote-action-btns   { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        .quote-status-box {
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .qsb-accepted { background: #DCFCE7; border: 1px solid #86efac; }
        .qsb-rejected { background: #FEE2E2; border: 1px solid #fca5a5; }
        .qsb-expired  { background: #FEF3C7; border: 1px solid #fde68a; }
        .qsb-converted { background: #EFF6FF; border: 1px solid #bfdbfe; }
        .qsb-icon { font-size: 26px; flex-shrink: 0; }
        .qsb-text h6 { font-weight: 700; margin-bottom: 2px; }
        .qsb-text p  { font-size: 12.5px; margin: 0; }
    </style>
    @yield('head')
</head>
<body>
    <!-- Top bar -->
    <div class="pub-topbar">
        <a href="{{ url('/') }}" class="pub-brand">
            <div class="pub-brand-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <div>FinTrack</div>
                <div class="pub-brand-sub">Financial Management</div>
            </div>
        </a>
        <div class="pub-topbar-right">
            <i class="fas fa-shield-halved me-1"></i> Secure Document
        </div>
    </div>

    <div class="pub-content">
        <!-- Flash messages -->
        @if(session('success'))
            <div class="pub-alert pub-alert-success">
                <i class="fas fa-check-circle mt-1 flex-shrink-0"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="pub-alert pub-alert-error">
                <i class="fas fa-times-circle mt-1 flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if(session('info'))
            <div class="pub-alert pub-alert-info">
                <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif
        @if(session('warning'))
            <div class="pub-alert pub-alert-warning">
                <i class="fas fa-exclamation-triangle mt-1 flex-shrink-0"></i>
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        @yield('content')
    </div>

    <div class="pub-footer">
        <p>Powered by <a href="{{ url('/') }}">FinTrack</a> &mdash; Professional Financial Management for Freelancers</p>
        <p class="mt-1" style="color:#94a3b8; font-size:11px;">
            <i class="fas fa-lock me-1"></i>This is a secure, unique link generated for you. Do not share this link.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
