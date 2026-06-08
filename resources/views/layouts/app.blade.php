

<!DOCTYPE html>
<html lang="es">

<head>
    <title>@yield('title', 'Inicio') — GNA Core</title>
    @include('partials.head')
</head>

@php
    $isAuthGuestPage = request()->routeIs('login', 'register', 'password.request', 'password.reset');
@endphp
<body @class([
    'theme-color-2',
    'has-mobile-nav' => ! $isAuthGuestPage,
    'auth-guest-page' => $isAuthGuestPage,
])>

    <!-- Loader Start -->
    @include('partials.loader')
    <!-- Loader End -->

    <!-- Header Start -->
    @include('partials.header')
    <!-- Header End -->

    @include('partials.mobile-menu-offcanvas')

    <!-- mobile fix menu start -->
    @unless ($isAuthGuestPage)
    <nav class="mobile-menu d-xl-none d-block mobile-cart" aria-label="Navegación inferior">
        <ul>
            <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">
                    <i class="iconly-Home icli"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <li class="mobile-category {{ request()->routeIs('shop.index') ? 'active' : '' }}">
                <a href="javascript:void(0)"
                   data-bs-toggle="offcanvas"
                   data-bs-target="#primaryMenu"
                   aria-controls="primaryMenu"
                   aria-label="Abrir menú y categorías">
                    <i class="iconly-Category icli"></i>
                    <span>Menú</span>
                </a>
            </li>

            <li>
                <a href="javascript:void(0)" class="search-box" aria-label="Buscar productos">
                    <i class="iconly-Search icli"></i>
                    <span>Buscar</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('listadeseo.*') ? 'active' : '' }}">
                <a href="{{ route('listadeseo.index') }}">
                    <i class="iconly-Heart icli"></i>
                    <span>Deseos</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
                <a href="{{ route('cart.index') }}">
                    <i class="iconly-Bag-2 icli fly-cate"></i>
                    <span>Carrito</span>
                </a>
            </li>
        </ul>
    </nav>
    @endunless
    <!-- mobile fix menu end -->
    @yield('content')

    <!-- Footer Start -->
    {{--  @include('partials.footer')  --}}
    @unless ($isAuthGuestPage)
        @include('partials.footer')
    @endunless
    <!-- Footer End -->

    <!-- Quick View Modal Box Start -->
    <div class="modal fade theme-modal view-modal" id="view" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header p-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-sm-4 g-2">
                        <div class="col-lg-6">
                            <div class="slider-image">
                                <img src="{{ asset('assets/images/product/category/1.jpg') }}" class="img-fluid blur-up lazyload" alt="">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="right-sidebar-modal">
                                <h4 class="title-name">Peanut Butter Bite Premium Butter Cookies 600 g</h4>
                                <h4 class="price">Q36.99</h4>
                                <div class="product-rating">
                                    <ul class="rating">
                                        <li>
                                            <i data-feather="star" class="fill"></i>
                                        </li>
                                        <li>
                                            <i data-feather="star" class="fill"></i>
                                        </li>
                                        <li>
                                            <i data-feather="star" class="fill"></i>
                                        </li>
                                        <li>
                                            <i data-feather="star" class="fill"></i>
                                        </li>
                                        <li>
                                            <i data-feather="star"></i>
                                        </li>
                                    </ul>
                                    <span class="ms-2">8 Reviews</span>
                                    <span class="ms-2 text-danger">6 sold in last 16 hours</span>
                                </div>

                                <div class="product-detail">
                                    <h4>Product Details :</h4>
                                    <p>Candy canes sugar plum tart cotton candy chupa chups sugar plum chocolate I love.
                                        Caramels marshmallow icing dessert candy canes I love soufflé I love toffee.
                                        Marshmallow pie sweet sweet roll sesame snaps tiramisu jelly bear claw. Bonbon
                                        muffin I love carrot cake sugar plum dessert bonbon.</p>
                                </div>

                                <ul class="brand-list">
                                    <li>
                                        <div class="brand-box">
                                            <h5>Brand Name:</h5>
                                            <h6>Black Forest</h6>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="brand-box">
                                            <h5>Product Code:</h5>
                                            <h6>W0690034</h6>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="brand-box">
                                            <h5>Product Type:</h5>
                                            <h6>White Cream Cake</h6>
                                        </div>
                                    </li>
                                </ul>

                                <div class="select-size">
                                    <h4>Cake Size :</h4>
                                    <select class="form-select select-form-size">
                                        <option selected="">Select Size</option>
                                        <option value="1.2">1/2 KG</option>
                                        <option value="0">1 KG</option>
                                        <option value="1.5">1/5 KG</option>
                                        <option value="red">Red Roses</option>
                                        <option value="pink">With Pink Roses</option>
                                    </select>
                                </div>

                                <div class="modal-button">
                                    <button type="button" class="btn btn-md add-cart-button icon" data-bs-dismiss="modal">
                                        Cerrar
                                    </button>
                                    <a href="{{ route('shop.index') }}" class="btn theme-bg-color view-button icon text-white fw-bold btn-md">
                                        Ir a la tienda
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick View Modal Box End -->

    @unless ($isAuthGuestPage)
    <!-- Items section Start (oculto en login/registro/reset) -->
    <div class="button-item">
        <button class="item-btn btn text-white">
            <i class="iconly-Bag-2 icli"></i>
        </button>
    </div>
    <div class="item-section">
        <button class="close-button">
            <i class="fas fa-times"></i>
        </button>
        <h6>
            <i class="iconly-Bag-2 icli"></i>
            <span id="carrito-count2">0 Prod</span>
        </h6>
        <button onclick="location.href = '{{ route('cart.index') }}';" class="btn item-button btn-sm fw-bold"><span id="total-cart2">0.00</span></button>
    </div>
    <!-- Items section End -->
    @endunless

    <!-- Tap to top and theme setting button start -->
    <!-- Tap to top and theme setting button end -->

    <!-- Bg overlay Start -->
    <div class="bg-overlay"></div>
    <!-- Bg overlay End -->

    @include('partials.js')
    <script>
        window.CART_CONFIG = {
            isAuthenticated: @json(auth()->check()),
            routes: {
                items: @json(route('cart.items')),
                sync: @json(route('cart.sync')),
                store: @json(route('cart.items.store')),
                itemBase: @json(url('/cart/items')),
            },
            shipping: {
                freeThreshold: @json((float) config('shipping.umbral_envio_gratis', 300)),
                cost: @json((float) config('shipping.costo_envio', 35)),
                digitalSlugs: @json(config('shipping.categorias_digitales_slug', [])),
            },
        };
        window.WISHLIST_CONFIG = {
            isAuthenticated: @json(auth()->check()),
            routes: {
                items: @json(route('listadeseo.items')),
                sync: @json(route('listadeseo.sync')),
                store: @json(route('listadeseo.items.store')),
                itemBase: @json(url('/listadeseo/items')),
            },
        };
    </script>
    <script src="{{asset('js/cart.js') }}"></script>
    @stack('scripts')
</body>

</html>
