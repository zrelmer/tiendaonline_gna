/**=====================
     Quantity js
     Cambio: delegación en document para que +/- funcione en HTML insertado
     después de cargar (p. ej. lista de deseos desde localStorage).
==========================**/
$(document).on('click', '.qty-right-plus', function () {
    var $input = $(this).prev('.qty-input');
    if (!$input.length) {
        $input = $(this).prev();
    }
    var val = parseInt($input.val(), 10) || 1;
    if (val < 9) {
        $input.val(val + 1);
    }
});

$(document).on('click', '.qty-left-minus', function () {
    var $input = $(this).next('.qty-input');
    if (!$input.length) {
        $input = $(this).next();
    }
    var val = parseInt($input.val(), 10) || 1;
    if (val > 1) {
        $input.val(val - 1);
    }
});
