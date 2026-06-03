<header class="header-2">
<div class="header-notification theme-bg-color overflow-hidden py-2">
    <div class="notification-slider">
        <div>
            <div class="timer-notification text-center">
                <h6>
                    <strong class="me-1">¡Bienvenido a tu tienda online GNA Core!</strong>
                    Descubre nuevas ofertas y regalos todos los fines de semana.
                    <strong class="ms-1">Muchas Gracias por Comprar</strong>
                </h6>
            </div>
        </div>

        <div>
            <div class="timer-notification text-center">
                <h6>
                    ¡Algo que te gusta ahora está en oferta!
                    <a href="{{ route('shop.index') }}" class="text-white">
                        Comprar ahora
                    </a>
                </h6>
            </div>
        </div>
    </div>

    <button class="btn close-notification" aria-label="Cerrar notificación">
        <span>Cerrar</span>
        <i class="fas fa-times"></i>
    </button>
</div>


<div class="top-nav top-header sticky-header sticky-header-3">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="navbar-top">
                    <a href="{{ route('home') }}" class="web-logo nav-logo">
                        <img src="{{ asset('assets/images/logo/LogoGNA.png') }}" class="img-fluid blur-up lazyload" alt="Logo GNA" style="width: 120px;">
                    </a>

                    {{-- Búsqueda global (móvil): envía a shop.index con ?search= --}}
                    <div class="search-full">
                        <form method="GET"
                              action="{{ route('shop.index') }}"
                              class="input-group"
                              role="search"
                              id="header-search-form-mobile">
                            <span class="input-group-text">
                                <i data-feather="search" class="font-light"></i>
                            </span>
                            <input type="search"
                                   name="search"
                                   class="form-control search-type"
                                   value="{{ request('search') }}"
                                   placeholder="Buscar productos..."
                                   aria-label="Buscar productos">
                            <span class="input-group-text close-search">
                                <i data-feather="x" class="font-light"></i>
                            </span>
                        </form>
                    </div>

                    <div class="middle-box">
                        <div class="center-box">
                            {{-- Búsqueda global (escritorio): mismo diseño searchbar-box, redirige a la tienda --}}
                            <form method="GET"
                                  action="{{ route('shop.index') }}"
                                  class="searchbar-box order-xl-1 d-none d-xl-block"
                                  role="search"
                                  id="header-search-form">
                                <input type="search"
                                       name="search"
                                       class="form-control"
                                       id="header-search-input"
                                       value="{{ request('search') }}"
                                       placeholder="Buscar productos..."
                                       aria-label="Buscar productos">
                                <button type="submit" class="btn search-button" aria-label="Buscar">
                                    <i class="iconly-Search icli"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if (request()->routeIs('dashboard') || request()->routeIs('dashboard.*'))
                        <div class="header-go-shop d-flex align-items-center flex-shrink-0 ms-2 ms-xl-3">
                            <a href="{{ route('home') }}"
                               class="btn theme-bg-color text-white btn-sm fw-semibold text-nowrap">
                                <i class="iconly-Buy icli"></i>
                                <span class="ms-1">Ir a tienda</span>
                            </a>
                        </div>
                    @endif

                    <div class="rightside-menu">
                        <div class="option-list">
                            <ul>
                                <li class="header-phone-hide">
                                    <a href="javascript:void(0)" class="header-icon user-icon search-icon">
                                        <i class="iconly-Profile icli"></i>
                                    </a>
                                </li>

                                <li>
                                    <a href="javascript:void(0)" class="header-icon search-box search-icon">
                                        <i class="iconly-Search icli"></i>
                                    </a>
                                </li>

                                <li class="onhover-dropdown">
                                    <a href="{{ route('listadeseo.index') }}" class="header-icon swap-icon">
                                        <small class="badge-number wishlist-count">0</small>
                                        <i class="iconly-Heart icli"></i>
                                    </a>
                                </li>

                                {{--  <i data-feather="user"></i>  --}}
                                <li class="onhover-dropdown">
                                    <a href="{{ route('cart.index') }}" class="header-icon bag-icon">
                                        {{-- Cambio: se agrega cart-count para que updateCartCounter actualice el badge. --}}
                                        <small id="carrito-count" class="badge-number cart-count">0</small>
                                        <i class="iconly-Bag-2 icli"></i>
                                    </a>
                                    <div class="onhover-div">
                                        <ul class="cart-list" id="mini-cart-items">
                                            {{-- JS --}}
                                        </ul>

                                        <div class="price-box">
                                            <h5>Precio Total:</h5>
                                            <h4 class="theme-color fw-bold" id="mini-cart-total">
                                                Q0.00
                                            </h4>
                                        </div>

                                        <div class="button-group">
                                            <a href="{{ route('cart.index') }}" class="btn btn-sm cart-button">Ver Carrito</a>
                                            <a href="{{ auth()->check() ? route('cart.checkout') : route('login') }}"
                                               class="btn btn-sm cart-button theme-bg-color text-white">Proceder al pago</a>
                                        </div>
                                    </div>
                                </li>
                                {{--  <li class="onhover-dropdown">
                                    <a href="javascript:void(0)" class="header-icon swap-icon">
                                        <i data-feather="user"></i>
                                    </a>
                                </li>  --}}
                                {{--  tuve problemas con la plantilla y genere css es este partado  --}}
                                <li class="right-side onhover-dropdown header-user-dropdown">
                                    <div class="delivery-login-box header-account-trigger"
                                         role="button"
                                         tabindex="0"
                                         aria-expanded="false"
                                         aria-haspopup="true"
                                         aria-controls="headerAccountMenu"
                                         aria-label="Menú de cuenta">

                                    <div class="delivery-icon">
                                        <i data-feather="user"></i>
                                    </div>

                                    <div class="delivery-detail">
                                        <h6>Hola,</h6>
                                        <h5>{{ Auth::user()->Usu_Nombre ?? 'Mi Cuenta'}}</h5>
                                    </div>

                                    </div>
                                    {{--  datos login  --}}
                                    <div class="onhover-div onhover-div-login" id="headerAccountMenu">
                                    <ul class="user-box-name">
                                        @auth
                                            @php
                                                $usuarioAuth = auth()->user();
                                                $esAdministrador = $usuarioAuth instanceof \App\Models\Usuario
                                                    && (int) $usuarioAuth->Id_Rol === \App\Models\Usuario::ROL_ADMIN;
                                            @endphp
                                            <!-- Si el usuario está autenticado -->
                                            <li class="product-box-contain">
                                                <a href="{{ route('dashboard') }}">Mi Panel</a>
                                            </li>

                                            @if ($esAdministrador)
                                                <li class="product-box-contain">
                                                    <a href="{{ route('admin.dashboard') }}" class="fw-bold theme-color">
                                                        Panel Admin
                                                    </a>
                                                </li>
                                            @endif

                                            <li class="product-box-contain">
                                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                                    @csrf
                                                    <a href="#"
                                                       onclick="event.preventDefault(); if (typeof clearClientShopStorage === 'function') { clearClientShopStorage(); } this.closest('form').submit();"
                                                       class="text-danger">
                                                        Cerrar Sesión
                                                    </a>
                                                </form>
                                            </li>
                                        @else
                                            <!-- Si el usuario no está autenticado -->
                                            <li class="product-box-contain">
                                                <a href="{{ route('login') }}">Iniciar Sesión</a>
                                            </li>

                                            <li class="product-box-contain">
                                                <a href="{{ route('register') }}">Registrarse</a>
                                            </li>
                                        @endauth
                                    </ul>
                                </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</header>
