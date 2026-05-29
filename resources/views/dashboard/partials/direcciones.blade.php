<div class="dashboard-address">
    <div class="title title-flex">
        <div>
            <h2>Mis direcciones</h2>
            <span class="title-leaf">
                <svg class="icon-width bg-gray">
                    <use xlink:href="{{ asset('assets/svg/leaf.svg') }}#leaf"></use>
                </svg>
            </span>
        </div>

        <button type="button"
                class="btn theme-bg-color text-white btn-sm fw-bold mt-lg-0 mt-3"
                data-bs-toggle="modal"
                data-bs-target="#add-address">
            <i data-feather="plus" class="me-2"></i> Agregar dirección
        </button>
    </div>

    @if ($errors->direccion->any() && session('tab') === 'addresses')
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->direccion->all() as $errorDireccion)
                    <li>{{ $errorDireccion }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($errors->has('direccion') && session('tab') === 'addresses')
        <div class="alert alert-danger">
            {{ $errors->first('direccion') }}
        </div>
    @endif

    <div class="row g-sm-4 g-3">
        @forelse ($direcciones as $index => $direccion)
            @php
                $municipio = $direccion->municipio;
                $departamento = $municipio?->departamento;
                $puedeEliminar = ($direccion->pedido_count ?? 0) === 0;
                $editarActivo = (int) session('editar_direccion_id') === (int) $direccion->Id_Direccion;
                $idDepartamento = $editarActivo
                    ? (int) old('id_departamento', $departamento?->Id_Departamento)
                    : (int) ($departamento?->Id_Departamento ?? 0);
                $idMunicipio = $editarActivo
                    ? (int) old('id_municipio', $direccion->Id_Municipio)
                    : (int) $direccion->Id_Municipio;
            @endphp

            <div class="col-xxl-4 col-xl-6 col-lg-12 col-md-6">
                <div class="address-box dashboard-address-card">
                    <div>
                        <div class="label">
                            <label>Dirección {{ $index + 1 }}</label>
                        </div>

                        <div class="table-responsive address-table">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2">{{ $usuario->Usu_Nombre }}</td>
                                    </tr>
                                    <tr>
                                        <td>Dirección :</td>
                                        <td><p class="mb-0">{{ $direccion->Direccion }}</p></td>
                                    </tr>
                                    <tr>
                                        <td>Municipio :</td>
                                        <td>{{ $municipio?->Nom_Municipio ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Departamento :</td>
                                        <td>{{ $departamento?->Nom_Departamento ?? '—' }}</td>
                                    </tr>
                                    @if ($usuario->Usu_Telefono)
                                        <tr>
                                            <td>Teléfono :</td>
                                            <td>{{ $usuario->Usu_Telefono }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button"
                                class="btn btn-sm add-button w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#editar-direccion-{{ $direccion->Id_Direccion }}">
                            <i data-feather="edit"></i> Editar
                        </button>
                        @if ($puedeEliminar)
                            <form method="POST"
                                  action="{{ route('dashboard.direcciones.destroy', $direccion) }}"
                                  class="w-100"
                                  onsubmit="return confirm('¿Eliminar esta dirección?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm add-button w-100 text-danger">
                                    <i data-feather="trash-2"></i> Eliminar
                                </button>
                            </form>
                        @else
                            <button type="button"
                                    class="btn btn-sm add-button w-100"
                                    disabled
                                    title="No se puede eliminar: está asociada a un pedido.">
                                <i data-feather="trash-2"></i> Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal fade"
                 id="editar-direccion-{{ $direccion->Id_Direccion }}"
                 tabindex="-1"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST"
                              action="{{ route('dashboard.direcciones.update', $direccion) }}"
                              class="dashboard-direccion-form"
                              data-departamento-inicial="{{ $idDepartamento }}"
                              data-municipio-inicial="{{ $idMunicipio }}">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header">
                                <h5 class="modal-title">Editar dirección</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-direccion-{{ $direccion->Id_Direccion }}">Dirección</label>
                                    <input type="text"
                                           class="form-control"
                                           id="edit-direccion-{{ $direccion->Id_Direccion }}"
                                           name="direccion"
                                           maxlength="200"
                                           value="{{ $editarActivo ? old('direccion', $direccion->Direccion) : $direccion->Direccion }}"
                                           required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="edit-departamento-{{ $direccion->Id_Direccion }}">Departamento</label>
                                    <select class="form-select dashboard-departamento-select"
                                            id="edit-departamento-{{ $direccion->Id_Direccion }}"
                                            name="id_departamento"
                                            required>
                                        <option value="">Selecciona un departamento</option>
                                        @foreach ($departamentos as $dep)
                                            <option value="{{ $dep->Id_Departamento }}"
                                                @selected($idDepartamento === (int) $dep->Id_Departamento)>
                                                {{ $dep->Nom_Departamento }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="edit-municipio-{{ $direccion->Id_Direccion }}">Municipio</label>
                                    <select class="form-select dashboard-municipio-select"
                                            id="edit-municipio-{{ $direccion->Id_Direccion }}"
                                            name="id_municipio"
                                            required>
                                        <option value="">Selecciona un municipio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn theme-bg-color text-white btn-sm">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="dashboard-bg-box p-4 text-center">
                    <p class="text-content mb-3">No tienes direcciones registradas.</p>
                    <button type="button"
                            class="btn theme-bg-color text-white btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#add-address">
                        Agregar primera dirección
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="add-address" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST"
                  action="{{ route('dashboard.direcciones.store') }}"
                  class="dashboard-direccion-form"
                  data-departamento-inicial="{{ (int) old('id_departamento') }}"
                  data-municipio-inicial="{{ (int) old('id_municipio') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="nueva-direccion-input">Dirección</label>
                        <input type="text"
                               class="form-control"
                               id="nueva-direccion-input"
                               name="direccion"
                               maxlength="200"
                               value="{{ old('direccion') }}"
                               required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="nueva-departamento-select">Departamento</label>
                        <select class="form-select dashboard-departamento-select"
                                id="nueva-departamento-select"
                                name="id_departamento"
                                required>
                            <option value="">Selecciona un departamento</option>
                            @foreach ($departamentos as $departamento)
                                <option value="{{ $departamento->Id_Departamento }}"
                                    @selected((int) old('id_departamento') === (int) $departamento->Id_Departamento)>
                                    {{ $departamento->Nom_Departamento }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="nueva-municipio-select">Municipio</label>
                        <select class="form-select dashboard-municipio-select"
                                id="nueva-municipio-select"
                                name="id_municipio"
                                required
                                disabled>
                            <option value="">Selecciona un municipio</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn theme-bg-color text-white btn-sm">Guardar dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var municipiosPorDepartamento = @json($municipiosPorDepartamento);

        function renderMunicipios(form) {
            var departamentoSelect = form.querySelector('.dashboard-departamento-select');
            var municipioSelect = form.querySelector('.dashboard-municipio-select');
            if (!departamentoSelect || !municipioSelect) {
                return;
            }

            var departamentoId = departamentoSelect.value;
            var municipioInicial = form.dataset.municipioInicial || '';
            var municipios = municipiosPorDepartamento[departamentoId] || [];

            municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';
            municipios.forEach(function (municipio) {
                var option = document.createElement('option');
                option.value = municipio.id;
                option.textContent = municipio.nombre;
                if (String(municipioInicial) === String(municipio.id)) {
                    option.selected = true;
                }
                municipioSelect.appendChild(option);
            });

            municipioSelect.disabled = municipios.length === 0;
        }

        document.querySelectorAll('.dashboard-direccion-form').forEach(function (form) {
            var departamentoSelect = form.querySelector('.dashboard-departamento-select');
            if (!departamentoSelect) {
                return;
            }

            renderMunicipios(form);

            departamentoSelect.addEventListener('change', function () {
                form.dataset.municipioInicial = '';
                renderMunicipios(form);
            });
        });

        @if (session('abrir_modal_direccion') === 'nueva' || ($errors->direccion->any() && session('tab') === 'addresses' && ! session('editar_direccion_id')))
            var modalNueva = document.getElementById('add-address');
            if (modalNueva && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(modalNueva).show();
            }
        @endif

        @if (session('abrir_modal_direccion') === 'editar' && session('editar_direccion_id'))
            var modalEditar = document.getElementById('editar-direccion-{{ session('editar_direccion_id') }}');
            if (modalEditar && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(modalEditar).show();
            }
        @endif
    })();
</script>
@endpush
