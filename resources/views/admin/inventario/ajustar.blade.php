@extends('layouts.appadmin')

@section('title', 'Ajustar stock')

@php
    use App\Services\AdminInventarioService;
@endphp

@section('content')
    @if ($errors->inventario->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->inventario->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Ajustar stock · {{ $producto->Prod_Nombre }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.inventario.index') }}">Volver al inventario</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Stock actual</h5>
                    </div>
                    @if ($sinRegistro)
                        <div class="alert alert-warning py-2 px-3 small mb-3">
                            Este producto aún no tenía registro de inventario. Se creará al confirmar el ajuste.
                        </div>
                    @endif
                    <dl class="mb-0 admin-inventario-meta">
                        <dt>Producto ID</dt>
                        <dd>{{ $producto->Id_Producto }}</dd>

                        <dt>Categoría</dt>
                        <dd>{{ $producto->categoria?->Cate_Nombre ?? '—' }}</dd>

                        <dt>Stock</dt>
                        <dd>{{ number_format($stock) }}</dd>

                        <dt>Reservado</dt>
                        <dd>{{ number_format($reservado) }}</dd>

                        <dt>Disponible</dt>
                        <dd class="fw-semibold">{{ number_format($disponible) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Nuevo ajuste</h5>
                    </div>
                    <p class="text-muted small">
                        Los ajustes quedan registrados en el historial como <strong>Ajuste manual</strong>.
                    </p>

                    <form method="POST"
                          action="{{ route('admin.inventario.ajustar.store', $producto) }}"
                          id="form-ajustar-inventario"
                          onsubmit="return confirm('¿Aplicar este ajuste de stock?');">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label d-block">Tipo de ajuste</label>
                            @foreach ([
                                AdminInventarioService::TIPO_ENTRADA => 'Entrada — sumar unidades al stock',
                                AdminInventarioService::TIPO_SALIDA => 'Salida — restar unidades disponibles',
                                AdminInventarioService::TIPO_FIJAR => 'Fijar stock — establecer el total exacto',
                            ] as $valor => $etiqueta)
                                <div class="form-check">
                                    <input class="form-check-input admin-inventario-tipo-ajuste"
                                           type="radio"
                                           name="tipo"
                                           id="tipo-{{ $valor }}"
                                           value="{{ $valor }}"
                                           @checked(old('tipo', AdminInventarioService::TIPO_ENTRADA) === $valor)
                                           required>
                                    <label class="form-check-label" for="tipo-{{ $valor }}">
                                        {{ $etiqueta }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="cantidad" id="admin-inventario-cantidad-label">
                                Cantidad a ingresar
                            </label>
                            <input type="number"
                                   class="form-control admin-inventario-cantidad-input"
                                   id="cantidad"
                                   name="cantidad"
                                   value="{{ old('cantidad', 1) }}"
                                   min="1"
                                   step="1"
                                   required>
                            <div class="form-text" id="admin-inventario-cantidad-ayuda">
                                Se sumará al stock actual.
                            </div>
                        </div>

                        <div class="alert alert-light border small mb-3 admin-inventario-preview">
                            Stock resultante estimado:
                            <strong id="admin-inventario-stock-resultante">{{ number_format($stock) }}</strong>
                            · Disponible:
                            <strong id="admin-inventario-disponible-resultante">{{ number_format($disponible) }}</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="comentario">Motivo (opcional)</label>
                            <textarea class="form-control"
                                      id="comentario"
                                      name="comentario"
                                      rows="2"
                                      maxlength="500"
                                      placeholder="Ej.: Conteo físico, ingreso de mercadería, corrección.">{{ old('comentario') }}</textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-solid">
                                <i class="ri-stack-line me-1"></i>
                                Aplicar ajuste
                            </button>
                            <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary">
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
            const form = document.getElementById('form-ajustar-inventario');
            if (!form) {
                return;
            }

            const stockActual = {{ (int) $stock }};
            const reservado = {{ (int) $reservado }};
            const cantidadInput = form.querySelector('.admin-inventario-cantidad-input');
            const cantidadLabel = document.getElementById('admin-inventario-cantidad-label');
            const cantidadAyuda = document.getElementById('admin-inventario-cantidad-ayuda');
            const stockResultanteEl = document.getElementById('admin-inventario-stock-resultante');
            const disponibleResultanteEl = document.getElementById('admin-inventario-disponible-resultante');

            const textos = {
                entrada: {
                    label: 'Cantidad a ingresar',
                    ayuda: 'Se sumará al stock actual.',
                    min: 1,
                },
                salida: {
                    label: 'Cantidad a retirar',
                    ayuda: 'No puede superar el stock disponible (' + Math.max(0, stockActual - reservado) + ').',
                    min: 1,
                },
                fijar: {
                    label: 'Nuevo stock total',
                    ayuda: 'Debe ser al menos igual al reservado (' + reservado + ').',
                    min: 0,
                },
            };

            function tipoSeleccionado() {
                const checked = form.querySelector('.admin-inventario-tipo-ajuste:checked');
                return checked ? checked.value : 'entrada';
            }

            function calcularResultante(tipo, cantidad) {
                if (tipo === 'entrada') {
                    return stockActual + cantidad;
                }
                if (tipo === 'salida') {
                    return stockActual - cantidad;
                }
                return cantidad;
            }

            function actualizarUi() {
                const tipo = tipoSeleccionado();
                const config = textos[tipo] || textos.entrada;
                const cantidad = parseInt(cantidadInput.value, 10) || 0;
                const resultante = calcularResultante(tipo, cantidad);
                const disponible = Math.max(0, resultante - reservado);

                cantidadLabel.textContent = config.label;
                cantidadAyuda.textContent = config.ayuda;
                cantidadInput.min = String(config.min);

                stockResultanteEl.textContent = resultante.toLocaleString('es-GT');
                disponibleResultanteEl.textContent = disponible.toLocaleString('es-GT');
            }

            form.querySelectorAll('.admin-inventario-tipo-ajuste').forEach(function (radio) {
                radio.addEventListener('change', actualizarUi);
            });

            cantidadInput.addEventListener('input', actualizarUi);
            actualizarUi();
        })();
    </script>
@endpush
