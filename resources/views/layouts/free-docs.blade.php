<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Free Business Docs — FinTrack')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Free Docs CSS --}}
    <link href="{{ asset('assets/css/free-docs.css') }}" rel="stylesheet">

    @stack('styles')

    <style>
        /* ── Builder layout base ── */
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', system-ui, sans-serif;
            background: #0d1117;
            color: #e6edf3;
        }
        /* Scrollable panels inside override overflow */
        .fd-form-panel, .fd-preview-panel { overflow-y: auto; }
    </style>
</head>
<body class="fd-builder-body">

{{-- ── Compact topbar ────────────────────────────────────────── --}}
<div class="fd-topbar">
    <a href="{{ route('home') }}" class="fd-topbar-brand">
        <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
        <span>FinTrack</span>
    </a>
    <span class="fd-topbar-divider">|</span>
    <span class="fd-topbar-label">Free Business Docs</span>

    <div class="fd-topbar-actions">
        <a href="{{ route('free-docs.index') }}" class="fd-topbar-link">
            <i class="fa-solid fa-grid-2"></i> All Docs
        </a>
        <a href="{{ route('register') }}" class="fd-topbar-cta">
            <i class="fa-solid fa-user-plus"></i> Save & Track Free
        </a>
    </div>
</div>

{{-- ── Builder content ──────────────────────────────────────────── --}}
@yield('content')

{{-- ── Scripts ─────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>
