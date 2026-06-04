/**
 * Iguala la altura del banner principal con los banners laterales del home.
 */
(function ($) {
    'use strict';

    var resizeTimer;

    function resetHeroHeights($slider) {
        $slider.css('height', '');
        $slider.find('.slick-list, .slick-track, .slick-slide, .slick-slide > div, .home-contain').css('height', '');
    }

    function syncHomeHeroBannerHeight() {
        var $row = $('.home-page-sections .home-section-2 > .container-fluid-lg > .row.g-4');
        var $heroCol = $row.find('.home-hero-banner-col');
        var $slider = $heroCol.find('.home-main-banner-slider');

        if (!$row.length || !$slider.length) {
            return;
        }

        if (!window.matchMedia('(min-width: 768px)').matches) {
            resetHeroHeights($slider);

            return;
        }

        var maxH = 0;

        $row.children().not('.home-hero-banner-col').each(function () {
            var $col = $(this);

            if ($col.is(':visible') && $col.css('display') !== 'none') {
                maxH = Math.max(maxH, $col.outerHeight());
            }
        });

        if (maxH < 240) {
            return;
        }

        $slider.css('height', maxH + 'px');
        $slider.find('.slick-list, .slick-track, .slick-slide, .slick-slide > div').css('height', maxH + 'px');
        $slider.find('.home-contain').css('height', maxH + 'px');
    }

    function scheduleSync() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(syncHomeHeroBannerHeight, 80);
    }

    $(window).on('load resize', scheduleSync);

    $(document).on('init reInit setPosition', '.home-main-banner-slider', scheduleSync);

    $('.home-page-sections .home-section-2 img').on('load', scheduleSync);

    $(document).ready(function () {
        scheduleSync();
        setTimeout(scheduleSync, 400);
        setTimeout(scheduleSync, 1200);
    });
})(jQuery);
