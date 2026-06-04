<div class="right-box-contain product-detail-purchase-box">
    <h2 class="name">{{ $producto->Prod_Nombre }}</h2>
    <div class="price-rating">
        <h3 class="theme-color price">
            Q{{ number_format($producto->Prod_Precio, 2) }}
            @if (!is_null($producto->Prod_PrecioOferta))
                <del class="text-content">Q{{ number_format($producto->Prod_PrecioOferta, 2) }}</del>
            @endif
        </h3>
        <div class="product-rating custom-rate">
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
        @if ($producto->descripcion_resumen !== '')
            <p class="w-100">{{ $producto->descripcion_resumen }}</p>
        @else
            <p class="w-100">Sin descripción disponible.</p>
        @endif
    </div>

    <div class="note-box product-package">
        <div class="cart_qty qty-box product-qty">
            <div class="input-group">
                <button type="button" class="qty-left-minus" data-type="minus" data-field="" aria-label="Disminuir cantidad">
                    <i class="fa fa-minus"></i>
                </button>
                <input class="form-control input-number qty-input"
                       type="text"
                       id="qty-{{ $producto->Id_Producto }}"
                       name="quantity"
                       value="1"
                       inputmode="numeric"
                       aria-label="Cantidad">
                <button type="button" class="qty-right-plus" data-type="plus" data-field="" aria-label="Aumentar cantidad">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <button type="button"
                class="btn btn-md bg-dark cart-button text-white w-100 product-detail-add-cart"
                aria-label="Agregar al carrito"
                onclick="addToCart(
                    {{ $producto->Id_Producto }},
                    @js($imagenUrl),
                    @js(route('product.details', ['idproducto' => $producto->Id_Producto, 'slug_producto' => $producto->Prod_Slug])),
                    @js($producto->Prod_Precio),
                    @js($producto->Prod_Nombre),
                    @js($producto->categoria?->Cate_Slug ?? '')
                )">
            Agregar al carrito
        </button>
    </div>

    <div class="buy-box">
        <a href="javascript:void(0)"
           class="product-detail-wishlist-link"
           aria-label="Agregar a lista de deseos"
           onclick="addToWishlist(
                {{ $producto->Id_Producto }},
                @js($imagenUrl),
                @js(route('product.details', ['idproducto' => $producto->Id_Producto, 'slug_producto' => $producto->Prod_Slug])),
                @js($producto->Prod_Precio),
                @js($producto->Prod_Nombre)
           )">
            <i data-feather="heart"></i>
            <span>Agregar a lista de deseos</span>
        </a>
    </div>

    @if ($producto->marca)
        <div class="product-detail-brand-mobile d-lg-none mt-3 p-3 border rounded">
            <h6 class="mb-1 fw-semibold">{{ $producto->marca->Nom_Marca }}</h6>
            <p class="text-content small mb-0">{{ $producto->marca->Descrip_Marca ?: 'Sin descripción de marca.' }}</p>
        </div>
    @endif
</div>
