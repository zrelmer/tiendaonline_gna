<section class="banner-section">
    <div class="container-fluid-lg">
        <div class="row gy-xl-0 gy-3">

            <!-- Banner 1 -->
            <div class="col-xl-6">
                <div class="banner-contain-3 hover-effect">
                    <img src="{{ asset('assets/images/veg-3/banner/1.webp') }}"
                         class="bg-img img-fluid" alt="Tecnología premium">

                    <div class="banner-detail banner-details-dark text-white p-center-left w-50 position-relative mend-auto">
                        <div>
                            <h6 class="ls-expanded text-uppercase">Premium</h6>
                            <h3 class="mb-sm-3 mb-1">Materiales de alta calidad</h3>
                            <h4>Garantía y envío inmediato</h4>

                            <a href="{{ route('shop.index') }}"
                               class="btn theme-color bg-white btn-md fw-bold mt-sm-3 mt-1 mend-auto">
                                Comprar ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner 2 -->
            <div class="col-xl-6">
                <div class="banner-contain-3 hover-effect">
                    <img src="{{ asset('assets/images/veg-3/banner/2.webp') }}"
                         class="bg-img img-fluid" alt="Ofertas especiales">

                    <div class="banner-detail text-dark p-center-left w-50 position-relative mend-auto">
                        <div>
                            <h6 class="ls-expanded text-uppercase">Disponible</h6>
                            <h3 class="mb-sm-3 mb-1">Servicio de Streaming</h3>
                            <h4 class="text-content">Ofertas especiales </h4>

                            <a href="{{ route('shop.index') }}"
                               class="btn theme-bg-color text-white btn-md fw-bold mt-sm-3 mt-1 mend-auto">
                                Comprar ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
