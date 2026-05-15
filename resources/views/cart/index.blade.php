@extends('layouts.app')

@section('title', 'Lista de deseos')

@section('content')

<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Carrito de Compras</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Carrito de Compras</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Cart Section Start -->
<section class="cart-section section-b-space">
    <div class="container-fluid-lg">

        {{-- CART EMPTY --}}
        <div class="row" id="cart-empty" style="display: none;">
            <div class="col-12">
                <div class="text-center py-5">
                    <img src="{{ asset('assets/images/svg/order.svg') }}"
                         class="img-fluid mb-4"
                         width="220"
                         alt="Carrito vacío">

                    <h3 class="fw-bold">Tu carrito está vacío</h3>

                    <p class="text-content">
                        Agrega productos al carrito para continuar comprando.
                    </p>

                    <a href="{{ route('home') }}"
                       class="btn btn-animation mt-3">
                        Ir a comprar
                    </a>
                </div>
            </div>
        </div>

        {{-- CART CONTENT --}}
        <div class="row g-sm-5 g-3" id="cart-content">

            {{-- PRODUCTS --}}
            <div class="col-xxl-9">

                <div class="cart-table">
                    <div class="table-responsive-xl">

                        <table class="table">
                            <tbody id="cart-items">
                                {{-- JS Render --}}
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>

            {{-- SUMMARY --}}
            <div class="col-xxl-3">

                <div class="summery-box p-sticky">

                    <div class="summery-header">
                        <h3>Resumen del carrito</h3>
                    </div>

                    <div class="summery-contain">

                        <ul>
                            <li>
                                <h4>Subtotal</h4>
                                <h4 class="price" id="subtotal">
                                    Q0.00
                                </h4>
                            </li>

                            <li>
                                <h4>Envío</h4>
                                <h4 class="price" id="shipping">
                                    Q0.00
                                </h4>
                            </li>
                        </ul>

                    </div>

                    <ul class="summery-total">
                        <li class="list-total border-top-0">
                            <h4>Total</h4>

                            <h4 class="price theme-color" id="total">
                                Q0.00
                            </h4>
                        </li>
                    </ul>

                    <div class="button-group cart-button">
                        <ul>

                            <li>
                                <a href="{{ route('home') }}"
                                   class="btn btn-animation proceed-btn fw-bold w-100">
                                    Proceder al pago
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('home') }}"
                                   class="btn btn-light shopping-button text-dark w-100">
                                    <i class="fa-solid fa-arrow-left-long"></i>
                                    Continuar comprando
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>

            </div>

        </div>
    </div>
</section>
<!-- Cart Section End -->
@endsection

