<!-- jQuery -->
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<!-- jQuery UI -->
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<!-- Bootstrap (incluye Popper, no necesitas el otro archivo) -->
<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<!-- Feather Icons -->
<script src="{{ asset('assets/js/feather/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/feather/feather-icon.js') }}"></script>
<!-- Lazy Load -->
<script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
<!-- Slick Slider -->
<script src="{{ asset('assets/js/slick/slick.js') }}"></script>
<script src="{{ asset('assets/js/slick/custom_slick.js') }}"></script>
<!-- Cambio: se quita zoom del detalle para evitar comportamiento inestable -->
<!-- Notificaciones -->
<script src="{{ asset('assets/js/bootstrap/bootstrap-notify.min.js') }}"></script>
<!-- Funcionalidades UI -->
<script src="{{ asset('assets/js/auto-height.js') }}"></script>
<script src="{{ asset('assets/js/quantity.js') }}"></script>
<!-- Carrito animado -->
<script src="{{ asset('assets/js/fly-cart.js') }}"></script>
<!-- Animaciones -->
<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<script src="{{ asset('assets/js/custom-wow.js') }}"></script>
<!-- Script principal -->
<script src="{{ asset('assets/js/script.js') }}"></script>
<!-- Configuración del tema -->
<script src="{{ asset('assets/js/theme-setting.js') }}"></script>
<script src="{{ asset('js/detalles.js') }}"></script>
 <!-- Price Range Js -->
 <script src="../assets/js/ion.rangeSlider.min.js"></script>
<script>
    window.clearClientShopStorage = function () {
        localStorage.removeItem('carrito');
        localStorage.removeItem('wishlist');
        sessionStorage.removeItem('tiendaonline_cart_guest_merged');
        sessionStorage.removeItem('tiendaonline_wishlist_guest_merged');
    };
</script>
