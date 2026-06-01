<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    @include('partials.admin.head')
</head>

<body>
    <!-- tap on top start -->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- tap on tap end -->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <div class="page-header">
            <div class="header-wrapper m-0">
                <div class="header-logo-wrapper p-0">
                    <div class="logo-wrapper">
                        <a href="{{ route('admin.dashboard') }}">
                            <img class="img-fluid main-logo" src="{{ asset('assets/admin/images/logo/LogoGNA.png') }}" alt="logo">
                            <img class="img-fluid white-logo" src="{{ asset('assets/admin/images/logo/LogoGNA.png') }}" alt="logo">
                        </a>
                    </div>
                    <div class="toggle-sidebar">
                        <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
                        <a href="{{ route('admin.dashboard') }}">
                            <img src="{{ asset('assets/admin/images/logo/LogoGNA.png') }}" class="img-fluid" alt="">
                        </a>
                    </div>
                </div>

                <div class="nav-right col-6 pull-right right-header p-0">
                    <ul class="nav-menus">
                        <li class="profile-nav onhover-dropdown pe-0 me-0">
                            <div class="media profile-media">
                                <img class="user-profile rounded-circle" src="{{ asset('assets/admin/images/users/5.png') }}" alt="">
                                <div class="user-name-hide media-body">
                                    <span>{{ auth()->user()->Usu_Nombre }}</span>
                                    <p class="mb-0 font-roboto">{{ auth()->user()->Id_Rol == 1 ? 'Admin' : 'Usuario' }}<i class="middle ri-arrow-down-s-line"></i></p>
                                </div>
                            </div>
                            <ul class="profile-dropdown onhover-show-div">
                                <li>
                                    <a href="all-users.html">
                                        <i data-feather="users"></i>
                                        <span>Users</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="order-list.html">
                                        <i data-feather="archive"></i>
                                        <span>Orders</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="support-ticket.html">
                                        <i data-feather="phone"></i>
                                        <span>Spports Tickets</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="profile-setting.html">
                                        <i data-feather="settings"></i>
                                        <span>Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a data-bs-toggle="modal" data-bs-target="#staticBackdrop" href="javascript:void(0)">
                                        <i data-feather="log-out"></i>
                                        <span>Log out</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            <div class="sidebar-wrapper">
                <div id="sidebarEffect"></div>
                <div>
                    <div class="logo-wrapper logo-wrapper-center">
                        <a href="index.html" data-bs-original-title="" title="">
                            <img class="img-fluid for-white" src="{{ asset('assets/admin/images/logo/LogoGNA.png') }}" alt="logo">
                        </a>
                        <div class="back-btn">
                            <i class="fa fa-angle-left"></i>
                        </div>
                        {{-- <div class="toggle-sidebar">
                            <i class="ri-apps-line status_toggle middle sidebar-toggle"></i>
                        </div> --}}
                    </div>
                    <div class="logo-icon-wrapper">
                        <a href="{{ route('admin.dashboard') }}">
                            <img class="img-fluid main-logo main-white" src="{{ asset('assets/admin/images/logo/logo.png') }}" alt="logo">
                            <img class="img-fluid main-logo main-dark" src="{{ asset('assets/admin/images/logo/logo-white.png') }}" alt="logo">
                        </a>
                    </div>
                    <nav class="sidebar-main">
                        <div class="left-arrow" id="left-arrow">
                            <i data-feather="arrow-left"></i>
                        </div>

                        <div id="sidebar-menu">
                            <ul class="sidebar-links" id="simple-bar">
                                <li class="back-btn"></li>

                                <li class="sidebar-list">
                                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.dashboard') }}">
                                        <i class="ri-home-line"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                {{-- aca controlaremos los productos el crud de productos --}}
                                <li class="sidebar-list">
                                    <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-store-3-line"></i>
                                        <span>Productos</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="{{ route('admin.productos.index') }}">Listado de Productos</a>
                                        </li>

                                        <li>
                                            <a href="{{ route('admin.productos.create') }}">Agregar Producto</a>
                                        </li>
                                    </ul>
                                </li>
                                {{-- aca controlaremos las categorias el crud de categorias --}}
                                <li class="sidebar-list">
                                    <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-list-check-2"></i>
                                        <span>Categoria</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="{{ route('admin.categorias.index') }}">Listado de Categorias</a>
                                        </li>

                                        <li>
                                            <a href="{{ route('admin.categorias.create') }}">Agregar Categoria</a>
                                        </li>
                                    </ul>
                                </li>
                                {{-- aca controlaremos los atributos el crud de Departamentos --}}
                                <li class="sidebar-list">
                                    <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-list-settings-line"></i>
                                        <span>Departamentos</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="{{ route('admin.departamentos.index') }}">Listado de Departamentos</a>
                                        </li>

                                        <li>
                                            <a href="{{ route('admin.departamentos.create') }}">Agregar Departamento</a>
                                        </li>
                                    </ul>
                                </li>

                                {{-- aca controlaremos los municipios el crud de Municipios --}}
                                <li class="sidebar-list">
                                    <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-list-settings-line"></i>
                                        <span>Municipios</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="{{ route('admin.municipios.index') }}">Listado de Municipios</a>
                                        </li>

                                        <li>
                                            <a href="{{ route('admin.municipios.create') }}">Agregar Municipio</a>
                                        </li>
                                    </ul>
                                </li>
                                {{-- aca controlaremos los usuarios el crud de Usuarios --}}
                                <li class="sidebar-list">
                                    <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-user-3-line"></i>
                                        <span>Usuarios</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="{{ route('admin.usuarios.index') }}">Listado de Usuarios</a>
                                        </li>
                                    </ul>
                                </li>
                                {{-- boletas de pago aca veremos el listado de boletas de pago y el detalle de una boleta de pago --}}
                                <li class="sidebar-list">
                                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.boletas.index') }}">
                                        <i class="ri-price-tag-3-line"></i>
                                        <span>Boletas de Pago</span>
                                    </a>
                                </li>
                                {{-- aca controlaremos los pedidos el crud de Pedidos --}}
                                <li class="sidebar-list">
                                    <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-archive-line"></i>
                                        <span>Pedidos</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="#">Listado de Pedidos</a>
                                        </li>
                                        <li>
                                            <a href="#">Detalle de Pedido</a>
                                        </li>
                                        <li>
                                            <a href="#">Historial de Pedido</a>
                                        </li>
                                        <li>
                                            {{-- en este boton actualizaremos el seguimiento de un pedido para que se vea reflejado en el panel de usuario --}}
                                            <a href="#">Seguimiento de Pedido</a>
                                        </li>
                                    </ul>
                                </li>

                                {{-- aca controlaremos las cotizaciones el crud de Cotizaciones --}}
                                <li class="sidebar-list">
                                    <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-focus-3-line"></i>
                                        <span>Cotizaciones</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="translation.html">Translation</a>
                                        </li>

                                        <li>
                                            <a href="currency-rates.html">Currency Rates</a>
                                        </li>
                                    </ul>
                                </li>

                                {{-- aca controlaremos el inventario el crud de Inventario --}}
                                <li class="sidebar-list">
                                    <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-focus-3-line"></i>
                                        <span>Inventario</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li>
                                            <a href="translation.html">Inventario</a>
                                        </li>

                                        <li>
                                            <a href="currency-rates.html">Historial de Inventario</a>
                                        </li>
                                    </ul>
                                </li>




                            </ul>
                        </div>

                        <div class="right-arrow" id="right-arrow">
                            <i data-feather="arrow-right"></i>
                        </div>
                    </nav>
                </div>
            </div>
            <!-- Page Sidebar Ends-->

            <!-- index body start -->
            <div class="page-body">
                <div class="container-fluid">
                    @hasSection('content')
                        @yield('content')
                    @else
                    <div class="row">
                        <!-- chart caard section start -->
                        <div class="col-sm-6 col-xxl-3 col-lg-6">
                            <div class="main-tiles border-5 border-0  card-hover card o-hidden">
                                <div class="custome-1-bg b-r-4 card-body">
                                    <div class="media align-items-center static-top-widget">
                                        <div class="media-body p-0">
                                            <span class="m-0">Total Revenue</span>
                                            <h4 class="mb-0 counter">$6659
                                                <span class="badge badge-light-primary grow">
                                                    <i data-feather="trending-up"></i>8.5%</span>
                                            </h4>
                                        </div>
                                        <div class="align-self-center text-center">
                                            <i class="ri-database-2-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-xxl-3 col-lg-6">
                            <div class="main-tiles border-5 card-hover border-0 card o-hidden">
                                <div class="custome-2-bg b-r-4 card-body">
                                    <div class="media static-top-widget">
                                        <div class="media-body p-0">
                                            <span class="m-0">Total Orders</span>
                                            <h4 class="mb-0 counter">9856
                                                <span class="badge badge-light-danger grow">
                                                    <i data-feather="trending-down"></i>8.5%</span>
                                            </h4>
                                        </div>
                                        <div class="align-self-center text-center">
                                            <i class="ri-shopping-bag-3-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-xxl-3 col-lg-6">
                            <div class="main-tiles border-5 card-hover border-0  card o-hidden">
                                <div class="custome-3-bg b-r-4 card-body">
                                    <div class="media static-top-widget">
                                        <div class="media-body p-0">
                                            <span class="m-0">Total Products</span>
                                            <h4 class="mb-0 counter">893
                                                <a href="add-new-product.html" class="badge badge-light-secondary grow">
                                                    ADD NEW</a>
                                            </h4>
                                        </div>

                                        <div class="align-self-center text-center">
                                            <i class="ri-chat-3-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-xxl-3 col-lg-6">
                            <div class="main-tiles border-5 card-hover border-0 card o-hidden">
                                <div class="custome-4-bg b-r-4 card-body">
                                    <div class="media static-top-widget">
                                        <div class="media-body p-0">
                                            <span class="m-0">Total Customers</span>
                                            <h4 class="mb-0 counter">4.6k
                                                <span class="badge badge-light-success grow">
                                                    <i data-feather="trending-down"></i>8.5%</span>
                                            </h4>
                                        </div>

                                        <div class="align-self-center text-center">
                                            <i class="ri-user-add-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- fin de las cards de ejemplo --}}
                        {{-- CATEGORIAS --}}
                        <div class="col-12">
                            <div class="card o-hidden card-hover">
                                <div class="card-header border-0 pb-1">
                                    <div class="card-header-title p-0">
                                        <h4>Categorías</h4>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="category-slider no-arrow">
                                        @forelse ($categorias as $categoria)
                                            @php
                                                $iconoCategoria = \App\Support\CategoriaIcon::remixClass(
                                                    $categoria->Cate_Slug,
                                                    $categoria->Cate_Nombre
                                                );
                                                $urlTienda = route('shop.index', ['category' => $categoria->Id_Categoria]);
                                            @endphp
                                            <div>
                                                <div class="dashboard-category dashboard-category-round">
                                                    <a href="{{ $urlTienda }}" class="category-image category-image-remix" target="_blank" rel="noopener" title="{{ $categoria->Cate_Nombre }}">
                                                        <span class="category-image-inner">
                                                            <i class="{{ $iconoCategoria }} category-remix-icon" aria-hidden="true"></i>
                                                        </span>
                                                    </a>
                                                    <a href="{{ $urlTienda }}" class="category-name" target="_blank" rel="noopener">
                                                        <h6>{{ $categoria->Cate_Nombre }}</h6>
                                                        @if ($categoria->productos_count > 0)
                                                            <span class="small text-muted">{{ $categoria->productos_count }} producto{{ $categoria->productos_count === 1 ? '' : 's' }}</span>
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-4 text-center text-muted w-100">
                                                No hay categorías registradas.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- chart card section End -->


                        <!-- Earning chart star-->
                        <div class="col-xl-6">
                            <div class="card o-hidden card-hover">
                                <div class="card-header border-0 pb-1">
                                    <div class="card-header-title">
                                        <h4>Reporte de ingresos</h4>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="report-chart"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Earning chart  end-->


                        <!-- Productos más vendidos -->
                        <div class="col-xl-6 col-md-12">
                            <div class="card o-hidden card-hover">
                                <div class="card-header card-header-top card-header--2 px-0 pt-0">
                                    <div class="card-header-title">
                                        <h4>Productos más vendidos</h4>
                                    </div>

                                    <div class="best-selling-box d-sm-flex d-none">
                                        <span class="text-muted small">Ordenado por unidades vendidas</span>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div>
                                        <div class="table-responsive">
                                            <table class="best-selling-table w-image table border-0">
                                                <tbody>
                                                    @forelse ($productosMasVendidos as $item)
                                                        @php
                                                            $producto = $item->producto;
                                                            $urlProducto = route('product.details', [
                                                                'idproducto' => $producto->Id_Producto,
                                                                'slug_producto' => $producto->Prod_Slug,
                                                            ]);
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="best-product-box">
                                                                    <div class="product-image">
                                                                        <a href="{{ $urlProducto }}" target="_blank" rel="noopener">
                                                                            <img src="{{ $item->imagen_url }}" class="img-fluid" alt="{{ $producto->Prod_Nombre }}">
                                                                        </a>
                                                                    </div>
                                                                    <div class="product-name">
                                                                        <h5>
                                                                            <a href="{{ $urlProducto }}" class="text-dark" target="_blank" rel="noopener">
                                                                                {{ $producto->Prod_Nombre }}
                                                                            </a>
                                                                        </h5>
                                                                        <h6>
                                                                            @if ($item->ultima_venta)
                                                                                {{ $item->ultima_venta->format('d-m-Y') }}
                                                                            @else
                                                                                —
                                                                            @endif
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Precio</h6>
                                                                    <h5>Q {{ number_format((float) $producto->Prod_Precio, 2) }}</h5>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Pedidos</h6>
                                                                    <h5>{{ number_format($item->pedidos_count) }}</h5>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Stock</h6>
                                                                    <h5>{{ number_format($item->stock) }}</h5>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Monto</h6>
                                                                    <h5>Q {{ number_format($item->monto_total, 2) }}</h5>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-4">
                                                                Aún no hay ventas registradas.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Productos más vendidos End -->


                        <!-- Pedidos recientes -->
                        <div class="col-xl-6">
                            <div class="card o-hidden card-hover">
                                <div class="card-header card-header-top card-header--2 px-0 pt-0">
                                    <div class="card-header-title">
                                        <h4>Pedidos recientes</h4>
                                    </div>

                                    <div class="best-selling-box d-sm-flex d-none">
                                        <span class="text-muted small">Últimos 5 pedidos</span>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div>
                                        <div class="table-responsive">
                                            <table class="best-selling-table table border-0">
                                                <tbody>
                                                    @forelse ($pedidosRecientes as $pedido)
                                                        @php
                                                            $pagoInfo = \App\Support\PedidoPagoPresenter::estadoPago($pedido->pago);
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="best-product-box">
                                                                    <div class="product-name">
                                                                        <h5>{{ \App\Support\PedidoPagoPresenter::tituloProducto($pedido) }}</h5>
                                                                        <h6>{{ $pedido->Ped_Numero }}</h6>
                                                                    </div>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Fecha</h6>
                                                                    <h5>{{ $pedido->created_at?->format('d/m/Y') ?? '—' }}</h5>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Precio</h6>
                                                                    <h5>Q {{ number_format((float) $pedido->Ped_TotalPrecio, 2) }}</h5>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Estado</h6>
                                                                    <h5>{{ $pedido->estatus?->Nom_Estatus ?? 'Sin estatus' }}</h5>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="product-detail-box">
                                                                    <h6>Pago</h6>
                                                                    <h5 class="{{ $pagoInfo['class'] }}">{{ $pagoInfo['label'] }}</h5>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-4">
                                                                No hay pedidos registrados.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pedidos recientes end -->

                        <!-- Ingresos mensuales (barras) -->
                        <div class="col-xl-6">
                            <div class="card o-hidden card-hover">
                                <div class="card-header border-0 mb-0">
                                    <div class="card-header-title">
                                        <h4>Ingresos mensuales</h4>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="bar-chart-earning"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Ingresos mensuales end -->


                        <!-- Pagos recientes -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card o-hidden card-hover">
                                <div class="card-header border-0">
                                    <div class="card-header-title">
                                        <h4>Pagos recientes</h4>
                                    </div>
                                </div>

                                <div class="card-body pt-0">
                                    <div>
                                        <div class="table-responsive">
                                            <table class="user-table transactions-table table border-0">
                                                <tbody>
                                                    @forelse ($pagosRecientes as $pago)
                                                        @php
                                                            $pedido = $pago->pedido;
                                                            $montoInfo = \App\Support\PedidoPagoPresenter::montoTransaccion(
                                                                $pago,
                                                                (float) ($pedido?->Ped_TotalPrecio ?? 0)
                                                            );
                                                            $esUltima = $loop->last;
                                                        @endphp
                                                        <tr>
                                                            <td class="{{ \App\Support\PedidoPagoPresenter::claseFilaTransaccion($loop->index, $esUltima) }}">
                                                                <div class="transactions-icon">
                                                                    <i class="{{ \App\Support\PedidoPagoPresenter::iconoMetodoPago($pago->metodoPago) }}"></i>
                                                                </div>
                                                                <div class="transactions-name">
                                                                    <h6>{{ $pago->metodoPago?->MetPag_Descripcion ?? 'Método de pago' }}</h6>
                                                                    <p>
                                                                        {{ $pedido?->Ped_Numero ?? '—' }}
                                                                        · {{ \App\Support\PedidoPagoPresenter::estadoPago($pago)['label'] }}
                                                                    </p>
                                                                </div>
                                                            </td>

                                                            <td class="{{ $montoInfo['class'] }}{{ $esUltima ? ' pb-0' : '' }}">{{ $montoInfo['text'] }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2" class="text-center text-muted py-4">
                                                                No hay pagos registrados.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pagos recientes end -->

                        <!-- Pedidos por estado (dona) -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="h-100">
                                <div class="card o-hidden card-hover">
                                    <div class="card-header border-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="card-header-title">
                                                <h4>Pedidos por estado</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="pie-chart">
                                            <div id="pie-chart-visitors"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pedidos por estado end -->


                        <!-- Pendientes del panel -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card o-hidden card-hover">
                                <div class="card-header border-0">
                                    <div class="card-header-title">
                                        <h4>Pendientes</h4>
                                    </div>
                                </div>

                                <div class="card-body pt-0">
                                    <ul class="to-do-list" id="admin-tareas-list">
                                        @forelse ($tareasPendientes as $tarea)
                                            <li class="to-do-item">
                                                <div class="form-check user-checkbox">
                                                    <input class="checkbox_animated check-it admin-task-check"
                                                           type="checkbox"
                                                           value="1"
                                                           id="admin-task-{{ $tarea->id }}"
                                                           data-admin-task="{{ $tarea->id }}">
                                                </div>
                                                <div class="to-do-list-name">
                                                    <label for="admin-task-{{ $tarea->id }}" class="mb-0 w-100" style="cursor: pointer;">
                                                        <strong>{{ $tarea->titulo }}</strong>
                                                        <p class="mb-0">{{ $tarea->descripcion }}</p>
                                                    </label>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="to-do-item text-muted py-3 text-center">
                                                No hay pendientes por atender.
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Pendientes end -->


                    </div>
                    @endif
                </div>
                <!-- Container-fluid Ends-->

                <!-- footer start-->
                <div class="container-fluid">
                    <footer class="footer">
                        <div class="row">
                            <div class="col-md-12 footer-copyright text-center">
                                <p class="mb-0">Copyright 2022 © Fastkart theme by pixelstrap</p>
                            </div>
                        </div>
                    </footer>
                </div>
                <!-- footer End-->
            </div>
            <!-- index body end -->

        </div>
        <!-- Page Body End -->
    </div>
    <!-- page-wrapper End-->

    <!-- Modal Start -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title" id="staticBackdropLabel">Logging Out</h5>
                    <p>Are you sure you want to log out?</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="button-box">
                        <button type="button" class="btn btn--no" data-bs-dismiss="modal">No</button>
                        <button type="button" class="btn  btn--yes btn-primary">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End -->

    @include('partials.admin.scripts')
</body>

</html>
