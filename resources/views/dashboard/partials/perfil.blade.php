@php
    $direccionPrincipal = $direcciones->first();
    $textoDireccionPrincipal = $direccionPrincipal
        ? collect([
            $direccionPrincipal->Direccion,
            $direccionPrincipal->municipio?->Nom_Municipio,
            $direccionPrincipal->municipio?->departamento?->Nom_Departamento,
        ])->filter()->implode(', ')
        : null;
@endphp

<div class="dashboard-profile">
    <div class="title">
        <h2>Mi perfil</h2>
        <span class="title-leaf">
            <svg class="icon-width bg-gray">
                <use xlink:href="{{ asset('assets/svg/leaf.svg') }}#leaf"></use>
            </svg>
        </span>
    </div>

    <div class="profile-detail dashboard-bg-box dashboard-profile-summary">
        <div class="dashboard-title">
            <h3>Información de cuenta</h3>
        </div>

        <div class="profile-name-detail">
            <div class="d-sm-flex align-items-center d-block">
                <h3>{{ $usuario->Usu_Nombre }}</h3>
            </div>
        </div>

        <div class="location-profile">
            <ul>
                @if ($textoDireccionPrincipal)
                    <li>
                        <div class="location-box">
                            <i data-feather="map-pin"></i>
                            <h6>{{ $textoDireccionPrincipal }}</h6>
                        </div>
                    </li>
                @endif
                <li>
                    <div class="location-box">
                        <i data-feather="mail"></i>
                        <h6>{{ $usuario->Usu_Correo }}</h6>
                    </div>
                </li>
                @if ($usuario->Usu_Telefono)
                    <li>
                        <div class="location-box">
                            <i data-feather="phone"></i>
                            <h6>{{ $usuario->Usu_Telefono }}</h6>
                        </div>
                    </li>
                @endif
                <li>
                    <div class="location-box">
                        <i data-feather="calendar"></i>
                        <h6>Miembro desde {{ $usuario->created_at?->format('d/m/Y') ?? '—' }}</h6>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="profile-about dashboard-bg-box mt-4">
        <div class="row">
            <div class="col-xxl-7">
                <div class="dashboard-title mb-3">
                    <h3>Datos personales</h3>
                </div>

                @if ($errors->profile->any() && session('tab') === 'profile')
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->profile->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('dashboard.profile.update') }}" class="dashboard-profile-form">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label" for="Usu_Nombre">Nombre completo</label>
                        <input type="text"
                               class="form-control"
                               id="Usu_Nombre"
                               name="Usu_Nombre"
                               maxlength="200"
                               value="{{ old('Usu_Nombre', $usuario->Usu_Nombre) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="Usu_Correo">Correo electrónico</label>
                        <input type="email"
                               class="form-control"
                               id="Usu_Correo"
                               name="Usu_Correo"
                               maxlength="150"
                               value="{{ old('Usu_Correo', $usuario->Usu_Correo) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="Usu_Telefono">Teléfono</label>
                        <input type="text"
                               class="form-control"
                               id="Usu_Telefono"
                               name="Usu_Telefono"
                               maxlength="20"
                               value="{{ old('Usu_Telefono', $usuario->Usu_Telefono) }}"
                               required>
                    </div>

                    <button type="submit" class="btn theme-bg-color text-white btn-sm fw-bold">
                        Guardar cambios
                    </button>
                </form>

                <div class="dashboard-title mb-3 mt-4 pt-2">
                    <h3>Cambiar contraseña</h3>
                </div>

                @if ($errors->password->any() && session('tab') === 'profile')
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->password->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('dashboard.profile.password') }}" class="dashboard-password-form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="current_password">Contraseña actual</label>
                        <input type="password"
                               class="form-control"
                               id="current_password"
                               name="current_password"
                               autocomplete="current-password"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Nueva contraseña</label>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               autocomplete="new-password"
                               minlength="6"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation"
                               name="password_confirmation"
                               autocomplete="new-password"
                               minlength="6"
                               required>
                    </div>

                    <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold">
                        Actualizar contraseña
                    </button>
                </form>

                @if (! $textoDireccionPrincipal)
                    <p class="text-content small mt-4 mb-0">
                        Puedes registrar direcciones de envío en la pestaña <strong>Direcciones</strong>.
                    </p>
                @endif
            </div>

            <div class="col-xxl-5 d-none d-xxl-block">
                <div class="profile-image text-center">
                    <img src="{{ asset('assets/images/inner-page/dashboard-profile.png') }}"
                         class="img-fluid blur-up lazyload dashboard-profile-illustration"
                         alt="">
                </div>
            </div>
        </div>
    </div>
</div>
