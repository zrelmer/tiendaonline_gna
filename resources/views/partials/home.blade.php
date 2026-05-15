<section class="home-section-2 home-section-small section-b-space">
    <div class="container-fluid-lg">
        <div class="row g-4">

            <!-- Banner principal -->
            <div class="col-xxl-6 col-md-8">
                <div class="home-contain h-100">
                    <img src="{{ asset('assets/images/veg-3/home/1.png') }}"
                         class="img-fluid bg-img blur-up lazyload" alt="Tecnología">

                    <div class="home-detail home-width p-center-left position-relative">
                        <div>
                            <h6 class="ls-expanded theme-color">TECNOLOGÍA</h6>
                            <h1 class="fw-bold w-100">Lo último en innovación</h1>
                            <h3 class="text-content fw-light">Laptops, celulares y más</h3>
                            <p class="d-sm-block d-none">
                                Envíos a todo el país. Compra fácil, rápido y seguro.
                            </p>

                            <a href="{{ route('shop.index') }}"
                               class="btn mt-sm-4 btn-2 theme-bg-color text-white mend-auto btn-2-animation">
                                Comprar ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner lateral -->
            <div class="col-xxl-3 col-md-4 ratio_medium d-md-block d-none">
                <div class="home-contain home-small h-100">
                    <div class="h-100">
                        <img src="{{ asset('assets/images/veg-3/home/2.png') }}"
                             class="img-fluid bg-img blur-up lazyload" alt="Ofertas">
                    </div>

                    <div class="home-detail text-center p-top-center w-100 text-white">
                        <div>
                            <h4 class="fw-bold">Ofertas en tecnología</h4>
                            <h5 class="text-center">Descuentos especiales</h5>

                            <a href="shop-left-sidebar.html"
                               class="btn bg-white theme-color mt-3 home-button mx-auto btn-2">
                                Ver productos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banners pequeños -->
            <div class="col-xxl-3 ratio_65 d-xxl-block d-none">
                <div class="row g-3">

                    <div class="col-xxl-12 col-sm-6">
                        <div class="home-contain">
                            <a href="shop-left-sidebar.html">
                                <img src="{{ asset('assets/images/veg-3/home/3.png') }}"
                                     class="img-fluid bg-img blur-up lazyload" alt="Promociones">
                            </a>

                            <div class="home-detail text-white p-center text-center">
                                <div>
                                    <h4 class="text-center">Ofertas exclusivas</h4>
                                    <h5 class="text-center">Fin de semana</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-12 col-sm-6">
                        <div class="home-contain">
                            <a href="shop-left-sidebar.html">
                                <img src="{{ asset('assets/images/veg-3/home/4.png') }}"
                                     class="img-fluid bg-img blur-up lazyload" alt="Descuentos">
                            </a>

                            <div class="home-detail text-white w-50 p-center-left home-p-sm">
                                <div>
                                    <h4 class="fw-bold">Compra segura</h4>
                                    <h5>Grandes descuentos</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
