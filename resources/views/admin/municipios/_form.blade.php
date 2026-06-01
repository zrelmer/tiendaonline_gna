@php
    $esEdicion = isset($municipio) && $municipio !== null;
    $valor = function (string $campo, mixed $default = '') use ($esEdicion, $municipio) {
        $anterior = old($campo);
        if ($anterior !== null) {
            return $anterior;
        }
        if ($esEdicion) {
            return $municipio->{$campo} ?? $default;
        }

        return $default;
    };
    $departamentoSeleccionado = (int) $valor('Id_Departamento', 0);
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
                                    <h5>Información del municipio</h5>
                                </div>

                                @if ($esEdicion)
                                    <div class="mb-4 row align-items-center">
                                        <label class="form-label-title col-sm-3 mb-0">ID</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-plaintext mb-0">{{ $municipio->Id_Municipio }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0" for="Id_Departamento">Departamento</label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('Id_Departamento') is-invalid @enderror"
                                                id="Id_Departamento"
                                                name="Id_Departamento"
                                                required>
                                            <option value="" disabled @selected($departamentoSeleccionado === 0)>Selecciona un departamento</option>
                                            @foreach ($departamentos as $departamento)
                                                <option value="{{ $departamento->Id_Departamento }}"
                                                    @selected($departamentoSeleccionado === (int) $departamento->Id_Departamento)>
                                                    {{ $departamento->Nom_Departamento }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Id_Departamento')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0" for="Nom_Municipio">Nombre</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('Nom_Municipio') is-invalid @enderror"
                                               type="text"
                                               id="Nom_Municipio"
                                               name="Nom_Municipio"
                                               value="{{ $valor('Nom_Municipio') }}"
                                               placeholder="Ej. Ciudad de Guatemala"
                                               maxlength="200"
                                               required>
                                        @error('Nom_Municipio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if ($esEdicion && ($municipio->direcciones_count ?? 0) > 0)
                                    <div class="mb-4 row align-items-center">
                                        <label class="form-label-title col-sm-3 mb-0">Direcciones</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-plaintext mb-0 text-muted">
                                                {{ $municipio->direcciones_count }} dirección{{ $municipio->direcciones_count === 1 ? '' : 'es' }} asociada{{ $municipio->direcciones_count === 1 ? '' : 's' }}.
                                                No se puede eliminar el municipio mientras tenga direcciones.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('admin.municipios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
