/**
 * Ajusta el carrusel hero a la altura de la celda grid del home.
 */
(function ($) {
    'use strict';

    var resizeTimer;

    function resetHomeHeroLayout($row, $slider) {
        if ($slider.length) {
            $slider.css('height', '');
            $slider.find('.slick-list, .slick-track, .slick-slide, .slick-slide > div, .home-contain, .home-hero-slide').css('height', '');
        }

        $row.children().css('min-height', '');
    }

    function syncHomeHeroLayout() {
        var $row = $('.home-page-sections .home-section-2 > .container-fluid-lg > .row.g-4');
        var $heroCol = $row.find('.home-hero-banner-col');
        var $slider = $heroCol.find('.home-main-banner-slider');

        if (!$row.length || !$slider.length) {
            return;
        }

        if (!window.matchMedia('(min-width: 768px)').matches) {
            resetHomeHeroLayout($row, $slider);

            return;
        }

        resetHomeHeroLayout($row, $slider);

        var targetH = 0;

        $row.children().each(function () {
            var $col = $(this);

            if ($col.is(':visible') && $col.css('display') !== 'none') {
                targetH = Math.max(targetH, $col.innerHeight());
            }
        });

        if (targetH < 240) {
            return;
        }

        $slider.css('height', targetH + 'px');
        $slider.find('.slick-list, .slick-track, .slick-slide, .slick-slide > div, .home-contain, .home-hero-slide').css('height', targetH + 'px');
    }

    function scheduleSync() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(syncHomeHeroLayout, 80);
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
