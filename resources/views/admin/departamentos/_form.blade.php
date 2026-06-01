@php
    $esEdicion = isset($departamento) && $departamento !== null;
    $valor = function (string $campo, mixed $default = '') use ($esEdicion, $departamento) {
        $anterior = old($campo);
        if ($anterior !== null) {
            return $anterior;
        }
        if ($esEdicion) {
            return $departamento->{$campo} ?? $default;
        }

        return $default;
    };
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
                                    <h5>Información del departamento</h5>
                                </div>

                                @if ($esEdicion)
                                    <div class="mb-4 row align-items-center">
                                        <label class="form-label-title col-sm-3 mb-0">ID</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-plaintext mb-0">{{ $departamento->Id_Departamento }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0" for="Nom_Departamento">Nombre</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('Nom_Departamento') is-invalid @enderror"
                                               type="text"
                                               id="Nom_Departamento"
                                               name="Nom_Departamento"
                                               value="{{ $valor('Nom_Departamento') }}"
                                               placeholder="Ej. Guatemala"
                                               maxlength="200"
                                               required>
                                        @error('Nom_Departamento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if ($esEdicion && ($departamento->municipios_count ?? 0) > 0)
                                    <div class="mb-4 row align-items-center">
                                        <label class="form-label-title col-sm-3 mb-0">Municipios</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-plaintext mb-0 text-muted">
                                                {{ $departamento->municipios_count }} municipio{{ $departamento->municipios_count === 1 ? '' : 's' }} asociado{{ $departamento->municipios_count === 1 ? '' : 's' }}.
                                                No se puede eliminar el departamento mientras tenga municipios.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('admin.departamentos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
