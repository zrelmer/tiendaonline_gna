@extends('layouts.app')

@section('title', 'Restablecer Contraseña')

@section('noFooter', true)

@section('content')

<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>Restablecer Contraseña</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Restablecer Contraseña</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- RESET PASSWORD -->
<section class="log-in-section section-b-space">
    <div class="container-fluid-lg w-100">
        <div class="row">

            <!-- IMAGEN -->
            <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                <div class="image-contain">
                    <img src="{{ asset('assets/images/inner-page/reset.png') }}" class="img-fluid" alt="Restablecer Contraseña">
                </div>
            </div>

            <!-- FORMULARIO -->
            <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                <div class="log-in-box">

                    <div class="log-in-title">
                        <h3>Bienvenido a GNA Core</h3>
                        <h4>Restablecer contraseña</h4>
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
                        <form method="POST" action="{{ route('password.store') }}" class="row g-4">
                            @csrf

                            <!-- TOKEN -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- EMAIL -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           class="form-control"
                                           placeholder="Correo electrónico"
                                           value="{{ old('email', $request->email) }}"
                                           required
                                           autofocus
                                           autocomplete="username">
                                    <label for="email">Correo electrónico</label>
                                </div>
                            </div>

                            <!-- NUEVA CONTRASEÑA -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control"
                                           placeholder="Nueva contraseña"
                                           required
                                           autocomplete="new-password">
                                    <label for="password">Nueva contraseña</label>
                                </div>
                            </div>

                            <!-- CONFIRMAR CONTRASEÑA -->
                            <div class="col-12">
                                <div class="form-floating theme-form-floating">
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="form-control"
                                           placeholder="Confirmar contraseña"
                                           required
                                           autocomplete="new-password">
                                    <label for="password_confirmation">Confirmar contraseña</label>
                                </div>
                            </div>

                            <!-- BOTON -->
                            <div class="col-12">
                                <button class="btn btn-animation w-100" type="submit">
                                    Restablecer Contraseña
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- DIVISOR -->
                    <div class="other-log-in">
                        <h6>o</h6>
                    </div>

                    <!-- LOGIN -->
                    <div class="sign-up-box">
                        <h4>¿Recordaste tu contraseña?</h4>
                        <a href="{{ route('login') }}">Iniciar sesión</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@include('partials.footerlogin')
@endsection
