@php
    $rolSeleccionado = (int) old('Id_Rol', $usuario->Id_Rol ?? 0);
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-sm-8 m-auto">
                    <form method="POST"
                          action="{{ $formAction }}"
                          class="theme-form theme-form-2 mega-form">
                        @csrf
                        @if (($formMethod ?? 'POST') !== 'POST')
                            @method($formMethod)
                        @endif

                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-2">
                                    <h5>Datos del usuario</h5>
                                </div>

                                <p class="text-muted small mb-4">
                                    Por políticas de seguridad, solo puedes modificar el rol. Los demás datos los gestiona el propio usuario.
                                </p>

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0">Nombre</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-plaintext mb-0">{{ $usuario->Usu_Nombre }}</p>
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0">Correo</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-plaintext mb-0">{{ $usuario->Usu_Correo }}</p>
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0">Teléfono</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-plaintext mb-0">{{ $usuario->Usu_Telefono ?: '—' }}</p>
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-start">
                                    <label class="form-label-title col-sm-3 mb-0">Dirección</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-plaintext mb-0">{{ $usuario->textoDireccionPrincipal() }}</p>
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0" for="Id_Rol">Rol</label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('Id_Rol') is-invalid @enderror"
                                                id="Id_Rol"
                                                name="Id_Rol"
                                                required>
                                            @foreach ($roles as $rol)
                                                <option value="{{ $rol->Id_Rol }}"
                                                    @selected($rolSeleccionado === (int) $rol->Id_Rol)>
                                                    {{ $rol->Rol_Nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Id_Rol')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-solid">{{ $submitLabel }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
