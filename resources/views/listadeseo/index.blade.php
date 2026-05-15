@extends('layouts.app')

@section('title', 'Lista de deseos')

@section('content')
<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Lista de deseos</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Lista de deseos</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Product Section Start -->
<section class="product-section section-b-space">
    <div class="container-fluid-lg">
        <div class="title">
            <h2>Tus productos guardados</h2>
        </div>
        <div id="wishlist-products-root"></div>
        <div id="wishlist-empty" class="text-center py-5 d-none">
            <p class="text-content mb-3">Tu lista de deseos está vacía.</p>
            <a href="{{ route('home') }}" class="btn btn-animation btn-md fw-bold">Seguir comprando</a>
        </div>
    </div>
</section>
<!-- Product Section End -->
@endsection

@push('scripts')
<script>
(function () {
    function escapeHtml(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function chunkArray(arr, size) {
        const chunks = [];
        for (let i = 0; i < arr.length; i += size) {
            chunks.push(arr.slice(i, i + size));
        }
        return chunks;
    }

    function buildProductCard(product) {
        const id = parseInt(product.id, 10);
        if (Number.isNaN(id)) {
            return '';
        }
        const nombre = escapeHtml(product.nombre);
        const imagen = escapeHtml(product.imagen);
        const url = escapeHtml(product.url);
        const precio = Number(product.precio);
        const precioStr = Number.isFinite(precio) ? precio.toFixed(2) : '0.00';

        return (
            '<div class="col-lg-2 col-md-4 col-sm-6 mb-4">' +
                '<div class="product-box-4 wow fadeInUp">' +
                    '<div class="product-image">' +
                        '<div class="label-flex">' +
                            '<button type="button" class="btn p-0 wishlist btn-wishlist js-wishlist-remove" title="Quitar de la lista"' +
                                ' data-product-id="' + id + '">' +
                                '<i class="fa-solid fa-xmark"></i>' +
                            '</button>' +
                        '</div>' +
                        '<a href="' + url + '">' +
                            '<img src="' + imagen + '" class="img-fluid" alt="' + nombre + '">' +
                        '</a>' +
                        '<ul class="option">' +
                            '<li data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle">' +
                                '<a href="' + url + '"><i class="iconly-Show icli"></i></a>' +
                            '</li>' +
                        '</ul>' +
                    '</div>' +
                    '<div class="product-detail">' +
                        '<ul class="rating">' +
                            [1, 2, 3, 4, 5].map(function () {
                                return '<li><i data-feather="star"></i></li>';
                            }).join('') +
                        '</ul>' +
                        '<a href="' + url + '"><h5 class="name">' + nombre + '</h5></a>' +
                        '<h5 class="price theme-color">Q ' + precioStr + '</h5>' +
                        '<div class="price-qty">' +
                            '<div class="counter-number">' +
                                '<div class="counter">' +
                                    '<div class="qty-left-minus"><i class="fa-solid fa-minus"></i></div>' +
                                    '<input class="form-control input-number qty-input" type="text" id="qty-' + id + '" value="1">' +
                                    '<div class="qty-right-plus"><i class="fa-solid fa-plus"></i></div>' +
                                '</div>' +
                            '</div>' +
                            '<button type="button" class="buy-button buy-button-2 btn btn-cart js-wishlist-add-cart"' +
                                ' data-product-id="' + id + '">' +
                                '<i class="iconly-Buy icli text-white m-0"></i>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function bindWishlistRootClicks() {
        var root = document.getElementById('wishlist-products-root');
        if (!root || root.dataset.wishlistClickBound === '1') {
            return;
        }
        root.dataset.wishlistClickBound = '1';
        root.addEventListener('click', function (e) {
            var removeBtn = e.target.closest('.js-wishlist-remove');
            if (removeBtn) {
                e.preventDefault();
                var rid = parseInt(removeBtn.getAttribute('data-product-id'), 10);
                if (!Number.isNaN(rid) && typeof window.removeFromWishlist === 'function') {
                    window.removeFromWishlist(rid);
                }
                return;
            }
            var addBtn = e.target.closest('.js-wishlist-add-cart');
            if (addBtn) {
                e.preventDefault();
                var aid = parseInt(addBtn.getAttribute('data-product-id'), 10);
                if (Number.isNaN(aid) || typeof window.getWishlist !== 'function' || typeof window.addToCart !== 'function') {
                    return;
                }
                var item = window.getWishlist().find(function (p) {
                    return parseInt(p.id, 10) === aid;
                });
                if (item) {
                    window.addToCart(
                        parseInt(item.id, 10),
                        item.imagen,
                        item.url,
                        parseFloat(item.precio),
                        item.nombre
                    );
                }
            }
        });
    }

    function refreshWishlistPage() {
        var root = document.getElementById('wishlist-products-root');
        var empty = document.getElementById('wishlist-empty');
        if (!root || !empty) {
            return;
        }

        bindWishlistRootClicks();

        var list = typeof getWishlist === 'function' ? getWishlist() : [];

        if (!list.length) {
            root.innerHTML = '';
            empty.classList.remove('d-none');
            return;
        }

        empty.classList.add('d-none');
        var chunks = chunkArray(list, 6);
        var html = '';
        for (var c = 0; c < chunks.length; c++) {
            html += '<div class="row">';
            for (var i = 0; i < chunks[c].length; i++) {
                html += buildProductCard(chunks[c][i]);
            }
            html += '</div>';
        }
        root.innerHTML = html;

        if (typeof feather !== 'undefined' && feather.replace) {
            feather.replace();
        }
    }

    window.refreshWishlistPage = refreshWishlistPage;
    bindWishlistRootClicks();
    refreshWishlistPage();
})();
</script>
@endpush
