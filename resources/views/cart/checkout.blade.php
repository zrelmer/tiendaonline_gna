@extends('layouts.app')

@section('title', 'Checkout')

@section('content')

<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Checkout</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Checkout</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Checkout section Start -->
<section class="checkout-section-2 section-b-space">
    <div class="container-fluid-lg">
        @if (session('success'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('cart.checkout.store') }}" id="checkout-form">
            @csrf
        <div class="row g-sm-4 g-3">
            <div class="col-lg-8">
                <div class="left-sidebar-checkout">
                    <div class="checkout-detail-box">
                        <ul>
                            <li>
                                <div class="checkout-icon">
                                    <lord-icon target=".nav-item" src="../../ggihhudh.json" trigger="loop-on-hover" colors="primary:#121331,secondary:#646e78,tertiary:#0baf9a" class="lord-icon">
                                    </lord-icon>
                                </div>
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Dirección de Entrega</h4>
                                    </div>

                                    <div class="checkout-detail">
                                        @if ($direcciones->isEmpty())
                                            <p class="text-content mb-0">
                                                No tienes direcciones de entrega registradas.
                                                Agrega una en tu perfil para continuar con el pedido.
                                            </p>
                                        @else
                                            <div class="row g-4">
                                                @foreach ($direcciones as $direccion)
                                                    @php
                                                        $municipio = $direccion->municipio;
                                                        $departamento = $municipio?->departamento;
                                                    @endphp
                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="delivery-address-box">
                                                            <div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input"
                                                                           type="radio"
                                                                           name="id_direccion"
                                                                           id="direccion-{{ $direccion->Id_Direccion }}"
                                                                           value="{{ $direccion->Id_Direccion }}"
                                                                           @checked((int) old('id_direccion', $direcciones->first()->Id_Direccion) === (int) $direccion->Id_Direccion)>
                                                                </div>

                                                                <div class="label">
                                                                    <label for="direccion-{{ $direccion->Id_Direccion }}">
                                                                        Dirección {{ $loop->iteration }}
                                                                    </label>
                                                                </div>

                                                                <ul class="delivery-address-detail">
                                                                    <li>
                                                                        <h4 class="fw-500">{{ $usuario->Usu_Nombre }}</h4>
                                                                    </li>

                                                                    <li>
                                                                        <p class="text-content">
                                                                            <span class="text-title">Dirección:</span>
                                                                            {{ $direccion->Direccion }}
                                                                        </p>
                                                                    </li>

                                                                    <li>
                                                                        <h6 class="text-content">
                                                                            <span class="text-title">Municipio:</span>
                                                                            {{ $municipio?->Nom_Municipio ?? '—' }}
                                                                        </h6>
                                                                    </li>

                                                                    <li>
                                                                        <h6 class="text-content">
                                                                            <span class="text-title">Departamento:</span>
                                                                            {{ $departamento?->Nom_Departamento ?? '—' }}
                                                                        </h6>
                                                                    </li>

                                                                    <li>
                                                                        <h6 class="text-content mb-0">
                                                                            <span class="text-title">Teléfono:</span>
                                                                            {{ $usuario->Usu_Telefono }}
                                                                        </h6>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <!--implementacion futura-->
                            {{-- <li>
                                <div class="checkout-icon">
                                    <lord-icon target=".nav-item" src="../../oaflahpk.json" trigger="loop-on-hover" colors="primary:#0baf9a" class="lord-icon">
                                    </lord-icon>
                                </div>
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Delivery Option</h4>
                                    </div>

                                    <div class="checkout-detail">
                                        <div class="row g-4">
                                            <div class="col-xxl-6">
                                                <div class="delivery-option">
                                                    <div class="delivery-category">
                                                        <div class="shipment-detail">
                                                            <div class="form-check custom-form-check hide-check-box">
                                                                <input class="form-check-input" type="radio" name="standard" id="standard" checked="">
                                                                <label class="form-check-label" for="standard">Standard
                                                                    Delivery Option</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xxl-6">
                                                <div class="delivery-option">
                                                    <div class="delivery-category">
                                                        <div class="shipment-detail">
                                                            <div class="form-check mb-0 custom-form-check show-box-checked">
                                                                <input class="form-check-input" type="radio" name="standard" id="future">
                                                                <label class="form-check-label" for="future">Future
                                                                    Delivery Option</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 future-box">
                                                <div class="future-option">
                                                    <div class="row g-md-0 gy-4">
                                                        <div class="col-md-6">
                                                            <div class="delivery-items">
                                                                <div>
                                                                    <h5 class="items text-content"><span>3
                                                                            Items</span>@
                                                                        $693.48</h5>
                                                                    <h5 class="charge text-content">Delivery Charge
                                                                        $34.67
                                                                        <button type="button" class="btn p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Extra Charge">
                                                                            <i class="fa-solid fa-circle-exclamation"></i>
                                                                        </button>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <form class="form-floating theme-form-floating date-box">
                                                                <input type="date" class="form-control">
                                                                <label>Select Date</label>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li> --}}

                            <li>
                                <div class="checkout-icon">
                                    <lord-icon target=".nav-item" src="../../qmcsqnle.json" trigger="loop-on-hover" colors="primary:#0baf9a,secondary:#0baf9a" class="lord-icon">
                                    </lord-icon>
                                </div>
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Método de pago</h4>
                                    </div><!-- fin del checkout-title -->
                                    <div class="checkout-detail">
                                        <div class="accordion accordion-flush custom-accordion" id="accordionMetodosPago">
                                            @forelse ($metodosPago as $metodo)
                                                <div class="accordion-item">
                                                    <div class="accordion-header" id="flush-heading-{{ $metodo->Id_MetodoPago }}">
                                                        <div class="accordion-button collapsed"
                                                             data-bs-toggle="collapse"
                                                             data-bs-target="#flush-collapse-{{ $metodo->Id_MetodoPago }}">
                                                            <div class="custom-form-check form-check mb-0">
                                                                <label class="form-check-label" for="metodo-{{ $metodo->Id_MetodoPago }}">
                                                                    <input class="form-check-input mt-0"
                                                                           type="radio"
                                                                           name="id_metodo_pago"
                                                                           id="metodo-{{ $metodo->Id_MetodoPago }}"
                                                                           value="{{ $metodo->Id_MetodoPago }}"
                                                                           @checked(old('id_metodo_pago', 3) == $metodo->Id_MetodoPago)>
                                                                    {{ $metodo->MetPag_Descripcion }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="flush-collapse-{{ $metodo->Id_MetodoPago }}"
                                                         class="accordion-collapse collapse"
                                                         data-bs-parent="#accordionMetodosPago">
                                                        <div class="accordion-body">
                                                            @includeFirst([
                                                                'cart.checkout.partials.payment.' . $metodo->plantilla,
                                                                'cart.checkout.partials.payment.generico',
                                                            ], [
                                                                'metodo' => $metodo,
                                                                'pedidosPendientesBoleta' => $pedidosPendientesBoleta,
                                                            ])
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-content mb-0">
                                                    No hay métodos de pago configurados.
                                                    Ejecuta el seeder de métodos de pago.
                                                </p>
                                            @endforelse
                                        </div><!-- fin del accordion accordion-flush custom-accordion -->
                                    </div><!-- fin del checkout-detail -->
                                </div><!-- fin del checkout-box -->
                            </li>

                            <li>
                                <div class="checkout-icon">
                                    <lord-icon target=".nav-item" src="../../qmcsqnle.json" trigger="loop-on-hover" colors="primary:#0baf9a,secondary:#0baf9a" class="lord-icon">
                                    </lord-icon>
                                </div>
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Pagar</h4>
                                    </div><!-- fin del checkout-title -->
                                    <button type="submit"
                                            class="btn theme-bg-color text-white btn-md w-100 mt-4 fw-bold"
                                            @disabled($direcciones->isEmpty() || $lineasCarrito->isEmpty())>
                                        Realizar compra
                                    </button>
                                    @if ($lineasCarrito->isEmpty())
                                        <p class="text-content small mt-2 mb-0">
                                            Tu carrito está vacío. Agrega productos antes de confirmar.
                                        </p>
                                    @endif
                                </div><!-- fin del checkout-box -->
                            </li>

                        </ul>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="right-side-summery-box">
                    <div class="summery-box-2">
                        <div class="summery-header">
                            <h3>Resumen del pedido</h3>
                        </div>

                        <ul class="summery-contain">
                            @forelse ($lineasCarrito as $linea)
                                @php
                                    $producto = $linea->producto;
                                    $imagen = $producto?->imagenes?->sortBy('orden')->first();
                                @endphp
                                <li>
                                    @if ($imagen)
                                        <img src="{{ asset($imagen->url) }}"
                                             class="img-fluid blur-up lazyloaded checkout-image"
                                             alt="{{ $producto->Prod_Nombre }}">
                                    @endif
                                    <h4>{{ $producto->Prod_Nombre }} <span>X {{ $linea->Cantidad }}</span></h4>
                                    <h4 class="price">Q{{ number_format((float) $linea->Precio * (int) $linea->Cantidad, 2) }}</h4>
                                </li>
                            @empty
                                <li>
                                    <p class="text-content mb-0">No hay productos en el carrito.</p>
                                </li>
                            @endforelse
                        </ul>

                        <ul class="summery-total">
                            <li>
                                <h4>Subtotal</h4>
                                <h4 class="price">Q{{ number_format($subtotalCarrito, 2) }}</h4>
                            </li>

                            <li>
                                <h4>Envío</h4>
                                <h4 class="price">Q{{ number_format($envioCarrito, 2) }}</h4>
                            </li>

                            <li class="list-total">
                                <h4>Total</h4>
                                <h4 class="price theme-color">Q{{ number_format($totalCarrito, 2) }}</h4>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </form>

        <form id="form-boleta-pago"
              action="{{ route('boleta-pago.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="d-none"
              aria-hidden="true">
            @csrf
        </form>
    </div>
</section>
<!-- Checkout section End -->

@push('scripts')
@if (session('pedido_creado'))
<script>
    try {
        localStorage.removeItem('carrito');
    } catch (e) {}

    (function () {
        var metodoId = @json(session('abrir_metodo_pago'));
        if (!metodoId) return;

        var radio = document.getElementById('metodo-' + metodoId);
        if (radio) {
            radio.checked = true;
        }

        var collapse = document.getElementById('flush-collapse-' + metodoId);
        if (collapse) {
            collapse.classList.add('show');
        }

        var heading = document.getElementById('flush-heading-' + metodoId);
        if (heading) {
            var btn = heading.querySelector('.accordion-button');
            if (btn) {
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
            }
        }
    })();
</script>
@endif
@endpush

@endsection
