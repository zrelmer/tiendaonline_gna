@if ($recommendedProducts->isNotEmpty())
    <section class="product-section home-recommended-products section-b-space">
        <div class="container-fluid-lg">
            <div class="title">
                <h2 class="home-recommended-title">
                    <i class="fa-solid fa-star home-recommended-title__star" aria-hidden="true"></i>
                    <span>Recomendados para ti</span>
                    <i class="fa-solid fa-star home-recommended-title__star" aria-hidden="true"></i>
                </h2>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 g-md-4">
                @foreach ($recommendedProducts as $product)
                    @php
                        $imagen = $product->imagenes->sortBy('orden')->first();
                        $imagenUrl = $imagen
                            ? asset($imagen->url)
                            : asset('storage/products/default.png');
                        $rating = round($product->comentarios->avg('Rating') ?? 0);
                        $productUrl = route('product.details', [
                            'idproducto' => $product->Id_Producto,
                            'slug_producto' => $product->Prod_Slug,
                        ]);
                    @endphp

                    <div class="col">
                        <div class="product-box-4 wow fadeInUp h-100">
                            <div class="product-image">
                                <div class="label-flex">
                                    <button type="button"
                                            class="btn p-0 wishlist btn-wishlist"
                                            aria-label="Agregar a lista de deseos"
                                            onclick="addToWishlist(
                                                {{ $product->Id_Producto }},
                                                @js($imagenUrl),
                                                @js($productUrl),
                                                @js($product->Prod_Precio),
                                                @js($product->Prod_Nombre)
                                            )">
                                        <i class="iconly-Heart icli"></i>
                                    </button>
                                </div>
                                <a href="{{ $productUrl }}">
                                    <img src="{{ $imagenUrl }}"
                                         class="img-fluid blur-up lazyload"
                                         alt="{{ $product->Prod_Nombre }}">
                                </a>
                                <ul class="option">
                                    <li data-bs-toggle="tooltip" title="Ver detalle">
                                        <a href="{{ $productUrl }}">
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
                                <a href="{{ $productUrl }}">
                                    <h5 class="name">{{ $product->Prod_Nombre }}</h5>
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
                                            <div class="qty-left-minus">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <input class="form-control input-number qty-input"
                                                   type="text"
                                                   id="qty-rec-{{ $product->Id_Producto }}"
                                                   value="1">
                                            <div class="qty-right-plus">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button"
                                            class="buy-button buy-button-2 btn btn-cart"
                                            aria-label="Agregar al carrito"
                                            onclick="addToCart(
                                                {{ $product->Id_Producto }},
                                                @js($imagenUrl),
                                                @js($productUrl),
                                                @js($product->Prod_Precio),
                                                @js($product->Prod_Nombre),
                                                @js($product->categoria?->Cate_Slug ?? '')
                                            )">
                                        <i class="iconly-Buy icli text-white m-0"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
