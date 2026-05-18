@extends('layouts.app')

@section('title', 'Tienda')

@section('content')
<style>
    .shop-page .shop-sidebar {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 1.25rem;
        position: sticky;
        top: 100px;
    }

    .shop-page .shop-toolbar {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 1rem 1.25rem;
    }

    .shop-page .product-grid .product-box-4 {
        height: 100%;
    }

    @media (max-width: 991.98px) {
        .shop-page .shop-sidebar-wrap {
            position: fixed;
            top: 0;
            left: -320px;
            width: 300px;
            height: 100%;
            z-index: 1050;
            background: #fff;
            overflow-y: auto;
            transition: left 0.3s ease;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
        }

        .shop-page .shop-sidebar-wrap.show,
        .shop-page .left-box.show {
            left: 0;
        }

        .shop-page .shop-sidebar {
            position: static;
            top: auto;
            border: none;
            border-radius: 0;
            min-height: 100%;
            padding: 0;
        }

        .shop-page .shop-sidebar .back-button {
            display: block;
            cursor: pointer;
            margin-bottom: 1rem;
            padding: 1rem 1.25rem 0.75rem;
            border-bottom: 1px solid #eee;
        }

        .shop-page .shop-sidebar .back-button h3 {
            font-size: 1rem;
            margin: 0;
            font-weight: 600;
        }
    }

    @media (min-width: 992px) {
        .shop-page .shop-sidebar .back-button {
            display: none;
        }
    }
</style>

