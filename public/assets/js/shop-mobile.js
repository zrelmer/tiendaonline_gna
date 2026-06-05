/**
 * Fase 9 — Comportamiento móvil tienda (barra inferior, menú, dashboard usuario)
 */
(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    /* Home — hero carrusel: recalcular altura en móvil tras cargar imágenes */
    onReady(function () {
        if (!window.jQuery) {
            return;
        }

        var $heroSlider = jQuery('.home-main-banner-slider');

        if ($heroSlider.length) {
            function refreshHomeHeroSlider() {
                if (!$heroSlider.hasClass('slick-initialized')) {
                    return;
                }
                $heroSlider.slick('setPosition');
            }

            $heroSlider.find('img').on('load', refreshHomeHeroSlider);
            window.addEventListener('load', refreshHomeHeroSlider);

            window.addEventListener('resize', function () {
                if (window.matchMedia('(max-width: 767.98px)').matches) {
                    refreshHomeHeroSlider();
                }
            });
        }
    });

    /* Home — categorías: flechas móvil */
    onReady(function () {
        if (!window.jQuery) {
            return;
        }

        document.querySelectorAll('.category-slider-mobile-shell').forEach(function (shell) {
            var slider = shell.querySelector('.category-slider');
            var prevBtn = shell.querySelector('.category-slider-mobile-nav--prev');
            var nextBtn = shell.querySelector('.category-slider-mobile-nav--next');
            if (!slider || !prevBtn || !nextBtn) {
                return;
            }

            function move(direction) {
                var $slider = jQuery(slider);
                if (!$slider.hasClass('slick-initialized')) {
                    return;
                }
                if (direction === 'prev') {
                    $slider.slick('slickPrev');
                } else {
                    $slider.slick('slickNext');
                }
            }

            prevBtn.addEventListener('click', function () {
                move('prev');
            });
            nextBtn.addEventListener('click', function () {
                move('next');
            });
        });
    });

    /* Menú offcanvas #primaryMenu */
    onReady(function () {
        var primaryMenu = document.getElementById('primaryMenu');
        if (primaryMenu && typeof feather !== 'undefined') {
            primaryMenu.addEventListener('shown.bs.offcanvas', function () {
                feather.replace();
            });
        }
    });

    /* Cuenta header: desplegable por clic en tablet y escritorio (≥768px) */
    onReady(function () {
        var dropdown = document.querySelector('.header-user-dropdown');
        var trigger = document.querySelector('.header-account-trigger');
        var panel = document.getElementById('headerAccountMenu');

        if (!dropdown || !trigger || !panel) {
            return;
        }

        var accountMenuMq = window.matchMedia('(min-width: 768px)');

        function isAccountMenuViewport() {
            return accountMenuMq.matches;
        }

        function closeAccountMenu() {
            dropdown.classList.remove('is-open');
            panel.classList.remove('show');
            trigger.setAttribute('aria-expanded', 'false');
        }

        function openAccountMenu() {
            document.querySelectorAll('.header-user-dropdown.is-open').forEach(function (el) {
                if (el !== dropdown) {
                    el.classList.remove('is-open');
                    var otherPanel = el.querySelector('.onhover-div-login');
                    if (otherPanel) {
                        otherPanel.classList.remove('show');
                    }
                }
            });

            dropdown.classList.add('is-open');
            panel.classList.add('show');
            trigger.setAttribute('aria-expanded', 'true');
        }

        function toggleAccountMenu() {
            if (dropdown.classList.contains('is-open')) {
                closeAccountMenu();
            } else {
                openAccountMenu();
            }
        }

        function onTriggerActivate(event) {
            if (!isAccountMenuViewport()) {
                return;
            }
            event.preventDefault();
            event.stopPropagation();
            toggleAccountMenu();
        }

        trigger.addEventListener('click', onTriggerActivate);

        trigger.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                onTriggerActivate(event);
            }
        });

        panel.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function (event) {
            if (!isAccountMenuViewport() || !dropdown.classList.contains('is-open')) {
                return;
            }
            if (!dropdown.contains(event.target)) {
                closeAccountMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && dropdown.classList.contains('is-open')) {
                closeAccountMenu();
            }
        });

        window.addEventListener('resize', function () {
            if (!isAccountMenuViewport()) {
                closeAccountMenu();
            }
        });
    });

    /* Dashboard usuario — sidebar móvil (#dashboardMenuToggle) */
    onReady(function () {
        var menuToggle = document.getElementById('dashboardMenuToggle');
        var sidebar = document.getElementById('dashboardSidebar');
        if (!menuToggle || !sidebar) {
            return;
        }

        function setSidebarOpen(isOpen) {
            sidebar.classList.toggle('show', isOpen);
            var overlay = document.querySelector('.bg-overlay');
            if (overlay) {
                overlay.classList.toggle('show', isOpen);
            }
            document.body.classList.toggle('dashboard-sidebar-open', isOpen);
            sidebar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        menuToggle.addEventListener('click', function () {
            setSidebarOpen(!sidebar.classList.contains('show'));
        });

        sidebar.querySelectorAll('.close-sidebar').forEach(function (btn) {
            btn.addEventListener('click', function () {
                setSidebarOpen(false);
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && document.body.classList.contains('dashboard-sidebar-open')) {
                setSidebarOpen(false);
            }
        });

        window.addEventListener('resize', function () {
            if (window.matchMedia('(min-width: 992px)').matches) {
                setSidebarOpen(false);
            }
        });

        document.addEventListener('click', function (event) {
            if (!document.body.classList.contains('dashboard-sidebar-open')) {
                return;
            }
            if (event.target.classList.contains('bg-overlay')) {
                setSidebarOpen(false);
            }
        });

        document.querySelectorAll('.user-nav-pills .nav-item .nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 991.98px)').matches) {
                    setSidebarOpen(false);
                }
            });
        });
    });
})();
