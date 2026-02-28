// FinTrack Portal — Main UI Script

document.addEventListener('DOMContentLoaded', function () {

    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggle   = document.getElementById('sidebarToggle');

    if (!sidebar || !toggle) return;

    // ─────────────────────────────────────────────────────
    //  MOBILE: slide-over overlay behaviour (< 992px)
    // ─────────────────────────────────────────────────────
    function openMobileSidebar() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    // Close mobile sidebar when viewport grows past breakpoint
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) closeMobileSidebar();
    });

    // ─────────────────────────────────────────────────────
    //  DESKTOP: collapse / expand  (≥ 992px)
    //  State is persisted via localStorage.
    //  CSS drives the layout via .sidebar.collapsed sibling selector —
    //  no extra class is added to .main-content.
    // ─────────────────────────────────────────────────────
    const STORAGE_KEY = 'ft_sidebar_collapsed';

    function isDesktop() {
        return window.innerWidth >= 992;
    }

    /* Collapse sidebar to icon-only rail */
    function collapseSidebar() {
        sidebar.classList.add('collapsed');
        localStorage.setItem(STORAGE_KEY, '1');

        // Close any open Bootstrap sub-menus so they don't ghost-show
        sidebar.querySelectorAll('.collapse.show').forEach(function (el) {
            bootstrap.Collapse.getOrCreateInstance(el).hide();
        });

        // Enable hover tooltips on nav links
        enableNavTooltips();
    }

    /* Expand sidebar to full width */
    function expandSidebar() {
        sidebar.classList.remove('collapsed');
        localStorage.setItem(STORAGE_KEY, '0');

        // Remove tooltips — text labels are now visible
        disableNavTooltips();
    }

    function toggleDesktopCollapse() {
        if (sidebar.classList.contains('collapsed')) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    }

    /* Restore last saved state on page load */
    if (isDesktop() && localStorage.getItem(STORAGE_KEY) === '1') {
        // Add without transition so it's instant on first load
        sidebar.style.transition = 'none';
        sidebar.classList.add('collapsed');
        enableNavTooltips();
        // Re-enable transition after a tick
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                sidebar.style.transition = '';
            });
        });
    }

    /* Hamburger click */
    toggle.addEventListener('click', function () {
        if (isDesktop()) {
            toggleDesktopCollapse();
        } else {
            // Mobile: open/close slide-over
            sidebar.classList.contains('open') ? closeMobileSidebar() : openMobileSidebar();
        }
    });

    // ─────────────────────────────────────────────────────
    //  SUBMENU PARENT LINKS — when sidebar is collapsed,
    //  clicking an Income / Expenses / Debts toggle
    //  navigates to its first child route instead of
    //  expanding the accordion (which would be hidden anyway).
    // ─────────────────────────────────────────────────────
    sidebar.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!isDesktop() || !sidebar.classList.contains('collapsed')) return;

            e.preventDefault();
            e.stopPropagation();

            // Find the first navigable child link in the submenu
            var targetSel = link.getAttribute('href'); // e.g. "#incomeMenu"
            if (!targetSel) return;
            var submenu = document.querySelector(targetSel);
            if (!submenu) return;
            var firstLink = submenu.querySelector('.nav-link[href]');
            if (firstLink && firstLink.getAttribute('href') && firstLink.getAttribute('href') !== '#') {
                window.location.href = firstLink.getAttribute('href');
            } else {
                // Fallback: expand the sidebar so user can navigate
                expandSidebar();
            }
        });
    });

    // ─────────────────────────────────────────────────────
    //  TOOLTIPS for collapsed nav items
    //  Bootstrap 5 Tooltip — placed to the right of icon
    // ─────────────────────────────────────────────────────
    function enableNavTooltips() {
        sidebar.querySelectorAll('.sidebar-menu .nav-link').forEach(function (link) {
            // Grab label from the <span> inside the link
            var span = link.querySelector('span');
            var label = span ? span.textContent.trim() : '';
            if (!label) return;

            // Avoid double-init
            if (bootstrap.Tooltip.getInstance(link)) return;

            bootstrap.Tooltip.getOrCreateInstance(link, {
                title:     label,
                placement: 'right',
                trigger:   'hover',
                container: 'body',
                customClass: 'ft-sidebar-tooltip'
            });
        });
    }

    function disableNavTooltips() {
        sidebar.querySelectorAll('.sidebar-menu .nav-link').forEach(function (link) {
            var tt = bootstrap.Tooltip.getInstance(link);
            if (tt) tt.dispose();
        });
    }

    // Hide tooltip before navigation to avoid stale tooltip after page load
    sidebar.querySelectorAll('.sidebar-menu .nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            var tt = bootstrap.Tooltip.getInstance(link);
            if (tt) tt.hide();
        });
    });

    // ─────────────────────────────────────────────────────
    //  AUTO-DISMISS flash alerts after 5 s
    // ─────────────────────────────────────────────────────
    document.querySelectorAll('.alert.alert-dismissible').forEach(function (el) {
        setTimeout(function () {
            if (el.isConnected) {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }
        }, 5000);
    });

    // ─────────────────────────────────────────────────────
    //  CONFIRM DELETE  (add class="btn-delete" to trigger)
    // ─────────────────────────────────────────────────────
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this item?\nThis action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // ─────────────────────────────────────────────────────
    //  BOOTSTRAP HTML5 form validation
    // ─────────────────────────────────────────────────────
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ─────────────────────────────────────────────────────
    //  CHART.JS global defaults
    // ─────────────────────────────────────────────────────
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif";
        Chart.defaults.color = '#64748B';
    }

});
