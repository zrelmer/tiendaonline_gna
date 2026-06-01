<header class="admin-topbar">
    <div class="admin-topbar-start">
        <button type="button"
                class="btn btn-sm btn-outline-secondary d-lg-none admin-sidebar-toggle"
                aria-label="Abrir menú">
            <i data-feather="menu"></i>
        </button>
        <h1 class="admin-topbar-title mb-0">@yield('page_title', 'Panel de administración')</h1>
    </div>

    <div class="admin-topbar-end">
        <span class="admin-topbar-user text-content small d-none d-md-inline">
            {{ auth()->user()->Usu_Nombre }}
        </span>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">
                Cerrar sesión
            </button>
        </form>
    </div>
</header>
