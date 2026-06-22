/**
 * Safak Navbar - Frontend JavaScript
 * Handles: sticky scroll, mobile menu, language switcher dropdown
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.getElementById('safak-navbar');
        if (!navbar) return;

        const hamburger = navbar.querySelector('.safak-navbar__hamburger');
        const mobileMenu = navbar.querySelector('.safak-navbar__mobile-menu');
        const langBtns = navbar.querySelectorAll('.safak-navbar__lang-btn');

        // ===========================
        // Sticky Scroll Effect
        // ===========================
        if (navbar.classList.contains('safak-navbar--sticky')) {
            document.body.classList.add('safak-navbar-sticky-active');

            let lastScroll = 0;
            let ticking = false;

            function handleScroll() {
                const scrollY = window.scrollY;

                if (scrollY > 10) {
                    navbar.classList.add('safak-navbar--scrolled');
                } else {
                    navbar.classList.remove('safak-navbar--scrolled');
                }

                lastScroll = scrollY;
                ticking = false;
            }

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(handleScroll);
                    ticking = true;
                }
            }, { passive: true });
        }

        // ===========================
        // Mobile Menu Toggle
        // ===========================
        if (hamburger && mobileMenu) {
            hamburger.addEventListener('click', function () {
                const isOpen = hamburger.classList.contains('safak-navbar__hamburger--active');

                if (isOpen) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });

            // Close on mobile menu link click
            const mobileLinks = mobileMenu.querySelectorAll('.menu-item a');
            mobileLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    closeMobileMenu();
                });
            });

            // Close on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                    closeAllDropdowns();
                }
            });
        }

        function openMobileMenu() {
            hamburger.classList.add('safak-navbar__hamburger--active');
            hamburger.setAttribute('aria-expanded', 'true');
            mobileMenu.classList.add('safak-navbar__mobile-menu--open');
            mobileMenu.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            hamburger.classList.remove('safak-navbar__hamburger--active');
            hamburger.setAttribute('aria-expanded', 'false');
            mobileMenu.classList.remove('safak-navbar__mobile-menu--open');
            mobileMenu.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        // ===========================
        // Language Switcher Dropdown
        // ===========================
        langBtns.forEach(function (btn) {
            const switcher = btn.closest('.safak-navbar__lang-switcher');
            const dropdown = switcher.querySelector('.safak-navbar__lang-dropdown');

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = btn.getAttribute('aria-expanded') === 'true';

                // Close all other dropdowns first
                closeAllDropdowns();

                if (!isOpen) {
                    btn.setAttribute('aria-expanded', 'true');
                    dropdown.classList.add('safak-navbar__lang-dropdown--open');
                    dropdown.setAttribute('aria-hidden', 'false');
                }
            });
        });

        function closeAllDropdowns() {
            langBtns.forEach(function (btn) {
                const switcher = btn.closest('.safak-navbar__lang-switcher');
                const dropdown = switcher.querySelector('.safak-navbar__lang-dropdown');
                btn.setAttribute('aria-expanded', 'false');
                dropdown.classList.remove('safak-navbar__lang-dropdown--open');
                dropdown.setAttribute('aria-hidden', 'true');
            });
        }

        // Close dropdown on outside click
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.safak-navbar__lang-switcher')) {
                closeAllDropdowns();
            }
        });

        // ===========================
        // Resize Handler
        // ===========================
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (window.innerWidth > 960) {
                    closeMobileMenu();
                }
            }, 150);
        });
    });
})();
