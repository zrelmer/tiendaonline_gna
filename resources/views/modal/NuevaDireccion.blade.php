<div class="modal fade"
     id="modalNuevaDireccion"
     tabindex="-1"
     aria-labelledby="modalNuevaDireccionLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('cart.checkout.direccion.store') }}" id="form-nueva-direccion">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaDireccionLabel">Registrar dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    @if ($errors->direccion->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->direccion->all() as $errorDireccion)
                                    <li>{{ $errorDireccion }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="direccion-input" class="form-label">Dirección</label>
                        <input type="text"
                               class="form-control"
                               id="direccion-input"
                               name="direccion"
                               maxlength="200"
                               value="{{ old('direccion') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="departamento-select" class="form-label">Departamento</label>
                        <select class="form-select"
                                id="departamento-select"
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
                        <label for="municipio-select" class="form-label">Municipio</label>
                        <select class="form-select"
                                id="municipio-select"
                                name="id_municipio"
                                required
                                disabled>
                            <option value="">Selecciona un municipio</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-md btn-outline" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn theme-bg-color text-white btn-md">Guardar dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var municipiosPorDepartamento = @json($municipiosPorDepartamento);
        var departamentoSelect = document.getElementById('departamento-select');
        var municipioSelect = document.getElementById('municipio-select');
        var oldMunicipio = @json(old('id_municipio'));

        if (!departamentoSelect || !municipioSelect) {
            return;
        }

        function renderMunicipios() {
            var departamentoId = departamentoSelect.value;
            var municipios = municipiosPorDepartamento[departamentoId] || [];
            municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';

            municipios.forEach(function (municipio) {
                var option = document.createElement('option');
                option.value = municipio.id;
                option.textContent = municipio.nombre;

                if (String(oldMunicipio) === String(municipio.id)) {
                    option.selected = true;
                }

                municipioSelect.appendChild(option);
            });

            municipioSelect.disabled = municipios.length === 0;
        }

        departamentoSelect.addEventListener('change', function () {
            oldMunicipio = null;
            renderMunicipios();
        });

        renderMunicipios();
    })();
</script>

@if (session('abrir_modal_direccion') || $errors->direccion->any())
<script>
    (function () {
        var modalElement = document.getElementById('modalNuevaDireccion');
        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }

        var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    })();
</script>
@endif

@if (session('pedido_creado'))
<script>
    try {
        localStorage.removeItem('carrito');
    } catch (e) {}

    (function () {
        var metodoId = @json(session('abrir_metodo_pago'));
        if (!metodoId) return;

        var radio = document.getElementById('metodo-' + metodoId);
        if (radio) {
            radio.checked = true;
        }

        var collapse = document.getElementById('flush-collapse-' + metodoId);
        if (collapse) {
            collapse.classList.add('show');
        }

        var heading = document.getElementById('flush-heading-' + metodoId);
        if (heading) {
            var btn = heading.querySelector('.accordion-button');
            if (btn) {
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
            }
        }
    })();
</script>
@endif
@endpush

