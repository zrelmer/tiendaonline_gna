@php
    $homeBannerSlides = [
        [
            'image' => 'assets/images/veg-3/home/1.webp',
            'alt' => 'Tecnología e innovación',
            'tag' => 'TECNOLOGÍA',
            'title' => 'Lo último en innovación',
            'subtitle' => 'Laptops, celulares y más',
            'description' => 'Envíos a todo el país. Compra fácil, rápido y seguro.',
        ],
        [
            'image' => 'assets/images/veg-3/home/2.webp',
            'alt' => 'Ofertas en tecnología',
            'tag' => 'OFERTAS',
            'title' => 'Descuentos especiales',
            'subtitle' => 'Equipos seleccionados para ti',
            'description' => 'Aprovecha promociones por tiempo limitado.',
        ],
        [
            'image' => 'assets/images/veg-3/home/3.webp',
            'alt' => 'Laptops y computación',
            'tag' => 'LAPTOPS',
            'title' => 'Potencia para trabajar',
            'subtitle' => 'Modelos para estudio y oficina',
            'description' => 'Rendimiento y garantía en cada compra.',
        ],
        [
            'image' => 'assets/images/veg-3/home/4.webp',
            'alt' => 'Smartphones y accesorios',
            'tag' => 'SMARTPHONES',
            'title' => 'Conectividad al mejor precio',
            'subtitle' => 'Celulares y accesorios originales',
            'description' => 'Encuentra el equipo ideal para tu día a día.',
        ],
        [
            'image' => 'assets/images/veg-3/home/5.webp',
            'alt' => 'Accesorios y periféricos',
            'tag' => 'ACCESORIOS',
            'title' => 'Completa tu setup',
            'subtitle' => 'Audio, periféricos y más',
            'description' => 'Todo lo que necesitas en un solo lugar.',
        ],
    ];
@endphp

<section class="home-section-2 home-section-small section-b-space">
    <div class="container-fluid-lg">
        <div class="row g-4">

            <!-- Banner principal (carrusel 5 imágenes) -->
            <div class="col-xxl-6 col-md-8 home-hero-banner-col">
                <div class="home-main-banner-slider w-100" role="region" aria-label="Promociones destacadas">
                    @foreach ($homeBannerSlides as $slide)
                        <div>
                            <div class="home-contain home-hero-slide">
                                <a href="{{ route('shop.index') }}"
                                   class="home-hero-slide__shop-link"
                                   aria-label="{{ $slide['alt'] }} — Ir a la tienda">
                                    <img src="{{ asset($slide['image']) }}"
                                         class="img-fluid home-hero-slide__image"
                                         alt="{{ $slide['alt'] }}"
                                         loading="eager"
                                         decoding="async"
                                         width="1200"
                                         height="500">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Banner lateral -->
            <div class="col-xxl-3 col-md-4 ratio_medium d-md-block d-none home-side-banner-col">
                <div class="home-contain home-small h-100">
                    <div class="h-100">
                        <img src="{{ asset('assets/images/veg-3/home/6.webp') }}"
                             class="img-fluid bg-img blur-up lazyload" alt="Ofertas">
                    </div>

                    <div class="home-detail text-center p-top-center w-100 text-white">
                        <div>
                            <!-- <h4 class="fw-bold">Ofertas en tecnología</h4>
                            <h5 class="text-center">Descuentos especiales</h5> -->

                            <!-- <a href="{{ route('shop.index') }}"
                               class="btn bg-white theme-color mt-3 home-button mx-auto btn-2">
                                Ver productos
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banners pequeños (columna derecha) -->
            <div class="col-xxl-3 ratio_65 d-xxl-block d-none home-side-stack-col">
                <div class="home-side-stack">
                    <div class="home-side-stack__item home-contain">
                        <a href="{{ route('shop.index') }}">
                            <img src="{{ asset('assets/images/veg-3/home/9.webp') }}"
                                 class="img-fluid bg-img blur-up lazyload" alt="Promociones">
                        </a>

                        <div class="home-detail text-white p-center text-center">
                            <div></div>
                        </div>
                    </div>

                    <div class="home-side-stack__item home-contain">
                        <a href="{{ route('shop.index') }}">
                            <img src="{{ asset('assets/images/veg-3/home/10.webp') }}"
                                 class="img-fluid bg-img blur-up lazyload" alt="Descuentos">
                        </a>

                        <div class="home-detail text-white w-50 p-center-left home-p-sm">
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
