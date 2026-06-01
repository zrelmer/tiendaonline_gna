<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <a href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/logo/LogoGNA.png') }}" alt="GNA Core" class="admin-sidebar-logo">
        </a>
        <span class="admin-sidebar-badge">Admin</span>
    </div>

    <nav class="admin-sidebar-nav">
        <ul>
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   @class(['active' => request()->routeIs('admin.dashboard')])>
                    <i data-feather="grid"></i>
                    <span>Panel principal</span>
                </a>
            </li>
            <li class="admin-nav-divider"></li>
            <li>
                <a href="{{ route('home') }}" target="_blank" rel="noopener">
                    <i data-feather="external-link"></i>
                    <span>Ver tienda</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <i data-feather="user"></i>
                    <span>Mi panel usuario</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
