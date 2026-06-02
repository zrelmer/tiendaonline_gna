@extends('layouts.appadmin')

@section('title', 'Emitir cotización')

@php
    $lineas = $cotizacion->detalle ?? collect();
@endphp

@section('content')
    @if ($errors->cotizacion->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->cotizacion->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Emitir cotización · {{ $cotizacion->Cot_Numero }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.cotizaciones.show', $cotizacion) }}">Ver detalle</a>
                </li>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.cotizaciones.pendientes.index', ['accion' => 'en_revision']) }}">Pendientes</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Solicitud</h5>
                    </div>
                    <dl class="mb-0 admin-cotizacion-meta">
                        <dt>Cliente</dt>
                        <dd>{{ $cotizacion->Cot_NombreCliente }}</dd>

                        <dt>Usuario</dt>
                        <dd>
                            {{ $cotizacion->usuario?->Usu_Nombre ?? '—' }}
                            @if ($cotizacion->usuario?->Usu_Correo)
                                <span class="text-muted small d-block">{{ $cotizacion->usuario->Usu_Correo }}</span>
                            @endif
                        </dd>

                        <dt>Fecha solicitud</dt>
                        <dd>{{ $cotizacion->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </dl>
                    @if ($cotizacion->Cot_NotasSolicitud)
                        <hr>
                        <p class="text-muted small mb-1 fw-semibold">Notas del cliente</p>
                        <p class="text-muted small mb-0">{{ $cotizacion->Cot_NotasSolicitud }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Datos de emisión</h5>
                    </div>
                    <p class="text-muted small">
                        Define los precios finales, términos y sube el PDF. Al confirmar, el estatus pasará a
                        <strong>Cotización emitida</strong> y el cliente podrá descargar el documento.
                    </p>

                    <form method="POST"
                          action="{{ route('admin.cotizaciones.emitir.store', $cotizacion) }}"
                          enctype="multipart/form-data"
                          id="form-emitir-cotizacion"
                          onsubmit="return confirm('¿Emitir la cotización {{ $cotizacion->Cot_Numero }}? El cliente verá los montos finales y el PDF.');">
                        @csrf

                        <div class="table-responsive mb-3">
                            <table class="table theme-table table-cotizacion-detalle admin-cotizacion-emitir-lineas">
                                <thead>
                                    <tr>
                                        <th>Cant.</th>
                                        <th>Descripción</th>
                                        <th>Producto</th>
                                        <th style="min-width: 8rem;">Costo unit. (Q)</th>
                                        <th>Subtotal (Q)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lineas as $linea)
                                        @php
                                            $idDetalle = $linea->Id_CotizacionDetalle;
                                            $costoOld = old('lineas.'.$idDetalle.'.costo_unit', $linea->Costo_Unit);
                                            $cantidad = (int) $linea->Cantidad;
                                        @endphp
                                        <tr data-cantidad="{{ $cantidad }}">
                                            <td>{{ $cantidad }}</td>
                                            <td>{{ $linea->Descripcion }}</td>
                                            <td>{{ $linea->producto?->Prod_Nombre ?? '—' }}</td>
                                            <td>
                                                <input type="number"
                                                       class="form-control form-control-sm admin-cotizacion-costo-unit"
                                                       name="lineas[{{ $idDetalle }}][costo_unit]"
                                                       value="{{ $costoOld }}"
                                                       min="0"
                                                       step="0.01"
                                                       required>
                                            </td>
                                            <td class="admin-cotizacion-subtotal-linea td-price">
                                                Q {{ number_format((float) $costoOld * $cantidad, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total cotización</td>
                                        <td class="fw-bold td-price" id="admin-cotizacion-total-emitir">Q 0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="vigencia_dias">Vigencia (días)</label>
                                <input type="number"
                                       class="form-control"
                                       id="vigencia_dias"
                                       name="vigencia_dias"
                                       value="{{ old('vigencia_dias', $vigenciaDefault) }}"
                                       min="1"
                                       max="365"
                                       required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="archivo">Documento PDF</label>
                                <input type="file"
                                       class="form-control"
                                       id="archivo"
                                       name="archivo"
                                       accept="application/pdf,.pdf"
                                       required>
                                <div class="form-text">PDF formal de la cotización (máx. 10 MB).</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="terminos">Términos y condiciones</label>
                                <textarea class="form-control admin-cotizacion-terminos-input"
                                          id="terminos"
                                          name="terminos"
                                          rows="6"
                                          maxlength="5000"
                                          required>{{ old('terminos', $terminosDefault) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="comentario">Comentario para el historial (opcional)</label>
                                <textarea class="form-control"
                                          id="comentario"
                                          name="comentario"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Ej.: Cotización enviada con precios vigentes al día de hoy.">{{ old('comentario') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-solid">
                                <i class="ri-file-upload-line me-1"></i>
                                Emitir cotización
                            </button>
                            <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('form-emitir-cotizacion');
            if (!form) {
                return;
            }

            const totalEl = document.getElementById('admin-cotizacion-total-emitir');

            function recalcularTotal() {
                let total = 0;
                form.querySelectorAll('tbody tr[data-cantidad]').forEach(function (row) {
                    const cantidad = parseInt(row.getAttribute('data-cantidad'), 10) || 0;
                    const input = row.querySelector('.admin-cotizacion-costo-unit');
                    const subtotalCell = row.querySelector('.admin-cotizacion-subtotal-linea');
                    const costo = parseFloat(input?.value) || 0;
                    const subtotal = Math.round(costo * cantidad * 100) / 100;
                    total += subtotal;
                    if (subtotalCell) {
                        subtotalCell.textContent = 'Q ' + subtotal.toFixed(2);
                    }
                });
                if (totalEl) {
                    totalEl.textContent = 'Q ' + total.toFixed(2);
                }
            }

            form.addEventListener('input', function (event) {
                if (event.target.classList.contains('admin-cotizacion-costo-unit')) {
                    recalcularTotal();
                }
            });

            recalcularTotal();
        })();
    </script>
@endpush
