<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta-description', 'FinTrack — The financial command center for freelancers and independent professionals. Invoice clients, track income & expenses, manage savings goals, and gain financial clarity.')">
    <meta name="keywords" content="freelancer finance, income tracking, expense management, invoicing, quotes, client management, savings goals, financial reports">
    <meta name="robots" content="index, follow">
    <meta property="og:title"       content="@yield('og-title', 'FinTrack — Financial Management for Freelancers')">
    <meta property="og:description" content="@yield('og-description', 'The all-in-one financial platform built for freelancers. Track income, send invoices, manage expenses and savings — all in one dashboard.')">
    <meta property="og:type"        content="website">
    <meta property="og:image"       content="{{ asset('assets/images/dashbaord.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/logo.ico') }}">
    <title>@yield('title', 'FinTrack — Financial Management for Freelancers')</title>

    {{-- Fonts: Space Grotesk + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Custom styles --}}
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    @yield('styles')
    @stack('styles')
</head>
<body>

{{-- ── Navbar ──────────────────────────────────────────────── --}}
<nav class="site-nav" id="siteNav">
    <div class="nav-inner">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="nav-brand">
            <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
            <span class="nav-brand-name">FinTrack</span>
        </a>

        {{-- Desktop links --}}
        <ul class="nav-links">
            <li>
                <a href="{{ route('home') }}"
                   class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
            </li>
            <li>
                <a href="{{ route('about') }}"
                   class="{{ request()->routeIs('about') ? 'active' : '' }}">
                    About
                </a>
            </li>
            <li>
                <a href="{{ route('products') }}"
                   class="{{ request()->routeIs('products') ? 'active' : '' }}">
                    Products
                </a>
            </li>
            <li>
                <a href="{{ route('how-it-works') }}"
                   class="{{ request()->routeIs('how-it-works') ? 'active' : '' }}">
                    How It Works
                </a>
            </li>
            <li>
                <a href="{{ route('blog.index') }}"
                   class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">
                    Blog
                </a>
            </li>
            <li>
                <a href="{{ route('free-docs.index') }}"
                   class="{{ request()->routeIs('free-docs.*') ? 'active' : '' }}"
                   style="{{ request()->routeIs('free-docs.*') ? '' : 'color:#22d3ee;font-weight:700;' }}">
                    <i class="fa-solid fa-file-invoice" style="font-size:11px;"></i> Free Docs
                </a>
            </li>
        </ul>

        {{-- Auth actions --}}
        <div class="nav-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"    class="btn btn-ghost btn-sm">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                    Get Started <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endauth
        </div>

        {{-- Mobile hamburger --}}
        <button class="nav-toggle" id="navToggle" aria-label="Open menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
</nav>

{{-- ── Mobile Drawer ────────────────────────────────────────── --}}
<div class="nav-drawer" id="navDrawer">
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer-panel">
        <a href="{{ route('home') }}"          class="drawer-link"><i class="fa-solid fa-house"></i> Home</a>
        <a href="{{ route('about') }}"         class="drawer-link"><i class="fa-solid fa-circle-info"></i> About</a>
        <a href="{{ route('products') }}"      class="drawer-link"><i class="fa-solid fa-toolbox"></i> Products</a>
        <a href="{{ route('how-it-works') }}"  class="drawer-link"><i class="fa-solid fa-circle-question"></i> How It Works</a>
        <a href="{{ route('blog.index') }}"       class="drawer-link"><i class="fa-solid fa-rss"></i> Blog</a>
        <a href="{{ route('free-docs.index') }}" class="drawer-link" style="color:#22d3ee;font-weight:700;"><i class="fa-solid fa-file-invoice"></i> Free Docs</a>
        <div class="drawer-sep"></div>
        <div class="drawer-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-gauge-high"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"    class="btn btn-outline">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    Get Started Free <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endauth
        </div>
    </div>
</div>

{{-- ── Page Content ─────────────────────────────────────────── --}}
<main>
    @yield('content')
</main>

{{-- ── Footer ───────────────────────────────────────────────── --}}
<footer class="site-footer">
    <div class="container" style="max-width:1160px;">
        <div class="row g-5">

            {{-- Brand --}}
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
                    </a>
                </div>
                <p class="footer-desc">
                    FinTrack is the financial command center for freelancers and
                    independent professionals — built to bring clarity and control
                    to your finances.
                </p>
                <div class="footer-socials">
                    <a href="#" class="social-btn" aria-label="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="social-btn" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" class="social-btn" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-btn" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="col-lg-2 col-md-3 col-6">
                <p class="footer-h">Navigation</p>
                <ul class="footer-list">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('products') }}">Products</a></li>
                    <li><a href="{{ route('how-it-works') }}">How It Works</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('login') }}">Log In</a></li>
                    <li><a href="{{ route('register') }}">Get Started</a></li>
                </ul>
            </div>

            {{-- Features --}}
            <div class="col-lg-2 col-md-3 col-6">
                <p class="footer-h">Free Tools</p>
                <ul class="footer-list">
                    <li><a href="{{ route('free-docs.builder', 'invoice') }}">Free Invoice Generator</a></li>
                    <li><a href="{{ route('free-docs.builder', 'quote') }}">Free Quote Builder</a></li>
                    <li><a href="{{ route('free-docs.builder', 'receipt') }}">Free Receipt Maker</a></li>
                    <li><a href="{{ route('free-docs.index') }}">All Free Docs</a></li>
                    <li><a href="{{ route('products') }}">All Products</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="col-lg-4 col-md-6">
                <p class="footer-h">Get in Touch</p>
                <div class="footer-contact">
                    <div class="footer-contact-row">
                        <i class="fa-solid fa-envelope"></i>
                        <span>support@fintrack.co.ke</span>
                    </div>
                    <div class="footer-contact-row">
                        <i class="fa-solid fa-phone"></i>
                        <span>+254 793 543 659</span>
                    </div>
                    <div class="footer-contact-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Nairobi, Kenya</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} FinTrack. All rights reserved. Built for freelancers.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

{{-- ── Scripts ───────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
    /* ── Navbar scroll effect ── */
    const nav = document.getElementById('siteNav');
    function updateNav() {
        nav.classList.toggle('scrolled', window.scrollY > 40);
    }
    window.addEventListener('scroll', updateNav, { passive: true });
    updateNav();

    /* ── Mobile drawer ── */
    const toggle  = document.getElementById('navToggle');
    const drawer  = document.getElementById('navDrawer');
    const overlay = document.getElementById('drawerOverlay');

    function openDrawer() {
        drawer.classList.add('open');
        toggle.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        drawer.classList.remove('open');
        toggle.classList.remove('open');
        document.body.style.overflow = '';
    }

    toggle.addEventListener('click', () => {
        drawer.classList.contains('open') ? closeDrawer() : openDrawer();
    });
    overlay.addEventListener('click', closeDrawer);

    document.querySelectorAll('.drawer-link, .drawer-actions .btn').forEach(el => {
        el.addEventListener('click', closeDrawer);
    });
})();
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>
