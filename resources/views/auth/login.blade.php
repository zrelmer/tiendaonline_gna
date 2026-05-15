@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Iniciar sesión</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Iniciar sesión</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- LOGIN -->
<section class="log-in-section background-image-2 section-b-space">
    <div class="container-fluid-lg w-100">
        <div class="row">

            <!-- IMAGEN -->
            <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                <div class="image-contain">
                    <img src="{{ asset('assets/images/inner-page/log-in.png') }}"
                         class="img-fluid" alt="Login">
                </div>
            </div>

            <!-- FORMULARIO -->
            <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                <div class="log-in-box">

                    <div class="log-in-title text-center">
                        <h3>Bienvenido a GNA Core</h3>
                        <h4>Accede a tu cuenta</h4>
                    </div>

                    <!-- MENSAJE GLOBAL -->
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="input-box">
                        <form method="POST" action="{{ route('login') }}" class="row g-4">
                            @csrf

                            <!-- EMAIL -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating log-in-form">
                                    <input type="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="Correo electrónico"
                                           value="{{ old('email') }}"
                                           required autofocus>

                                    <label>Correo electrónico</label>

                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating log-in-form">
                                    <input type="password"
                                           name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Contraseña"
                                           required>

                                    <label>Contraseña</label>

                                    @error('password')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- RECORDAR -->
                            <div class="col-12">
                                <div class="forgot-box d-flex justify-content-between">
                                    <div class="form-check remember-box">
                                        <input class="checkbox_animated check-box"
                                               type="checkbox"
                                               name="remember"
                                               id="remember">

                                        <label for="remember">Recordarme</label>
                                    </div>

                                    <a href="{{ route('password.request') }}" class="forgot-password">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                </div>
                            </div>

                            <!-- BOTÓN -->
                            <div class="col-12">
                                <button class="btn btn-animation w-100" type="submit">
                                    Iniciar sesión
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- DIVISOR -->
                    <div class="other-log-in">
                        <h6>o</h6>
                    </div>

                    <!-- LOGIN SOCIAL (OPCIONAL) -->
                    <div class="log-in-button">
                        <ul>
                            <li>
                                <a href="#" class="btn google-button w-100">
                                    <img src="{{ asset('assets/images/inner-page/google.png') }}" alt="">
                                    Continuar con Google
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- REGISTRO -->
                    <div class="sign-up-box text-center">
                        <h4>¿No tienes cuenta?</h4>
                        <a href="{{ route('register') }}">Crear cuenta</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@include('partials.footerlogin')
@endsection
