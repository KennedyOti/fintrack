<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FinTrack - The financial management platform designed for freelancers. Track income, expenses, savings, generate invoices & quotes, and manage clients - all in one place.">
    <meta name="keywords" content="freelancer finance, income tracking, expense management, invoicing, quotes, client management, financial planning">
    <title>@yield('title', 'FinTrack - Financial Management for Freelancers')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar-custom" id="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a class="navbar-brand-custom" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack Logo">
            </a>
            
            <!-- Desktop Navigation -->
            <ul class="navbar-nav-custom">
                <li><a href="{{ route('home') }}" class="nav-link-custom active">Home</a></li>
                <li><a href="#features" class="nav-link-custom">Features</a></li>
                <li><a href="#how-it-works" class="nav-link-custom">How It Works</a></li>
                <li><a href="#testimonials" class="nav-link-custom">Testimonials</a></li>
                @auth
                <li class="nav-btn">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>
                @else
                <li class="nav-btn">
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>
                <li class="nav-btn">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Get Started
                    </a>
                </li>
                @endauth
            </ul>
            
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
    
    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-header">
            <a class="navbar-brand-custom" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack Logo">
            </a>
            <button class="mobile-sidebar-close" id="sidebarClose" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="mobile-nav-links">
            <li>
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li>
                <a href="#features">
                    <i class="fas fa-star"></i> Features
                </a>
            </li>
            <li>
                <a href="#how-it-works">
                    <i class="fas fa-question-circle"></i> How It Works
                </a>
            </li>
            <li>
                <a href="#testimonials">
                    <i class="fas fa-comment-alt"></i> Testimonials
                </a>
            </li>
        </ul>
        
        <div class="mobile-sidebar-footer">
            @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-th-large"></i> Go to Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-secondary w-100 mb-3">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-primary w-100">
                <i class="fas fa-user-plus"></i> Get Started Free
            </a>
            @endauth
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container">
            <div class="row g-5">
                <!-- Brand Column -->
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack Logo">
                        </a>
                    </div>
                    <p class="footer-description">
                        The financial management platform designed specifically for freelancers. 
                        Track income, expenses, savings, and manage your clients - all in one powerful dashboard.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2">
                    <h6 class="footer-title">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="#features"><i class="fas fa-chevron-right"></i> Features</a></li>
                        <li><a href="#how-it-works"><i class="fas fa-chevron-right"></i> How It Works</a></li>
                        <li><a href="#testimonials"><i class="fas fa-chevron-right"></i> Testimonials</a></li>
                    </ul>
                </div>
                
                <!-- Tools -->
                <div class="col-lg-2">
                    <h6 class="footer-title">Tools</h6>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Income Tracking</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Expense Management</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Invoice Generator</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Quote Generator</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Savings Goals</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div class="col-lg-4">
                    <h6 class="footer-title">Contact Us</h6>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>support@fintrack.io</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <span>+1 (555) 123-4567</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Business Street, Suite 100<br>New York, NY 10001</span>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p class="footer-copyright">
                        &copy; {{ date('Y') }} FinTrack. All rights reserved. Built for freelancers.
                    </p>
                    <div class="footer-bottom-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');
        
        function openSidebar() {
            mobileSidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSidebar() {
            mobileSidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        mobileMenuBtn.addEventListener('click', openSidebar);
        sidebarClose.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);
        
        // Close sidebar on link click
        document.querySelectorAll('.mobile-nav-links a').forEach(link => {
            link.addEventListener('click', closeSidebar);
        });
        
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
