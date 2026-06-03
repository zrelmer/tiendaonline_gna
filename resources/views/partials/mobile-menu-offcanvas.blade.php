<div class="offcanvas offcanvas-start categories-canvas mobile-menu-offcanvas"
     tabindex="-1"
     id="primaryMenu"
     aria-labelledby="primaryMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="primaryMenuLabel">Menú</h5>
        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Cerrar"></button>
    </div>

    <div class="offcanvas-body">
        <nav class="mobile-nav-links" aria-label="Navegación principal">
            <ul class="list-unstyled m-0 mobile-menu-nav-list">
                <li class="mobile-menu-nav-item">
                    <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <i class="iconly-Home icli"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="mobile-menu-nav-item">
                    <a href="{{ route('shop.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <i class="iconly-Buy icli"></i>
                        <span>Tienda</span>
                    </a>
                </li>
                <li class="mobile-menu-nav-item">
                    <a href="{{ route('cart.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <i class="iconly-Bag-2 icli"></i>
                        <span>Carrito</span>
                    </a>
                </li>
                <li class="mobile-menu-nav-item">
                    <a href="{{ route('listadeseo.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <i class="iconly-Heart icli"></i>
                        <span>Lista de deseos</span>
                    </a>
                </li>
                @auth
                    @php
                        $usuarioAuth = auth()->user();
                        $esAdministrador = $usuarioAuth instanceof \App\Models\Usuario
                            && (int) $usuarioAuth->Id_Rol === \App\Models\Usuario::ROL_ADMIN;
                    @endphp
                    <li class="mobile-menu-nav-item">
                        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                            <i data-feather="user"></i>
                            <span>Mi panel</span>
                        </a>
                    </li>
                    @if ($esAdministrador)
                        <li class="mobile-menu-nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none fw-semibold theme-color">
                                <i data-feather="grid"></i>
                                <span>Panel admin</span>
                            </a>
                        </li>
                    @endif
                    <li class="mobile-menu-nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="btn btn-link p-0 text-danger text-decoration-none d-flex align-items-center gap-2 border-0"
                                    onclick="if (typeof clearClientShopStorage === 'function') { clearClientShopStorage(); }">
                                <i data-feather="log-out"></i>
                                <span>Cerrar sesión</span>
                            </button>
                        </form>
                    </li>
                @else
                    <li class="mobile-menu-nav-item">
                        <a href="{{ route('login') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                            <i data-feather="log-in"></i>
                            <span>Iniciar sesión</span>
                        </a>
                    </li>
                    <li class="mobile-menu-nav-item">
                        <a href="{{ route('register') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                            <i data-feather="user-plus"></i>
                            <span>Registrarse</span>
                        </a>
                    </li>
                @endauth
            </ul>
        </nav>

        <div class="categories-list mobile-menu-categories">
            <h6 class="mobile-menu-section-title mb-3 fw-semibold">Categorías</h6>
            @if ($navCategories->isEmpty())
                <p class="text-content small mb-0">No hay categorías disponibles.</p>
            @else
                <ul class="list-unstyled m-0 mobile-menu-category-list">
                    @foreach ($navCategories as $categoria)
                        <li class="mobile-menu-category-item">
                            <a href="{{ route('shop.index', ['category' => $categoria->Id_Categoria]) }}"
                               class="d-flex align-items-center justify-content-between text-decoration-none py-2">
                                <h6 class="mb-0">{{ $categoria->Cate_Nombre }}</h6>
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
