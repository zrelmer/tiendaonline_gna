@extends('layouts.app')

@section('title', 'Registrarse')

@section('noFooter', true)

@section('content')

<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Registrarse</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Crear cuenta</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- REGISTER -->
<section class="log-in-section section-b-space">
    <div class="container-fluid-lg w-100">
        <div class="row">

            <!-- IMAGEN -->
            <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                <div class="image-contain">
                    <img src="{{ asset('assets/images/inner-page/sign-up.png') }}" class="img-fluid" alt="Registro">
                </div>
            </div>

            <!-- FORMULARIO -->
            <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                <div class="log-in-box">

                    <div class="log-in-title">
                        <h3>Bienvenido a GNA Core</h3>
                        <h4>Crear nueva cuenta</h4>
                    </div>

                    <!-- MENSAJES -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="input-box">
                        <form method="POST" action="{{ route('register') }}" class="row g-4">
                            @csrf

                            <!-- NOMBRE -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="text"
                                           name="Usu_Nombre"
                                           class="form-control"
                                           placeholder="Nombre completo"
                                           value="{{ old('Usu_Nombre') }}"
                                           required>
                                    <label>Nombre completo</label>
                                </div>
                            </div>

                            <!-- EMAIL -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="email"
                                           name="Usu_Correo"
                                           class="form-control"
                                           placeholder="Correo electrónico"
                                           value="{{ old('Usu_Correo') }}"
                                           required>
                                    <label>Correo electrónico</label>
                                </div>
                            </div>

                            <!-- TELEFONO -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="text"
                                           name="Usu_Telefono"
                                           class="form-control"
                                           placeholder="Teléfono"
                                           value="{{ old('Usu_Telefono') }}"
                                           required>
                                    <label>Teléfono</label>
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="password"
                                           name="Usu_Pass"
                                           class="form-control"
                                           placeholder="Contraseña"
                                           required>
                                    <label>Contraseña</label>
                                </div>
                            </div>

                            <!-- CONFIRMAR PASSWORD -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="password"
                                           name="Usu_Pass_confirmation"
                                           class="form-control"
                                           placeholder="Confirmar contraseña"
                                           required>
                                    <label>Confirmar contraseña</label>
                                </div>
                            </div>

                            <!-- TERMINOS -->
                            <div class="col-12">
                                <div class="forgot-box">
                                    <div class="form-check ps-0 m-0 remember-box">
                                        <input class="checkbox_animated check-box" type="checkbox" required>
                                        <label class="form-check-label">
                                            Acepto los <span>Términos</span> y <span>Política de privacidad</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- BOTON -->
                            <div class="col-12">
                                <button class="btn btn-animation w-100" type="submit">
                                    Registrarse
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- DIVISOR -->
                    <div class="other-log-in">
                        <h6>o</h6>
                    </div>

                    <!-- GOOGLE -->
                    <div class="log-in-button">
                        <ul>
                            <li>
                                <a href="#" class="btn google-button w-100">
                                    <img src="{{ asset('assets/images/inner-page/google.png') }}" alt="">
                                    Registrarse con Google
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- LOGIN -->
                    <div class="sign-up-box">
                        <h4>¿Ya tienes cuenta?</h4>
                        <a href="{{ route('login') }}">Iniciar sesión</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@include('partials.footerlogin')
@endsection
