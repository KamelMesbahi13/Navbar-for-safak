/**
 * Via Firenze Navbar - Luxury Minimalist Frontend Script
 * Handles: Sticky scroll, Search overlay toggle, Mobile drawer navigation, Keyboard accessibility
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var navbar = document.getElementById('via-firenze-navbar');
        if (!navbar) return;

        var isSticky = navbar.classList.contains('via-firenze-navbar--sticky');
        var hamburger = navbar.querySelector('.via-firenze-navbar__hamburger');
        var mobileDrawer = document.getElementById('via-firenze-mobile-drawer');
        var mobileClose = mobileDrawer ? mobileDrawer.querySelector('.via-firenze-navbar__mobile-close') : null;

        var searchToggle = navbar.querySelector('.via-firenze-navbar__search-toggle');
        var searchOverlay = navbar.querySelector('.via-firenze-navbar__search-overlay');
        var searchClose = searchOverlay ? searchOverlay.querySelector('.via-firenze-navbar__search-close') : null;
        var searchInput = searchOverlay ? searchOverlay.querySelector('.via-firenze-navbar__search-input') : null;

        // =====================================================================
        // Scroll Effect: Transparent at top -> Solid White on scroll down
        // =====================================================================
        if (isSticky) {
            document.body.classList.add('via-firenze-navbar-sticky-active');
        }

        var ticking = false;
        function onScroll() {
            var scrollY = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollY > 15) {
                navbar.classList.add('via-firenze-navbar--scrolled');
            } else {
                navbar.classList.remove('via-firenze-navbar--scrolled');
            }
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(onScroll);
                ticking = true;
            }
        }, { passive: true });

        onScroll(); // initialize on load

        // =====================================================================
        // Search Overlay Controls
        // =====================================================================
        function openSearch() {
            if (!searchOverlay) return;
            searchOverlay.classList.add('is-active');
            searchOverlay.setAttribute('aria-hidden', 'false');
            if (searchToggle) {
                searchToggle.setAttribute('aria-expanded', 'true');
            }
            if (searchInput) {
                setTimeout(function () {
                    searchInput.focus();
                }, 100);
            }
        }

        function closeSearch() {
            if (!searchOverlay) return;
            searchOverlay.classList.remove('is-active');
            searchOverlay.setAttribute('aria-hidden', 'true');
            if (searchToggle) {
                searchToggle.setAttribute('aria-expanded', 'false');
            }
        }

        if (searchToggle && searchOverlay) {
            searchToggle.addEventListener('click', function (e) {
                e.preventDefault();
                var isOpen = searchOverlay.classList.contains('is-active');
                if (isOpen) {
                    closeSearch();
                } else {
                    openSearch();
                }
            });
        }

        if (searchClose) {
            searchClose.addEventListener('click', function (e) {
                e.preventDefault();
                closeSearch();
            });
        }

        // =====================================================================
        // Mobile Drawer Controls
        // =====================================================================
        function openMobileMenu() {
            if (!mobileDrawer) return;
            closeSearch();
            if (hamburger) {
                hamburger.classList.add('via-firenze-navbar__hamburger--active');
                hamburger.setAttribute('aria-expanded', 'true');
            }
            mobileDrawer.classList.add('via-firenze-navbar__mobile-menu--open');
            mobileDrawer.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            if (!mobileDrawer) return;
            if (hamburger) {
                hamburger.classList.remove('via-firenze-navbar__hamburger--active');
                hamburger.setAttribute('aria-expanded', 'false');
            }
            mobileDrawer.classList.remove('via-firenze-navbar__mobile-menu--open');
            mobileDrawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        if (hamburger && mobileDrawer) {
            hamburger.addEventListener('click', function (e) {
                e.preventDefault();
                var isOpen = mobileDrawer.classList.contains('via-firenze-navbar__mobile-menu--open');
                if (isOpen) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });

            if (mobileClose) {
                mobileClose.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeMobileMenu();
                });
            }

            // Close on link click inside drawer
            var mobileLinks = mobileDrawer.querySelectorAll('a');
            mobileLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    closeMobileMenu();
                });
            });
        }

        // =====================================================================
        // Global Keyboard Handler (Escape key)
        // =====================================================================
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                closeSearch();
                closeMobileMenu();
            }
        });

        // =====================================================================
        // Viewport Resize Handler
        // =====================================================================
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (window.innerWidth > 992) {
                    closeMobileMenu();
                }
            }, 150);
        });
    });
})();
