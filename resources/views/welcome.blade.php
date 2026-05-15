@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<!-- home section start -->
@include('partials.home')
    <!-- Home Section End -->

    <!-- Category Section Start -->
    @include('partials.category')
    <!-- Category Section End -->

    <!-- Value Section Start -->
    @include('partials.value')
    <!-- Value Section End -->

    <!-- Product Section Start -->
    <!-- Product Section End -->

    <!-- Banner Section Start -->
    @include('partials.banner1')
    <!-- Banner Section End -->

    <!-- Banner Section Start -->
    @include('partials.banner2')
    <!-- Banner Section End -->

    <!-- Product Section Start -->
    <section class="product-section">
        <div class="container-fluid-lg">
            <div class="title">
                <h2>Top Productos</h2>
            </div>
            @foreach ($topSellingProducts->chunk(6) as $chunk)
            <div class="row">
            @foreach ($chunk as $product)

                @php
                    $imagen = $product->imagenes->sortBy('orden')->first();
                    $imagenUrl = $imagen ? asset($imagen->url) : asset('storage/products/default.png');
                @endphp

                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="product-box-4 wow fadeInUp">

                        <!-- IMAGEN -->
                        <div class="product-image">

                            <div class="label-flex">
                                <button class="btn p-0 wishlist btn-wishlist notifi-wishlist"
                                    onclick="addToWishlist(
                                        {{ $product->Id_Producto }},
                                        {{-- Cambio: @js escapa correctamente comillas en parámetros JS inline. --}}
                                        @js($imagenUrl),
                                        @js(route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug])),
                                        @js($product->Prod_Precio),
                                        @js($product->Prod_Nombre)
                                    )">
                                    <i class="iconly-Heart icli"></i>
                                </button>
                            </div>

                            <!-- IMAGEN PRODUCTO -->
                            <a href="{{ route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug]) }}">
                                <img src="{{ $imagenUrl }}"
                                        class="img-fluid"
                                        alt="{{ $product->Prod_Nombre }}">
                            </a>

                            <!-- OPCIONES -->
                            <ul class="option">
                                <li data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle">
                                    <a href="{{ route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug]) }}">
                                        <i class="iconly-Show icli"></i>
                                    </a>
                                </li>
                            </ul>

                        </div>

                        <!-- DETALLE -->
                        <div class="product-detail">

                            <!-- RATING -->
                            <ul class="rating">
                                @php
                                    $rating = round($product->comentarios->avg('Rating') ?? 0);
                                @endphp

                                @for ($i = 1; $i <= 5; $i++)
                                    <li>
                                        <i data-feather="star" class="{{ $i <= $rating ? 'fill' : '' }}"></i>
                                    </li>
                                @endfor
                            </ul>

                            <!-- NOMBRE -->
                            <a href="{{ route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug]) }}">
                                <h5 class="name">{{ $product->Prod_Nombre }}</h5>
                            </a>

                            <!-- PRECIO -->
                            <h5 class="price theme-color">
                                Q {{ number_format($product->Prod_Precio, 2) }}
                                <del>Q {{ number_format($product->Prod_PrecioOferta, 2) }}</del>
                            </h5>

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
                                                id="qty-{{ $product->Id_Producto }}"
                                                value="1">

                                        <div class="qty-right-plus">
                                                {{-- Cambio: se reutiliza quantity.js del template para evitar doble incremento. --}}
                                            <i class="fa-solid fa-plus"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- BOTÓN CARRITO -->
                                <button class="buy-button buy-button-2 btn btn-cart"
                                    onclick="addToCart(
                                        {{ $product->Id_Producto }},
                                        {{-- Cambio: @js escapa correctamente comillas en parámetros JS inline. --}}
                                        @js($imagenUrl),
                                        @js(route('product.details', ['idproducto' => $product->Id_Producto, 'slug_producto' => $product->Prod_Slug])),
                                        @js($product->Prod_Precio),
                                        @js($product->Prod_Nombre)
                                    )">
                                    <i class="iconly-Buy icli text-white m-0"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            @endforeach
            </div>
            @endforeach
        </div>
    </section>
    <!-- Product Section End -->

    <!-- Newsletter Section Start -->
    @include('partials.news')
    <!-- Newsletter Section End -->
@endsection
