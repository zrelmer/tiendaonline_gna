 /**=====================
     Fly Cart js
     Cambio: delegación en document para botones .btn-cart añadidos dinámicamente
     (p. ej. lista de deseos).
==========================**/
 $(document).on('click', '.btn-cart', function () {
     if ($(window).width() > 768) {
         var cart = $('.button-item');
     } else {
         var cart = $('.mobile-cart ul li a .icli.fly-cate');
     }
    if (!cart.length) {
        return;
    }
    var cartOffset = cart.offset();
    if (!cartOffset) {
        return;
    }
    var imgtodrag = $(this).closest('.product-box-4, .deal-box').find(".product-image img, .category-image img").eq(0);
    if (!imgtodrag.length) {
        return;
    }
    var imgOffset = imgtodrag.offset();
    if (!imgOffset) {
        return;
    }
    var imgclone = imgtodrag.clone()
        .offset({
            top: imgOffset.top,
            left: imgOffset.left
        })
        .css({
            'opacity': '0.5',
            'position': 'absolute',
            'height': '130px',
            'width': '130px',
            'z-index': '100'
        })
        .appendTo($('body'))
        .animate({
            'top': cartOffset.top + 10,
            'left': cartOffset.left + 10,
            'width': 75,
            'height': 75
        }, 1000, 'easeInOutExpo');

    imgclone.animate({
        'width': 0,
        'height': 0
    }, function () {
        $(this).detach();
    });
});