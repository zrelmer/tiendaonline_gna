@extends('layouts.app')

@section('title', 'Recuperar contraseña')



@section('content')

<!-- Breadcrumb -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Recuperar contraseña</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Recuperar contraseña</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN -->
<section class="log-in-section section-b-space forgot-section">
    <div class="container-fluid-lg w-100">
        <div class="row">

            <!-- IMAGEN -->
            <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                <div class="image-contain">
                    <img src="{{ asset('assets/images/inner-page/forgot.png') }}"
                         class="img-fluid" alt="Recuperar contraseña">
                </div>
            </div>

            <!-- FORMULARIO -->
            <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="log-in-box">

                        <div class="log-in-title">
                            <h3>¿Olvidaste tu contraseña?</h3>
                            <h4>Ingresa tu correo y te enviaremos un enlace para recuperarla</h4>
                        </div>

                        <!-- MENSAJE DE ÉXITO -->
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- FORM -->
                        <div class="input-box">
                            <form method="POST" action="{{ route('password.email') }}" class="row g-4">
                                @csrf

                                <!-- EMAIL -->
                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="email"
                                               name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="ejemplo@correo.com"
                                               value="{{ old('email') }}"
                                               required>

                                        <label>Correo electrónico</label>

                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- BOTÓN -->
                                <div class="col-12">
                                    <button class="btn btn-animation w-100" type="submit">
                                        Enviar enlace de recuperación
                                    </button>
                                </div>

                            </form>
                        </div>

                        <!-- VOLVER -->
                        <div class="sign-up-box mt-3 text-center">
                            <h4>¿Recordaste tu contraseña?</h4>
                            <a href="{{ route('login') }}">Iniciar sesión</a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@include('partials.footerlogin')
@endsection