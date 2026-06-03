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
