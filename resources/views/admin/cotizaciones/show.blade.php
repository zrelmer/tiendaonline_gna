@extends('layouts.appadmin')

@section('title', 'Detalle de cotización')

@php
    use App\Support\EstatusCatalog;

    $usuario = $cotizacion->usuario;
    $lineas = $cotizacion->detalle ?? collect();
    $estatusId = (int) $cotizacion->Id_Estatus;
    $estatusNombre = $cotizacion->estatus?->Nom_Estatus ?? '—';
    $badgeClass = match ($estatusId) {
        EstatusCatalog::COTIZACION_EMITIDA, EstatusCatalog::COTIZACION_ACEPTADA => 'success-bg',
        EstatusCatalog::COTIZACION_RECHAZADA, EstatusCatalog::COTIZACION_VENCIDA => 'danger',
        EstatusCatalog::COTIZACION_EN_REVISION => 'warning-bg',
        default => '',
    };
    $archivoDisponible = $cotizacion->archivoDisponible();
@endphp

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
        <h5>Cotización {{ $cotizacion->Cot_Numero }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.cotizaciones.pendientes.index') }}">Pendientes</a>
                </li>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.cotizaciones.index') }}">Volver al listado</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Resumen</h5>
                    </div>
                    <dl class="mb-0 admin-cotizacion-meta">
                        <dt>ID</dt>
                        <dd>{{ $cotizacion->Id_Cotizacion }}</dd>

                        <dt>Número</dt>
                        <dd>{{ $cotizacion->Cot_Numero }}</dd>

                        <dt>Estatus</dt>
                        <dd><span class="admin-cotizacion-badge {{ $badgeClass }}">{{ $estatusNombre }}</span></dd>

                        <dt>Fecha solicitud</dt>
                        <dd>{{ $cotizacion->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                        @if ($cotizacion->Cot_FechaEmision)
                            <dt>Fecha emisión</dt>
                            <dd>{{ $cotizacion->Cot_FechaEmision->format('d/m/Y H:i') }}</dd>
                        @endif

                        <dt>Vigencia</dt>
                        <dd>{{ (int) $cotizacion->Cot_VigenciaDias }} días</dd>

                        <dt>Total referencia</dt>
                        <dd class="td-price">Q {{ number_format((float) $cotizacion->Cot_Total, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Cliente y contacto</h5>
                    </div>
                    <dl class="mb-0 admin-cotizacion-meta">
                        <dt>Cliente / razón social</dt>
                        <dd>{{ $cotizacion->Cot_NombreCliente }}</dd>

                        <dt>NIT</dt>
                        <dd>{{ $cotizacion->Cot_Nit ?: '—' }}</dd>

                        <dt>Correo</dt>
                        <dd>{{ $cotizacion->Cot_Email ?: '—' }}</dd>

                        <dt>Dirección</dt>
                        <dd>{{ $cotizacion->Cot_Direccion ?: '—' }}</dd>

                        <dt>Usuario registrado</dt>
                        <dd>
                            {{ $usuario?->Usu_Nombre ?? '—' }}
                            @if ($usuario?->Usu_Correo)
                                <span class="text-muted small d-block">{{ $usuario->Usu_Correo }}</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        @if ($cotizacion->Cot_NotasSolicitud)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-2 mb-2">
                            <h5>Notas de la solicitud</h5>
                        </div>
                        <p class="text-muted mb-0">{{ $cotizacion->Cot_NotasSolicitud }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Ítems solicitados</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table theme-table table-cotizacion-detalle">
                            <thead>
                                <tr>
                                    <th>Cant.</th>
                                    <th>Descripción</th>
                                    <th>Producto catálogo</th>
                                    <th>Costo unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lineas as $linea)
                                    <tr>
                                        <td>{{ (int) $linea->Cantidad }}</td>
                                        <td>{{ $linea->Descripcion }}</td>
                                        <td>{{ $linea->producto?->Prod_Nombre ?? '—' }}</td>
                                        <td>Q {{ number_format((float) $linea->Costo_Unit, 2) }}</td>
                                        <td>Q {{ number_format((float) $linea->Subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                    <td class="fw-bold td-price">Q {{ number_format((float) $cotizacion->Cot_Total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="text-muted small mb-0">
                        @if ($estatusId >= EstatusCatalog::COTIZACION_EMITIDA)
                            Montos finales de la cotización emitida.
                        @else
                            Los montos son referencia del catálogo al momento de la solicitud. El total final se define al emitir la cotización.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if ($cotizacion->Cot_Terminos && $estatusId >= EstatusCatalog::COTIZACION_EMITIDA)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-2 mb-2">
                            <h5>Términos y condiciones</h5>
                        </div>
                        <pre class="admin-cotizacion-terminos mb-0">{{ $cotizacion->Cot_Terminos }}</pre>
                    </div>
                </div>
            </div>
        @endif

        @if ($archivoDisponible)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-2 mb-2">
                            <h5>Archivo emitido</h5>
                        </div>
                        <p class="text-muted small mb-3">
                            Documento disponible para el cliente en su panel de cotizaciones.
                        </p>
                        <a href="{{ route('admin.cotizaciones.download', $cotizacion) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="ri-download-2-line me-1"></i>
                            Descargar PDF
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if ($puedeMarcarEnRevision)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-2 mb-3">
                            <h5>Acción disponible</h5>
                        </div>
                        <p class="text-muted small">
                            Marca la solicitud como <strong>En revisión</strong>. El usuario verá el cambio en su panel de cotizaciones.
                        </p>
                        <form method="POST"
                              action="{{ route('admin.cotizaciones.revision', $cotizacion) }}"
                              onsubmit="return confirm('¿Marcar la solicitud {{ $cotizacion->Cot_Numero }} en revisión?');">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="comentario-revision">Comentario para el historial (opcional)</label>
                                <textarea class="form-control"
                                          id="comentario-revision"
                                          name="comentario"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Ej.: Revisando productos y disponibilidad.">{{ old('comentario') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-solid">
                                <i class="ri-search-eye-line me-1"></i>
                                Marcar en revisión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @elseif ($estatusId === EstatusCatalog::COTIZACION_EN_REVISION)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-2 mb-3">
                            <h5>Acción disponible</h5>
                        </div>
                        <p class="text-muted small mb-3">
                            Esta solicitud está <strong>en revisión</strong>. Emite la cotización formal con precios finales, términos y PDF.
                        </p>
                        <a href="{{ route('admin.cotizaciones.emitir', $cotizacion) }}" class="btn btn-solid">
                            <i class="ri-file-upload-line me-1"></i>
                            Emitir cotización
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if ($eventos->isNotEmpty())
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-2 mb-3">
                            <h5>Historial</h5>
                        </div>
                        <ul class="admin-pedido-historial-timeline">
                            @foreach ($eventos as $evento)
                                @php
                                    $esActual = (int) $evento->Id_Estatus === $estatusId
                                        && $loop->last;
                                @endphp
                                <li @class(['admin-pedido-historial-timeline-item', 'is-current' => $esActual])>
                                    <div class="admin-pedido-historial-timeline-marker">
                                        <i class="ri-time-line"></i>
                                    </div>
                                    <div>
                                        <div class="admin-pedido-historial-timeline-title">
                                            {{ $evento->estatus?->Nom_Estatus ?? '—' }}
                                        </div>
                                        @if ($evento->Comentario)
                                            <p class="text-muted small mb-1">{{ $evento->Comentario }}</p>
                                        @endif
                                        <span class="admin-pedido-historial-timeline-date">
                                            {{ $evento->Fecha_Cambio ? \Illuminate\Support\Carbon::parse($evento->Fecha_Cambio)->format('d/m/Y H:i') : '—' }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
