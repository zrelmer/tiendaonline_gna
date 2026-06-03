/**
 * Panel admin — comportamiento solo en viewport ≤991px (no altera escritorio).
 */
(function ($) {
    'use strict';

    var MOBILE_MAX = 991;
    var $body = $('body.admin-app');
    if (!$body.length) {
        return;
    }

    var $nav = $('.sidebar-wrapper');
    var $header = $('.page-header');
    var $toggle = $('.sidebar-toggle');
    var $sidebarMenu = $('#sidebar-menu');

    function isMobile() {
        return window.innerWidth <= MOBILE_MAX;
    }

    function sidebarIsOpen() {
        return !$nav.hasClass('close_icon');
    }

    function isAccordionTrigger($link) {
        var href = ($link.attr('href') || '').trim();
        return href === '' || href.indexOf('javascript') === 0;
    }

    function resetSidebarMenuOffset() {
        if ($sidebarMenu.length) {
            $sidebarMenu.css('margin-left', '0');
        }
    }

    function clearSidebarMenuOffset() {
        if ($sidebarMenu.length) {
            $sidebarMenu.css('margin-left', '');
        }
    }

    function syncSidebarAria() {
        if (!$toggle.length) {
            return;
        }
        var open = isMobile() && sidebarIsOpen();
        $toggle.attr('aria-expanded', open ? 'true' : 'false');
        $body.toggleClass('admin-sidebar-open', open);
        $nav.toggleClass('admin-sidebar-mobile-open', open);
        if (open) {
            resetSidebarMenuOffset();
        }
    }

    function toggleSubmenu($link) {
        var $li = $link.closest('.sidebar-list');
        var $sub = $li.children('.sidebar-submenu');
        if (!$sub.length) {
            return false;
        }

        var willOpen = !$li.hasClass('admin-submenu-open');

        $nav.find('.sidebar-list').not($li).removeClass('admin-submenu-open');
        $nav.find('.sidebar-title').not($link).removeClass('active')
            .find('.according-menu i').attr('class', 'ri-arrow-right-s-line');
        $nav.find('.sidebar-submenu').not($sub).stop(true, true).slideUp(200);

        if (willOpen) {
            $li.addClass('admin-submenu-open');
            $link.addClass('active');
            $link.find('.according-menu i').attr('class', 'ri-arrow-down-s-line');
            $sub.stop(true, true).slideDown(200);
        } else {
            $li.removeClass('admin-submenu-open');
            $link.removeClass('active');
            $link.find('.according-menu i').attr('class', 'ri-arrow-right-s-line');
            $sub.stop(true, true).slideUp(200);
        }

        return true;
    }

    function closeAllSubmenus() {
        if (!isMobile()) {
            return;
        }
        $nav.find('.sidebar-list').removeClass('admin-submenu-open');
        $nav.find('.sidebar-title').removeClass('active');
        $nav.find('.according-menu i').attr('class', 'ri-arrow-right-s-line');
        $nav.find('.sidebar-submenu').stop(true, true).slideUp(200);
    }

    function restoreDesktopSidebar() {
        clearSidebarMenuOffset();
        $body.removeClass('admin-sidebar-open');
        $nav.removeClass('admin-sidebar-mobile-open');
        $nav.find('.sidebar-list').removeClass('admin-submenu-open');
        $nav.find('.sidebar-submenu').removeAttr('style');
        $nav.find('.sidebar-title.active').each(function () {
            $(this).next('.sidebar-submenu').show();
        });
    }

    function ensureAccordingMenus() {
        $nav.find('.sidebar-title').each(function () {
            var $link = $(this);
            if ($link.children('.according-menu').length) {
                return;
            }
            if ($link.closest('.sidebar-list').children('.sidebar-submenu').length) {
                $link.append('<div class="according-menu"><i class="ri-arrow-right-s-line"></i></div>');
            }
        });
    }

    function bindMobileSidebarAccordion() {
        var suppressClickUntil = 0;

        $nav.off('click.adminMobileAccordion touchend.adminMobileAccordion', '.sidebar-title');

        $nav.on('touchend.adminMobileAccordion', '.sidebar-title', function (e) {
            if (!isMobile() || !sidebarIsOpen()) {
                return;
            }

            var $link = $(this);
            if (!isAccordionTrigger($link)) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();
            suppressClickUntil = Date.now() + 450;
            toggleSubmenu($link);
        });

        $nav.on('click.adminMobileAccordion', '.sidebar-title', function (e) {
            if (Date.now() < suppressClickUntil) {
                e.preventDefault();
                return;
            }

            if (!isMobile() || !sidebarIsOpen()) {
                return;
            }

            var $link = $(this);
            if (!isAccordionTrigger($link)) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();
            toggleSubmenu($link);
        });
    }

    var wasMobileViewport = isMobile();

    function setupViewportMode() {
        var mobile = isMobile();

        if (mobile) {
            ensureAccordingMenus();
            bindMobileSidebarAccordion();
            if (sidebarIsOpen()) {
                resetSidebarMenuOffset();
            }
            wasMobileViewport = true;
            return;
        }

        $nav.off('click.adminMobileAccordion touchend.adminMobileAccordion', '.sidebar-title');
        if (wasMobileViewport) {
            restoreDesktopSidebar();
            wasMobileViewport = false;
        }
    }

    function afterSidebarToggle() {
        window.setTimeout(function () {
            syncSidebarAria();
            setupViewportMode();
        }, 50);
    }

    $toggle.on('click', afterSidebarToggle);

    $('.sidebar-wrapper .back-btn').on('click', afterSidebarToggle);

    $body.on('click', '.bg-overlay', function () {
        afterSidebarToggle();
    });

    $nav.on('click', 'a[href]', function (e) {
        var href = ($(this).attr('href') || '').trim();
        if (!isMobile() || !href || href.indexOf('javascript') === 0) {
            return;
        }
        $nav.addClass('close_icon');
        $header.addClass('close_icon');
        $('.bg-overlay').remove();
        closeAllSubmenus();
        syncSidebarAria();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && isMobile() && sidebarIsOpen()) {
            $nav.addClass('close_icon');
            $header.addClass('close_icon');
            $('.bg-overlay').remove();
            closeAllSubmenus();
            syncSidebarAria();
        }
    });

    $(window).on('resize overlay', function () {
        syncSidebarAria();
        setupViewportMode();
    });

    setupViewportMode();
    syncSidebarAria();
})(jQuery);