<!-- Breadcrumb -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Listado de Productos</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i></a>
                            </li>
                            <li class="breadcrumb-item active">Listado de Productos</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-b-space shop-page shop-section">
    <div class="container-fluid-lg">
        <div class="row g-4">

            {{-- Sidebar filtros --}}
            <div class="col-lg-3">
                <div class="shop-sidebar-wrap left-box">
                    <div class="shop-sidebar shop-left-sidebar">
                        <div class="back-button">
                            <h3>
                                <i class="fa-solid fa-arrow-left"></i>
                                Regresar
                            </h3>
                        </div>

                        <form method="GET"
                              action="{{ route('shop.index') }}"
                              id="shop-filter-form"
                              class="shop-filter-form">
                            <div class="accordion custom-accordion" id="accordionExample">

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseSearch">
                                            <span>Buscar</span>
                                        </button>
                                    </h2>
                                    <div id="collapseSearch" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <input type="text"
                                                   class="form-control"
                                                   name="search"
                                                   value="{{ request('search') }}"
                                                   placeholder="Buscar producto...">
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseCategory">
                                            <span>Categorías</span>
                                        </button>
                                    </h2>
                                    <div id="collapseCategory" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <ul class="category-list custom-padding custom-height">
                                                @foreach ($categories as $category)
                                                    <li>
                                                        <div class="form-check ps-0 m-0 category-list-box">
                                                            <input class="checkbox_animated"
                                                                   type="radio"
                                                                   name="category"
                                                                   value="{{ $category->Id_Categoria }}"
                                                                   id="cat-{{ $category->Id_Categoria }}"
                                                                   {{ request('category') == $category->Id_Categoria ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="cat-{{ $category->Id_Categoria }}">
                                                                <span class="name">{{ $category->Cate_Nombre }}</span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseBrand">
                                            <span>Marcas</span>
                                        </button>
                                    </h2>
                                    <div id="collapseBrand" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <ul class="category-list custom-padding">
                                                @foreach ($brands as $brand)
                                                    <li>
                                                        <div class="form-check ps-0 m-0 category-list-box">
                                                            <input class="checkbox_animated"
                                                                   type="radio"
                                                                   name="brand"
                                                                   value="{{ $brand->Id_Marca }}"
                                                                   id="brand-{{ $brand->Id_Marca }}"
                                                                   {{ request('brand') == $brand->Id_Marca ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="brand-{{ $brand->Id_Marca }}">
                                                                <span class="name">{{ $brand->Nom_Marca }}</span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapsePrice">
                                            <span>Precio</span>
                                        </button>
                                    </h2>
                                    <div id="collapsePrice" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <input type="number"
                                                           name="min_price"
                                                           class="form-control"
                                                           placeholder="Min"
                                                           value="{{ request('min_price') }}"
                                                           min="0"
                                                           step="0.01">
                                                </div>
                                                <div class="col-6">
                                                    <input type="number"
                                                           name="max_price"
                                                           class="form-control"
                                                           placeholder="Max"
                                                           value="{{ request('max_price') }}"
                                                           min="0"
                                                           step="0.01">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseRating">
                                            <span>Rating</span>
                                        </button>
                                    </h2>
                                    <div id="collapseRating" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <ul class="category-list custom-padding">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <li>
                                                        <div class="form-check ps-0 m-0 category-list-box">
                                                            <input class="checkbox_animated"
                                                                   type="radio"
                                                                   name="rating"
                                                                   value="{{ $i }}"
                                                                   id="rating-{{ $i }}"
                                                                   {{ request('rating') == $i ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="rating-{{ $i }}">
                                                                <ul class="rating">
                                                                    @for ($s = 1; $s <= 5; $s++)
                                                                        <li>
                                                                            <i data-feather="star" class="{{ $s <= $i ? 'fill' : '' }}"></i>
                                                                        </li>
                                                                    @endfor
                                                                </ul>
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endfor
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('shop.index') }}" class="btn btn-light w-100">Limpiar filtros</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Productos --}}
            <div class="col-lg-9">
                <div class="shop-toolbar d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h5 class="mb-0">Productos</h5>
                        <p class="text-content mb-0 small">{{ $products->total() }} resultado(s)</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary filter-button d-lg-none">
                        <i class="fa-solid fa-filter me-1"></i> Filtros
                    </button>
                </div>

                @if ($products->isEmpty())
                    <div class="text-center py-5 bg-white rounded-3 border">
                        <h4 class="mb-2">No hay productos</h4>
                        <p class="text-content mb-3">Prueba con otros filtros o limpia la búsqueda.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-animation btn-sm">Ver todos</a>
                    </div>
                @else
                    <div class="row g-3 g-md-4 product-grid">
                        @foreach ($products as $product)
                            @php
                                $imagen = $product->imagenes->sortBy('orden')->first();
                                $imagenUrl = $imagen
                                    ? asset($imagen->url)
                                    : asset('storage/products/default.png');
                                $rating = round($product->comentarios->avg('Rating') ?? 0);
                            @endphp

                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="product-box-4 wow fadeInUp h-100">
                                    <div class="product-image">
                                        <div class="label-flex">
                                            <button type="button"
                                                    class="btn p-0 wishlist btn-wishlist notifi-wishlist"
                                                    onclick="addToWishlist(
                                                        {{ $product->Id_Producto }},
                                                        @js($imagenUrl),
                                                        @js(route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug])),
                                                        @js($product->Prod_Precio),
                                                        @js($product->Prod_Nombre)
                                                    )">
                                                <i class="iconly-Heart icli"></i>
                                            </button>
                                        </div>
                                        <a href="{{ route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug]) }}">
                                            <img src="{{ $imagenUrl }}"
                                                 class="img-fluid blur-up lazyload"
                                                 alt="{{ $product->Prod_Nombre }}">
                                        </a>
                                        <ul class="option">
                                            <li data-bs-toggle="tooltip" title="Ver detalle">
                                                <a href="{{ route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug]) }}">
                                                    <i class="iconly-Show icli"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="product-detail">
                                        <ul class="rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <li>
                                                    <i data-feather="star" class="{{ $i <= $rating ? 'fill' : '' }}"></i>
                                                </li>
                                            @endfor
                                        </ul>
                                        <a href="{{ route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug]) }}">
                                            <h5 class="name text-truncate">{{ $product->Prod_Nombre }}</h5>
                                        </a>
                                        <h5 class="price theme-color">
                                            Q {{ number_format($product->Prod_Precio, 2) }}
                                            @if ($product->Prod_PrecioOferta)
                                                <del>Q {{ number_format($product->Prod_PrecioOferta, 2) }}</del>
                                            @endif
                                        </h5>
                                        <div class="price-qty">
                                            <div class="counter-number">
                                                <div class="counter">
                                                    <div class="qty-left-minus"><i class="fa-solid fa-minus"></i></div>
                                                    <input class="form-control input-number qty-input"
                                                           type="text"
                                                           id="qty-{{ $product->Id_Producto }}"
                                                           value="1">
                                                    <div class="qty-right-plus"><i class="fa-solid fa-plus"></i></div>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    class="buy-button buy-button-2 btn btn-cart"
                                                    onclick="addToCart(
                                                        {{ $product->Id_Producto }},
                                                        @js($imagenUrl),
                                                        @js(route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug])),
                                                        @js($product->Prod_Precio),
                                                        @js($product->Prod_Nombre)
                                                    )">
                                                <i class="iconly-Buy icli text-white m-0"></i>
                                            </button>

                <div class="row g-sm-4 g-3 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-3 row-cols-2 product-list-section">
                    <div>
                        <div class="product-box-3 h-100 wow fadeInUp">
                            <div class="product-box-4 wow fadeInUp">

                        <!-- IMAGEN -->
                        <div class="product-image">

                            <div class="label-flex">

                            </div>
                        </div>

                        <!-- DETALLE -->
                        <div class="product-detail">

                            <!-- RATING -->
                            <ul class="rating">
                                @for ($i = 1; $i <= 5; $i++)
                                @endfor
                            </ul>

                            <!-- NOMBRE -->
                            <!-- PRECIO -->
                            <!-- CANTIDAD + CARRITO -->
                            <div class="price-qty">
                                <div class="counter-number">
                                    <div class="counter">
                                        <div class="qty-left-minus">
                                                {{-- Cambio: se reutiliza quantity.js del template para evitar doble incremento. --}}
                                            <i class="fa-solid fa-minus"></i>
                                        </div>

                                        <input class="form-control input-number qty-input"
                                                type="text"

                                                value="1">

                                        <div class="qty-right-plus">
                                                {{-- Cambio: se reutiliza quantity.js del template para evitar doble incremento. --}}
                                            <i class="fa-solid fa-plus"></i>

                                        </div>
                                    </div>
                                </div>

                                <!-- BOTÓN CARRITO -->
                            </div>

                        @endforeach

                        </div>
                    </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        var form = document.getElementById('shop-filter-form');
        var debounceTimer = null;

        function closeMobileSidebar() {
            $('.shop-page .shop-sidebar-wrap, .bg-overlay').removeClass('show');
        }

        function submitFilters() {
            if (!form) {
                return;
            }
            closeMobileSidebar();
            form.submit();
        }

        function debouncedSubmit() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(submitFilters, 500);
        }

        if (form) {
            form.querySelectorAll('input[type="radio"]').forEach(function (input) {
                input.addEventListener('change', submitFilters);
            });

            form.querySelectorAll('input[name="search"], input[name="min_price"], input[name="max_price"]').forEach(function (input) {
                input.addEventListener('input', debouncedSubmit);
                input.addEventListener('change', debouncedSubmit);
            });
        }

        $('.shop-page .filter-button').on('click', function () {
            $('.shop-page .shop-sidebar-wrap, .bg-overlay').addClass('show');
        });
        $('.shop-page .back-button, .bg-overlay').on('click', closeMobileSidebar);
    });
</script>
@endpush

