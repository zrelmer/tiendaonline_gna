@php
    use App\Support\EstatusCatalog;

    $textoDireccionPrincipal = $direccionPrincipal
        ? collect([
            $direccionPrincipal->Direccion,
            $direccionPrincipal->municipio?->Nom_Municipio,
            $direccionPrincipal->municipio?->departamento?->Nom_Departamento,
        ])->filter()->implode(', ')
        : '';
@endphp

<div class="dashboard-quotes">
    <div class="title title-flex">
        <div>
            <h2>Cotizaciones</h2>
            <span class="title-leaf title-leaf-gray">
                <svg class="icon-width bg-gray">
                    <use xlink:href="{{ asset('assets/svg/leaf.svg') }}#leaf"></use>
                </svg>
            </span>
        </div>
        <button type="button"
                class="btn theme-bg-color text-white btn-sm fw-bold mt-lg-0 mt-3"
                data-bs-toggle="collapse"
                data-bs-target="#form-solicitud-cotizacion"
                aria-expanded="{{ session('abrir_formulario_cotizacion') ? 'true' : 'false' }}">
            <i data-feather="plus" class="me-2"></i> Nueva solicitud
        </button>
    </div>

    <p class="text-content mb-4">
        Solicita una cotización formal. Los precios de referencia salen del catálogo; el equipo confirmará el total final
        según tu plantilla (como la cotización en Excel).
    </p>

    @if ($errors->cotizacion->any() && session('tab') === 'quotes')
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->cotizacion->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="collapse {{ session('abrir_formulario_cotizacion') ? 'show' : '' }}" id="form-solicitud-cotizacion">
        <div class="dashboard-bg-box dashboard-quote-form-box mb-4">
            <h3 class="mb-3">Nueva solicitud de cotización</h3>

            <form method="POST"
                  action="{{ route('dashboard.cotizaciones.store') }}"
                  id="form-cotizacion-solicitud">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="nombre_cliente">Nombre o razón social *</label>
                        <input type="text"
                               class="form-control"
                               id="nombre_cliente"
                               name="nombre_cliente"
                               maxlength="200"
                               value="{{ old('nombre_cliente', $usuario->Usu_Nombre) }}"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="nit">NIT</label>
                        <input type="text"
                               class="form-control"
                               id="nit"
                               name="nit"
                               maxlength="50"
                               value="{{ old('nit') }}"
                               placeholder="Opcional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Correo de contacto</label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               maxlength="150"
                               value="{{ old('email', $usuario->Usu_Correo) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="direccion">Dirección</label>
                        <input type="text"
                               class="form-control"
                               id="direccion"
                               name="direccion"
                               maxlength="300"
                               value="{{ old('direccion', $textoDireccionPrincipal) }}"
                               placeholder="Dirección fiscal o de entrega">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="notas">Notas adicionales</label>
                        <textarea class="form-control"
                                  id="notas"
                                  name="notas"
                                  rows="2"
                                  maxlength="2000"
                                  placeholder="Urgencia, uso del producto, observaciones...">{{ old('notas') }}</textarea>
                    </div>
                </div>

                <div class="dashboard-quote-lines mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Productos o descripciones *</h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-agregar-linea-cotizacion">
                            <i data-feather="plus"></i> Agregar línea
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tabla-lineas-cotizacion">
                            <thead>
                                <tr>
                                    <th style="min-width:180px;">Producto del catálogo</th>
                                    <th>Descripción</th>
                                    <th style="width:100px;">Cant.</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="cotizacion-lineas-body">
                                @php
                                    $oldItems = old('items', [['id_producto' => '', 'descripcion' => '', 'cantidad' => 1]]);
                                    if ($oldItems === []) {
                                        $oldItems = [['id_producto' => '', 'descripcion' => '', 'cantidad' => 1]];
                                    }
                                @endphp
                                @foreach ($oldItems as $index => $item)
                                    <tr class="cotizacion-linea-row">
                                        <td>
                                            <select class="form-select form-select-sm cotizacion-producto-select"
                                                    name="items[{{ $index }}][id_producto]">
                                                <option value="">— Texto libre —</option>
                                                @foreach ($productosCotizacion as $producto)
                                                    <option value="{{ $producto->Id_Producto }}"
                                                        data-nombre="{{ $producto->Prod_Nombre }}"
                                                        data-precio="{{ $producto->Prod_Precio }}"
                                                        @selected((string) ($item['id_producto'] ?? '') === (string) $producto->Id_Producto)>
                                                        {{ $producto->Prod_Nombre }} (Q {{ number_format($producto->Prod_Precio, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                   class="form-control form-control-sm cotizacion-descripcion-input"
                                                   name="items[{{ $index }}][descripcion]"
                                                   maxlength="500"
                                                   value="{{ $item['descripcion'] ?? '' }}"
                                                   placeholder="Descripción del ítem">
                                        </td>
                                        <td>
                                            <input type="number"
                                                   class="form-control form-control-sm"
                                                   name="items[{{ $index }}][cantidad]"
                                                   min="1"
                                                   max="9999"
                                                   value="{{ $item['cantidad'] ?? 1 }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-link text-danger btn-quitar-linea-cotizacion"
                                                    title="Quitar línea">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-content small mb-0">
                        Si eliges un producto del catálogo, la descripción se completa automáticamente.
                        Los montos mostrados son referencia; el total final lo confirma el administrador.
                    </p>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn theme-bg-color text-white btn-sm fw-bold">
                        Enviar solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="dashboard-quote-list">
        <h3 class="mb-3">Mis solicitudes y cotizaciones</h3>

        @forelse ($cotizaciones as $cotizacion)
            @php
                $estatusId = (int) $cotizacion->Id_Estatus;
                $badgeClass = match ($estatusId) {
                    EstatusCatalog::COTIZACION_EMITIDA, EstatusCatalog::COTIZACION_ACEPTADA => 'success-bg',
                    EstatusCatalog::COTIZACION_RECHAZADA, EstatusCatalog::COTIZACION_VENCIDA => 'danger',
                    default => '',
                };
                $puedeDescargar = $cotizacion->puedeDescargarArchivo();
                $puedeResponder = $cotizacion->puedeResponderCliente();
                $fechaVencimiento = $cotizacion->fechaVencimiento();
                $historial = ($cotizacion->historial ?? collect())->sortBy('Fecha_Cambio');
            @endphp

            <div class="dashboard-bg-box dashboard-quote-card mb-4">
                <div class="dashboard-quote-card-header">
                    <div>
                        <h4 class="mb-1">{{ $cotizacion->Cot_Numero }}</h4>
                        <p class="text-content small mb-0">
                            Solicitud: {{ $cotizacion->created_at?->format('d/m/Y H:i') ?? '—' }}
                            @if ($cotizacion->Cot_FechaEmision)
                                · Emitida: {{ $cotizacion->Cot_FechaEmision->format('d/m/Y H:i') }}
                            @endif
                            @if ($fechaVencimiento && in_array($estatusId, [EstatusCatalog::COTIZACION_EMITIDA, EstatusCatalog::COTIZACION_VENCIDA], true))
                                · Vence: {{ $fechaVencimiento->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                    <span @class(['dashboard-quote-badge', $badgeClass])>
                        {{ $cotizacion->estatus?->Nom_Estatus ?? 'Sin estatus' }}
                    </span>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <p class="small mb-1"><strong>Cliente:</strong> {{ $cotizacion->Cot_NombreCliente }}</p>
                        @if ($cotizacion->Cot_Nit)
                            <p class="small mb-1"><strong>NIT:</strong> {{ $cotizacion->Cot_Nit }}</p>
                        @endif
                        @if ($cotizacion->Cot_Direccion)
                            <p class="small mb-1"><strong>Dirección:</strong> {{ $cotizacion->Cot_Direccion }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="small mb-1">
                            <strong>Total referencia:</strong>
                            Q {{ number_format((float) $cotizacion->Cot_Total, 2) }}
                        </p>
                        @if ($estatusId === EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA)
                            <p class="text-content small mb-0">En espera de revisión por el equipo.</p>
                        @endif
                        @if ($estatusId === EstatusCatalog::COTIZACION_EMITIDA && $fechaVencimiento)
                            <p class="text-content small mb-2">
                                Tienes hasta el <strong>{{ $fechaVencimiento->format('d/m/Y') }}</strong>
                                para aceptar o rechazar esta cotización.
                            </p>
                        @endif
                        @if ($estatusId === EstatusCatalog::COTIZACION_VENCIDA)
                            <p class="text-content small mb-2 text-danger">
                                El plazo de vigencia expiró. Puedes solicitar una nueva cotización si lo necesitas.
                            </p>
                        @endif
                        @if ($puedeDescargar)
                            <a href="{{ route('dashboard.cotizaciones.download', $cotizacion) }}"
                               class="btn btn-sm theme-bg-color text-white mt-2 me-2">
                                <i data-feather="download"></i> Descargar cotización
                            </a>
                        @endif
                        @if ($puedeResponder)
                            <form method="POST"
                                  action="{{ route('dashboard.cotizaciones.aceptar', $cotizacion) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('¿Confirmas que aceptas esta cotización?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success mt-2 me-2">
                                    <i data-feather="check"></i> Aceptar
                                </button>
                            </form>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger mt-2"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#rechazar-cotizacion-{{ $cotizacion->Id_Cotizacion }}"
                                    aria-expanded="false">
                                <i data-feather="x"></i> Rechazar
                            </button>
                        @endif
                    </div>
                </div>

                @if ($puedeResponder)
                    <div class="collapse mt-3" id="rechazar-cotizacion-{{ $cotizacion->Id_Cotizacion }}">
                        <div class="dashboard-bg-box border border-danger-subtle">
                            <h6 class="mb-2">Rechazar cotización</h6>
                            <form method="POST"
                                  action="{{ route('dashboard.cotizaciones.rechazar', $cotizacion) }}"
                                  onsubmit="return confirm('¿Seguro que deseas rechazar esta cotización?');">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="rechazo-comentario-{{ $cotizacion->Id_Cotizacion }}">
                                        Motivo del rechazo *
                                    </label>
                                    <textarea class="form-control form-control-sm"
                                              id="rechazo-comentario-{{ $cotizacion->Id_Cotizacion }}"
                                              name="comentario"
                                              rows="2"
                                              maxlength="500"
                                              minlength="10"
                                              required
                                              placeholder="Indica por qué no procedes con esta cotización…"></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-danger">Confirmar rechazo</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($cotizacion->Cot_NotasSolicitud)
                    <p class="small text-content mb-3">
                        <strong>Notas:</strong> {{ $cotizacion->Cot_NotasSolicitud }}
                    </p>
                @endif

                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Cant.</th>
                                <th>Descripción</th>
                                <th>Costo unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cotizacion->detalle as $linea)
                                <tr>
                                    <td>{{ (int) $linea->Cantidad }}</td>
                                    <td>{{ $linea->Descripcion }}</td>
                                    <td>Q {{ number_format((float) $linea->Costo_Unit, 2) }}</td>
                                    <td>Q {{ number_format((float) $linea->Subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="fw-bold">Q {{ number_format((float) $cotizacion->Cot_Total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($cotizacion->Cot_Terminos && $estatusId >= EstatusCatalog::COTIZACION_EMITIDA)
                    <div class="dashboard-quote-terms small mb-3">
                        <strong>Términos y condiciones</strong>
                        <pre class="mb-0 mt-1 text-content" style="white-space: pre-wrap; font-family: inherit;">{{ $cotizacion->Cot_Terminos }}</pre>
                    </div>
                @endif

                @if ($historial->isNotEmpty())
                    <div class="dashboard-quote-history">
                        <h6 class="mb-2">Seguimiento</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach ($historial as $evento)
                                <li class="small text-content mb-1">
                                    <strong>{{ $evento->estatus?->Nom_Estatus }}</strong>
                                    — {{ $evento->Comentario }}
                                    <span class="text-muted">
                                        ({{ $evento->Fecha_Cambio ? \Illuminate\Support\Carbon::parse($evento->Fecha_Cambio)->format('d/m/Y H:i') : '' }})
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @empty
            <div class="dashboard-bg-box p-4 text-center">
                <p class="text-content mb-3">Aún no has solicitado cotizaciones.</p>
                <button type="button"
                        class="btn theme-bg-color text-white btn-sm"
                        data-bs-toggle="collapse"
                        data-bs-target="#form-solicitud-cotizacion">
                    Crear primera solicitud
                </button>
            </div>
        @endforelse
    </div>
</div>

<template id="tpl-linea-cotizacion">
    <tr class="cotizacion-linea-row">
        <td>
            <select class="form-select form-select-sm cotizacion-producto-select" name="items[__INDEX__][id_producto]">
                <option value="">— Texto libre —</option>
                @foreach ($productosCotizacion as $producto)
                    <option value="{{ $producto->Id_Producto }}"
                            data-nombre="{{ $producto->Prod_Nombre }}"
                            data-precio="{{ $producto->Prod_Precio }}">
                        {{ $producto->Prod_Nombre }} (Q {{ number_format($producto->Prod_Precio, 2) }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text"
                   class="form-control form-control-sm cotizacion-descripcion-input"
                   name="items[__INDEX__][descripcion]"
                   maxlength="500"
                   placeholder="Descripción del ítem">
        </td>
        <td>
            <input type="number"
                   class="form-control form-control-sm"
                   name="items[__INDEX__][cantidad]"
                   min="1"
                   max="9999"
                   value="1">
        </td>
        <td class="text-center">
            <button type="button"
                    class="btn btn-sm btn-link text-danger btn-quitar-linea-cotizacion"
                    title="Quitar línea">
                <i data-feather="trash-2"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    (function () {
        var lineIndex = document.querySelectorAll('#cotizacion-lineas-body .cotizacion-linea-row').length;
        var tbody = document.getElementById('cotizacion-lineas-body');
        var tpl = document.getElementById('tpl-linea-cotizacion');

        function reindexLineas() {
            tbody.querySelectorAll('.cotizacion-linea-row').forEach(function (row, idx) {
                row.querySelectorAll('[name^="items["]').forEach(function (input) {
                    input.name = input.name.replace(/items\[\d+\]/, 'items[' + idx + ']');
                });
            });
            lineIndex = tbody.querySelectorAll('.cotizacion-linea-row').length;
        }

        function bindProductoSelect(row) {
            var select = row.querySelector('.cotizacion-producto-select');
            var desc = row.querySelector('.cotizacion-descripcion-input');
            if (!select || !desc) return;

            select.addEventListener('change', function () {
                var opt = select.options[select.selectedIndex];
                if (opt.value && opt.dataset.nombre) {
                    desc.value = opt.dataset.nombre;
                    desc.readOnly = true;
                } else {
                    desc.readOnly = false;
                }
            });

            if (select.value) {
                desc.readOnly = true;
            }
        }

        tbody.querySelectorAll('.cotizacion-linea-row').forEach(bindProductoSelect);

        document.getElementById('btn-agregar-linea-cotizacion')?.addEventListener('click', function () {
            if (!tpl || !tbody) return;
            var html = tpl.innerHTML.replace(/__INDEX__/g, String(lineIndex));
            var temp = document.createElement('tbody');
            temp.innerHTML = html.trim();
            var row = temp.firstElementChild;
            tbody.appendChild(row);
            lineIndex++;
            bindProductoSelect(row);
            if (window.feather) window.feather.replace();
        });

        tbody.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-quitar-linea-cotizacion');
            if (!btn) return;
            var rows = tbody.querySelectorAll('.cotizacion-linea-row');
            if (rows.length <= 1) return;
            btn.closest('tr')?.remove();
            reindexLineas();
        });

        var quotesTab = document.getElementById('pills-quotes-tab');
        if (quotesTab && window.feather) {
            quotesTab.addEventListener('shown.bs.tab', function () {
                window.feather.replace();
            });
        }
    })();
</script>
@endpush
