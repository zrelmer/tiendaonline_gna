@extends('layouts.app')

@section('title', $producto->Prod_Nombre)

@section('content')
<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>{{ $producto->Prod_Nombre }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>

                            <li class="breadcrumb-item active">{{ $producto->Prod_Nombre }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

    <!-- Product Left Sidebar Start -->
<section class="product-section">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-xxl-9 col-xl-8 col-lg-7 wow fadeInUp">
                <div class="row g-4">
                    <div class="col-xl-6 wow fadeInUp">
                        @php
                            $galeriaImagenes = $producto->imagenes
                                ->sortBy('orden')
                                ->map(fn ($img) => asset($img->url))
                                ->values();

                            if ($galeriaImagenes->isEmpty()) {
                                $galeriaImagenes = collect([$imagenUrl]);
                            }
                        @endphp
                        <div class="product-left-box">
                            <div class="row g-sm-4 g-2">
                                <div class="col-12">
                                    <div class="product-main no-arrow">
                                        @foreach ($galeriaImagenes as $index => $imgUrl)
                                            <div>
                                                <div class="slider-image">
                                                    <img src="{{ $imgUrl }}"
                                                         @if ($index === 0) id="img-1" @endif
                                                         {{-- Cambio: se eliminan atributos/clases de zoom para desactivar el efecto. --}}
                                                         class="img-fluid blur-up lazyload"
                                                         alt="{{ $producto->Prod_Nombre }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="left-slider-image left-slider no-arrow slick-top">
                                        @foreach ($galeriaImagenes as $imgUrl)
                                            <div>
                                                <div class="sidebar-image">
                                                    <img src="{{ $imgUrl }}" class="img-fluid blur-up lazyload" alt="{{ $producto->Prod_Nombre }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 wow fadeInUp">
                        <div class="right-box-contain">
                            <h2 class="name">{{ $producto->Prod_Nombre }}</h2>
                            <div class="price-rating">
                                <h3 class="theme-color price">
                                    Q{{ number_format($producto->Prod_Precio, 2) }}
                                    @if (!is_null($producto->Prod_PrecioOferta))
                                        <del class="text-content">Q{{ number_format($producto->Prod_PrecioOferta, 2) }}</del>
                                    @endif
                                </h3>
                                <div class="product-rating custom-rate">
                                    <!-- RATING -->
                                    <ul class="rating">
                                        @php
                                            $rating = round($producto->comentarios->avg('Rating') ?? 0);
                                        @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            <li>
                                                <i data-feather="star" class="{{ $i <= $rating ? 'fill' : '' }}"></i>
                                            </li>
                                        @endfor
                                    </ul>
                                </div>
                            </div>

                            <div class="product-contain">
                                <p class="w-100">{{ $producto->Prod_Descripcion ?: 'Sin descripcion disponible.' }}</p>
                            </div>

                            <div class="note-box product-package">
                                <div class="cart_qty qty-box product-qty">
                                    <div class="input-group">
                                        <button type="button" class="qty-left-minus" data-type="minus" data-field="">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input class="form-control input-number qty-input" type="text" id="qty-{{ $producto->Id_Producto }}" name="quantity" value="1">
                                        <button type="button" class="qty-right-plus" data-type="plus" data-field="">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <button class="btn btn-md bg-dark cart-button text-white w-100"
                                        onclick="addToCart(
                                            {{ $producto->Id_Producto }},
                                            @js($imagenUrl),
                                            @js(route('product.details', ['idproducto' => $producto->Id_Producto, 'slug_producto' => $producto->Prod_Slug])),
                                            @js($producto->Prod_Precio),
                                            @js($producto->Prod_Nombre)
                                        )">
                                    Agregar al Carrito
                                </button>
                            </div>

                            <div class="buy-box">
                                <a href="javascript:void(0)"
                                   onclick="addToWishlist(
                                        {{ $producto->Id_Producto }},
                                        @js($imagenUrl),
                                        @js(route('product.details', ['idproducto' => $producto->Id_Producto, 'slug_producto' => $producto->Prod_Slug])),
                                        @js($producto->Prod_Precio),
                                        @js($producto->Prod_Nombre)
                                   )">
                                    <i data-feather="heart"></i>
                                    <span>Agregar a Wishlist</span>
                                </a>
                            </div>

                            <div class="payment-option">
                                <div class="product-title">
                                    <h4>Guaranteed Safe Checkout</h4>
                                </div>
                                <ul>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <img src="../assets/images/product/payment/1.svg" class="blur-up lazyload" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <img src="../assets/images/product/payment/2.svg" class="blur-up lazyload" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <img src="../assets/images/product/payment/3.svg" class="blur-up lazyload" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <img src="../assets/images/product/payment/4.svg" class="blur-up lazyload" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <img src="../assets/images/product/payment/5.svg" class="blur-up lazyload" alt="">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-5 d-none d-lg-block wow fadeInUp">
                <div class="right-sidebar-box">
                    <div class="vendor-box">
                        <div class="vendor-contain">
                            <div class="vendor-name">
                                <h5 class="fw-500">{{ $producto->marca->Nom_Marca }}</h5>
                            </div>
                        </div>

                        <p class="vendor-detail">{{ $producto->marca->Descrip_Marca ?: 'Sin descripcion disponible.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="product-section-box m-0">
                    <ul class="nav nav-tabs custom-nav" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Description</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab">Review</button>
                        </li>
                    </ul>

                    <div class="tab-content custom-tab" id="myTabContent">
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="product-description">
                                <div class="nav-desh">
                                    <p class="w-100">{{ $producto->Prod_Descripcion ?: 'Sin descripcion disponible.' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="info" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table info-table">
                                    <tbody>
                                        <tr>
                                            <td>Specialty</td>
                                            <td>Vegetarian</td>
                                        </tr>
                                        <tr>
                                            <td>Ingredient Type</td>
                                            <td>Vegetarian</td>
                                        </tr>
                                        <tr>
                                            <td>Brand</td>
                                            <td>Lavian Exotique</td>
                                        </tr>
                                        <tr>
                                            <td>Form</td>
                                            <td>Bar Brownie</td>
                                        </tr>
                                        <tr>
                                            <td>Package Information</td>
                                            <td>Box</td>
                                        </tr>
                                        <tr>
                                            <td>Manufacturer</td>
                                            <td>Prayagh Nutri Product Pvt Ltd</td>
                                        </tr>
                                        <tr>
                                            <td>Item part number</td>
                                            <td>LE 014 - 20pcs Crème Bakes (Pack of 2)</td>
                                        </tr>
                                        <tr>
                                            <td>Net Quantity</td>
                                            <td>40.00 count</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="care" role="tabpanel">
                            <div class="information-box">
                                <ul>
                                    <li>Store cream cakes in a refrigerator. Fondant cakes should be
                                        stored in an air conditioned environment.</li>

                                    <li>Slice and serve the cake at room temperature and make sure
                                        it is not exposed to heat.</li>

                                    <li>Use a serrated knife to cut a fondant cake.</li>

                                    <li>Sculptural elements and figurines may contain wire supports
                                        or toothpicks or wooden skewers for support.</li>

                                    <li>Please check the placement of these items before serving to
                                        small children.</li>

                                    <li>The cake should be consumed within 24 hours.</li>

                                    <li>Enjoy your cake!</li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="review" role="tabpanel">
                            <div class="review-box">
                                <div class="row">
                                    <div class="col-xl-5">
                                        <div class="product-rating-box">
                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="product-main-rating">
                                                        <h2>{{ number_format($averageRating, 2) }}
                                                            <i data-feather="star"></i>
                                                        </h2>

                                                        <h5>{{ $totalReviews }} Overall Rating</h5>
                                                    </div>
                                                </div>

                                                <div class="col-xl-12">
                                                    <ul class="product-rating-list">
                                                        @foreach ($ratingBreakdown as $item)
                                                            <li>
                                                                <div class="rating-product">
                                                                    <h5>{{ $item['stars'] }}<i data-feather="star"></i></h5>
                                                                    <div class="progress">
                                                                        <div class="progress-bar" style="width: {{ $item['percentage'] }}%;"></div>
                                                                    </div>
                                                                    <h5 class="total">{{ $item['count'] }}</h5>
                                                                </div>
                                                            </li>
                                                        @endforeach

                                                    </ul>

                                                    <div class="review-title-2">
                                                        <h4 class="fw-bold">Califica este producto</h4>
                                                        <p>Cuéntales a otros clientes lo que piensas</p>
                                                        @auth
                                                            @if ($userCanReview)
                                                                <button class="btn" type="button" data-bs-toggle="modal" data-bs-target="#writereview">
                                                                    {{ $userReview ? 'Editar mi reseña' : 'Escribir una reseña' }}
                                                                </button>
                                                            @else
                                                                <p class="text-content mb-0">Debes comprar este producto para poder reseñarlo.</p>
                                                            @endif
                                                        @else
                                                            <p class="text-content mb-0">
                                                                Inicia sesión para escribir una reseña.
                                                            </p>
                                                        @endauth
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-7">
                                        <div class="review-people">
                                            <ul class="review-list">
                                                @forelse ($comentarios as $comentario)
                                                    <li>
                                                        <div class="people-box">
                                                            <div>
                                                                <div class="people-image people-text">
                                                                    <img alt="user" class="img-fluid" src="../assets/images/review/1.jpg">
                                                                </div>
                                                            </div>
                                                            <div class="people-comment">
                                                                <div class="people-name">
                                                                    <a href="javascript:void(0)" class="name">
                                                                        {{ $comentario->usuario->Usu_Nombre ?? 'Usuario' }}
                                                                    </a>
                                                                    <div class="date-time">
                                                                        <h6 class="text-content">
                                                                            {{ $comentario->created_at ? $comentario->created_at->format('d/m/Y h:i A') : 'Fecha no disponible' }}
                                                                        </h6>
                                                                        <div class="product-rating">
                                                                            <ul class="rating">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <li>
                                                                                        <i data-feather="star" class="{{ $i <= $comentario->Rating ? 'fill' : '' }}"></i>
                                                                                    </li>
                                                                                @endfor
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="reply">
                                                                    <p>{{ $comentario->Comentario }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li>
                                                        <div class="people-box">
                                                            <div class="people-comment">
                                                                <div class="reply">
                                                                    <p>Aun no hay comentarios para este producto.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Nav Tab Section End -->
<!-- Related Product Section Start -->
<section class="product-list-section section-b-space">
    <div class="container-fluid-lg">
        <div class="title">
            <h2>Productos Relacionados</h2>
            <span class="title-leaf">
                <svg class="icon-width">
                    <use xlink:href="../assets/svg/leaf.svg#leaf"></use>
                </svg>
            </span>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="{{ $relatedProducts->isNotEmpty() ? 'slider-6_1' : '' }} product-wrapper">
                    @forelse ($relatedProducts as $relacionado)
                        @php
                            $relImg = $relacionado->imagenes->sortBy('orden')->first();
                            $relImgUrl = $relImg ? asset($relImg->url) : asset('storage/products/default.png');
                            $relRating = round($relacionado->comentarios->avg('Rating') ?? 0, 1);
                        @endphp
                        <div>
                            <div class="product-box-4 wow fadeInUp">
                                <div class="product-image">
                                    <div class="label-flex">
                                        <button type="button" class="btn p-0 wishlist btn-wishlist notifi-wishlist"
                                            onclick="addToWishlist(
                                                {{ $relacionado->Id_Producto }},
                                                @js($relImgUrl),
                                                @js(route('product.details', ['idproducto' => $relacionado->Id_Producto, 'slug_producto' => $relacionado->Prod_Slug])),
                                                @js($relacionado->Prod_Precio),
                                                @js($relacionado->Prod_Nombre)
                                            )">
                                            <i class="iconly-Heart icli"></i>
                                        </button>
                                    </div>
                                    <a href="{{ route('product.details', ['idproducto' => $relacionado->Id_Producto, 'slug_producto' => $relacionado->Prod_Slug]) }}">
                                        <img src="{{ $relImgUrl }}" class="img-fluid blur-up lazyload" alt="{{ $relacionado->Prod_Nombre }}">
                                    </a>
                                    <ul class="option">
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle">
                                            <a href="{{ route('product.details', ['idproducto' => $relacionado->Id_Producto, 'slug_producto' => $relacionado->Prod_Slug]) }}">
                                                <i class="iconly-Show icli"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="product-detail">
                                    <ul class="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <li>
                                                <i data-feather="star" class="{{ $i <= round($relRating) ? 'fill' : '' }}"></i>
                                            </li>
                                        @endfor
                                    </ul>
                                    <a href="{{ route('product.details', ['idproducto' => $relacionado->Id_Producto, 'slug_producto' => $relacionado->Prod_Slug]) }}">
                                        <h5 class="name">{{ $relacionado->Prod_Nombre }}</h5>
                                    </a>
                                    <h5 class="price theme-color">
                                        Q {{ number_format($relacionado->Prod_Precio, 2) }}
                                        @if (!is_null($relacionado->Prod_PrecioOferta))
                                            <del>Q {{ number_format($relacionado->Prod_PrecioOferta, 2) }}</del>
                                        @endif
                                    </h5>
                                    <div class="price-qty">
                                        <div class="counter-number">
                                            <div class="counter">
                                                <div class="qty-left-minus">
                                                    <i class="fa-solid fa-minus"></i>
                                                </div>
                                                <input class="form-control input-number qty-input"
                                                    type="text"
                                                    id="qty-{{ $relacionado->Id_Producto }}"
                                                    value="1">
                                                <div class="qty-right-plus">
                                                    <i class="fa-solid fa-plus"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="buy-button buy-button-2 btn btn-cart"
                                            onclick="addToCart(
                                                {{ $relacionado->Id_Producto }},
                                                @js($relImgUrl),
                                                @js(route('product.details', ['idproducto' => $relacionado->Id_Producto, 'slug_producto' => $relacionado->Prod_Slug])),
                                                @js($relacionado->Prod_Precio),
                                                @js($relacionado->Prod_Nombre)
                                            )">
                                            <i class="iconly-Buy icli text-white m-0"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="w-100 text-center py-4">
                            <p class="text-content mb-0">No hay productos relacionados disponibles por ahora.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Related Product Section End -->
<!-- Review Modal Start -->
<div class="modal fade theme-modal question-modal"
     id="writereview"
     tabindex="-1"
     data-open-on-load="{{ ($errors->has('rating') || $errors->has('comentario') || $errors->has('review')) ? '1' : '0' }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    {{ $userReview ? 'Editar reseña' : 'Escribir reseña' }}
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body pt-0">
                <form class="product-review-form" method="POST" action="{{ route('product.review.save', ['idproducto' => $producto->Id_Producto]) }}">
                    @csrf
                    <div class="product-wrapper">
                        <div class="product-image">
                            <img class="img-fluid" alt="{{ $producto->Prod_Nombre }}" src="{{ $imagenUrl }}">
                        </div>
                        <div class="product-content">
                            <h5 class="name">{{ $producto->Prod_Nombre }}</h5>
                            <div class="product-review-rating">
                                <div class="product-rating">
                                    <h6 class="price-number">Q{{ number_format($producto->Prod_Precio, 2) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="review-box">
                        <div class="product-review-rating">
                            <label for="rating">Calificación *</label>
                            <select id="rating" name="rating" class="form-select" required>
                                <option value="">Selecciona una calificación</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('rating', $userReview->Rating ?? null) == $i)>
                                        {{ $i }} estrella{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                            @error('rating')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="review-box">
                        <label for="comentario" class="form-label">Tu comentario *</label>
                        <textarea id="comentario" name="comentario" rows="3" class="form-control" placeholder="Tu comentario" required>{{ old('comentario', $userReview->Comentario ?? '') }}</textarea>
                        @error('comentario')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    @if ($errors->has('review'))
                        <div class="alert alert-danger mt-2 mb-0">{{ $errors->first('review') }}</div>
                    @endif
                    @if (session('review_status'))
                        <div class="alert alert-success mt-2 mb-0">{{ session('review_status') }}</div>
                    @endif
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-md btn-theme-outline fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-md fw-bold text-light theme-bg-color">
                            {{ $userReview ? 'Guardar cambios' : 'Publicar reseña' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- Review Modal End -->
<!-- Product Left Sidebar End -->
{{-- Scripts: solo en layouts/app (partials.js). Incluirlos aquí ejecutaba JS antes del panel tema (#colorPick) y duplicaba script.js / slick. --}}
@endsection
