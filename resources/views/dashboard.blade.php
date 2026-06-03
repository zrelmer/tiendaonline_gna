@extends('layouts.app')

@section('title', 'Mi cuenta')

@section('content')

<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Mi cuenta</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Mi cuenta</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- User Dashboard Section Start -->
<section class="user-dashboard-section user-dashboard-page section-b-space">
    <div class="container-fluid-lg">
        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="dashboard-left-sidebar" id="dashboardSidebar" aria-hidden="true">
                    <div class="close-button d-flex d-lg-none">
                        <button type="button" class="close-sidebar" aria-label="Cerrar menú">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="profile-box">
                        <div class="cover-image">
                            <img src="{{ asset('assets/images/inner-page/cover-img.jpg') }}" class="img-fluid blur-up lazyload" alt="">
                        </div>

                        <div class="profile-contain">
                            <div class="profile-image">
                                <div class="position-relative">
                                    <img src="{{ asset('assets/images/inner-page/user/5.png') }}" class="blur-up lazyload update_img" alt="">
                                </div>
                            </div>

                            <div class="profile-name">
                                <h3>{{ $usuario->Usu_Nombre }}</h3>
                                <h6 class="text-content">{{ $usuario->Usu_Correo }}</h6>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills user-nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-dashboard-tab" data-bs-toggle="pill" data-bs-target="#pills-dashboard" type="button"><i data-feather="home"></i>
                                Mi Panel</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-order-tab" data-bs-toggle="pill" data-bs-target="#pills-order" type="button"><i data-feather="shopping-bag"></i>Mis Pedidos</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-address-tab" data-bs-toggle="pill" data-bs-target="#pills-address" type="button" role="tab"><i data-feather="map-pin"></i>Direcciones</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab"><i data-feather="user"></i>
                                Perfil</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-tracking-tab" data-bs-toggle="pill" data-bs-target="#pills-tracking" type="button" role="tab"><i data-feather="truck"></i>
                                Seguimiento de órdenes</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-quotes-tab" data-bs-toggle="pill" data-bs-target="#pills-quotes" type="button" role="tab"><i data-feather="clipboard"></i>
                                Cotizaciones</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-privacy-tab" data-bs-toggle="pill" data-bs-target="#pills-privacy" type="button" role="tab"><i data-feather="shield"></i>
                                Privacidad</button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <button type="button"
                        class="btn dashboard-menu-toggle btn-animation btn-md fw-bold d-block mb-4 d-lg-none w-100"
                        id="dashboardMenuToggle"
                        aria-controls="dashboardSidebar"
                        aria-expanded="false">
                    <i class="fa-solid fa-bars me-2"></i> Mostrar menú
                </button>
                <div class="dashboard-right-sidebar">
                    @if (session('success'))
                        <div class="alert alert-success dashboard-alert">{{ session('success') }}</div>
                    @endif

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-dashboard" role="tabpanel">
                            <div class="dashboard-home">
                                <div class="title">
                                    <h2>Mi Panel</h2>
                                    <span class="title-leaf">
                                        <svg class="icon-width bg-gray">
                                            <use xlink:href="{{ asset('assets/svg/leaf.svg') }}#leaf"></use>
                                        </svg>
                                    </span>
                                </div>

                                <div class="dashboard-user-name">
                                    <h6 class="text-content">Hola, <b class="text-title">{{ $usuario->Usu_Nombre }}</b></h6>
                                    <p class="text-content">Desde el panel de control de tu cuenta puedes ver un resumen
                                        de tu actividad reciente y actualizar tu información. Selecciona una opción del
                                        menú lateral para ver o editar tus datos.</p>
                                </div>

                                <div class="total-box">
                                    <div class="row g-sm-4 g-3">
                                        <div class="col-xxl-4 col-lg-6 col-md-4 col-sm-6">
                                            <div class="total-contain">
                                                <img src="{{ asset('assets/images/svg/order.svg') }}" class="img-1 blur-up lazyload" alt="">
                                                <img src="{{ asset('assets/images/svg/order.svg') }}" class="blur-up lazyload" alt="">
                                                <div class="total-detail">
                                                    <h5>Total de pedidos</h5>
                                                    <h3>{{ $totalPedidos }}</h3>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-lg-6 col-md-4 col-sm-6">
                                            <div class="total-contain">
                                                <img src="{{ asset('assets/images/svg/pending.svg') }}" class="img-1 blur-up lazyload" alt="">
                                                <img src="{{ asset('assets/images/svg/pending.svg') }}" class="blur-up lazyload" alt="">
                                                <div class="total-detail">
                                                    <h5>Pedidos en curso</h5>
                                                    <h3>{{ $pedidosPendientes }}</h3>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-lg-6 col-md-4 col-sm-6">
                                            <div class="total-contain">
                                                <img src="{{ asset('assets/images/svg/wishlist.svg') }}" class="img-1 blur-up lazyload" alt="">
                                                <img src="{{ asset('assets/images/svg/wishlist.svg') }}" class="blur-up lazyload" alt="">
                                                <div class="total-detail">
                                                    <h5>Lista de deseos</h5>
                                                    <h3>{{ $totalListaDeseos }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-order" role="tabpanel">
                            @include('dashboard.partials.ordenes')
                        </div>

                        <div class="tab-pane fade" id="pills-address" role="tabpanel">
                            @include('dashboard.partials.direcciones')
                        </div>

                        <div class="tab-pane fade" id="pills-profile" role="tabpanel">
                            @include('dashboard.partials.perfil')
                        </div>

                        <div class="tab-pane fade" id="pills-tracking" role="tabpanel">
                            @include('dashboard.partials.seguimiento')
                        </div>

                        <div class="tab-pane fade" id="pills-quotes" role="tabpanel">
                            @include('dashboard.partials.cotizaciones')
                        </div>

                        <div class="tab-pane fade" id="pills-privacy" role="tabpanel">
                            @include('dashboard.partials.privacidad')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- User Dashboard Section End -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tabMap = {
            orders: 'pills-order-tab',
            addresses: 'pills-address-tab',
            profile: 'pills-profile-tab',
            tracking: 'pills-tracking-tab',
            quotes: 'pills-quotes-tab',
            privacy: 'pills-privacy-tab',
        };
        var activeTab = @json(session('tab'));
        var tabButton = activeTab ? document.getElementById(tabMap[activeTab] || '') : null;

        if (tabButton && window.bootstrap) {
            window.bootstrap.Tab.getOrCreateInstance(tabButton).show();
        }

        if (window.feather) {
            window.feather.replace();
        }
    });
</script>
@endpush

