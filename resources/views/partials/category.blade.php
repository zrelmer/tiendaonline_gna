@php
    $categoryPlaceholders = [
        asset('assets/images/veg-3/category/1.png'),
        asset('assets/images/veg-3/category/2.png'),
        asset('assets/images/veg-3/category/3.png'),
        asset('assets/images/veg-3/category/4.png'),
        asset('assets/images/veg-3/category/5.png'),
        asset('assets/images/veg-3/category/6.png'),
        asset('assets/images/veg-3/category/7.png'),
        asset('assets/images/veg-3/category/8.png'),
    ];
    $circleClasses = ['circle-1', 'circle-2', 'circle-3', 'circle-4'];
@endphp

<section class="category-section-2">
    <div class="container-fluid-lg">
        <div class="title">
            <h2>Comprar por categorías</h2>
        </div>
        <div class="row">
            <div class="col-12">
                @if ($navCategories->isEmpty())
                    <p class="text-content text-center mb-0">
                        Próximamente nuevas categorías.
                        <a href="{{ route('shop.index') }}" class="theme-color fw-semibold">Ver tienda</a>
                    </p>
                @else
                    <div class="category-slider-mobile-shell">
                        <button type="button"
                                class="category-slider-mobile-nav category-slider-mobile-nav--prev"
                                aria-label="Ver categorías anteriores">
                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <div class="category-slider arrow-slider">
                        @foreach ($navCategories as $index => $categoria)
                            @php
                                $imagenUrl = $categoria->Cate_Imagen
                                    ? asset($categoria->Cate_Imagen)
                                    : $categoryPlaceholders[$index % count($categoryPlaceholders)];
                                $circleClass = $circleClasses[$index % count($circleClasses)];
                                $delay = number_format($index * 0.05, 2, '.', '');
                            @endphp
                            <div>
                                <div class="shop-category-box border-0 wow fadeIn"
                                     @if ($index > 0) data-wow-delay="{{ $delay }}s" @endif>
                                    <a href="{{ route('shop.index', ['category' => $categoria->Id_Categoria]) }}"
                                       class="{{ $circleClass }}">
                                        <img src="{{ $imagenUrl }}"
                                             class="img-fluid blur-up lazyload"
                                             alt="{{ $categoria->Cate_Nombre }}">
                                    </a>
                                    <div class="category-name">
                                        <h6>{{ $categoria->Cate_Nombre }}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <button type="button"
                                class="category-slider-mobile-nav category-slider-mobile-nav--next"
                                aria-label="Ver más categorías">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
