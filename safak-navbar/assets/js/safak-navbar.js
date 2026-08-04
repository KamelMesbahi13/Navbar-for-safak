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
            function toggleMenu(e) {
                e.preventDefault();
                e.stopPropagation();

                const isOpen = hamburger.classList.contains('safak-navbar__hamburger--active');

                if (isOpen) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            }

            hamburger.addEventListener('click', toggleMenu);

            // Also listen for touchend on iOS — some mobile browsers
            // don't reliably fire 'click' on dynamically-shown buttons
            hamburger.addEventListener('touchend', function (e) {
                e.preventDefault();
                toggleMenu(e);
            });

            // Close on mobile menu link or CTA button click
            var mobileLinks = mobileMenu.querySelectorAll('.menu-item a, .safak-navbar__cta-btn--mobile');
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
            if (!hamburger || !mobileMenu) return;
            hamburger.classList.add('safak-navbar__hamburger--active');
            hamburger.setAttribute('aria-expanded', 'true');
            mobileMenu.classList.add('safak-navbar__mobile-menu--open');
            mobileMenu.setAttribute('aria-hidden', 'false');
            // Force inline styles as fallback in case CSS classes are overridden
            mobileMenu.style.display = 'block';
            mobileMenu.style.visibility = 'visible';
            mobileMenu.style.transform = 'translateX(0)';
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            if (!hamburger || !mobileMenu) return;
            hamburger.classList.remove('safak-navbar__hamburger--active');
            hamburger.setAttribute('aria-expanded', 'false');
            mobileMenu.classList.remove('safak-navbar__mobile-menu--open');
            mobileMenu.setAttribute('aria-hidden', 'true');
            // Remove inline overrides so CSS takes back control
            mobileMenu.style.visibility = '';
            mobileMenu.style.transform = '';
            document.body.style.overflow = '';
            // Delay removing display to allow the close transition to play
            setTimeout(function () {
                if (!hamburger.classList.contains('safak-navbar__hamburger--active')) {
                    mobileMenu.style.display = '';
                }
            }, 400);
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
