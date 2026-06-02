@extends('layouts.appadmin')

@section('title', 'Cotizaciones pendientes')

@php
    use App\Services\AdminCotizacionService;
@endphp

@inject('cotizacionService', App\Services\AdminCotizacionService::class)

@section('content')
    <div class="title-header option-title d-sm-flex d-block align-items-center justify-content-between gap-2">
        <h5>Cotizaciones pendientes</h5>
        @if ($totalPendientes > 0)
            <span class="badge bg-primary admin-cotizacion-pendientes-total-badge">
                {{ $totalPendientes }} pendiente{{ $totalPendientes === 1 ? '' : 's' }}
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="admin-cotizacion-pendientes-filtros mb-3">
                        <p class="text-muted small mb-2">Filtrar por próxima acción:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $queryBase = request()->only('q');
                                $filtros = [
                                    null => ['etiqueta' => 'Todas', 'conteo' => $totalPendientes],
                                    AdminCotizacionService::ACCION_REVISION => [
                                        'etiqueta' => 'Marcar en revisión',
                                        'conteo' => $conteosAccion[AdminCotizacionService::ACCION_REVISION],
                                    ],
                                    AdminCotizacionService::ACCION_EN_REVISION => [
                                        'etiqueta' => 'En revisión',
                                        'conteo' => $conteosAccion[AdminCotizacionService::ACCION_EN_REVISION],
                                    ],
                                ];
                            @endphp
                            @foreach ($filtros as $clave => $info)
                                @php
                                    $params = $queryBase;
                                    if ($clave !== null) {
                                        $params['accion'] = $clave;
                                    }
                                    $activo = $filtroAccion === $clave || ($clave === null && $filtroAccion === null);
                                @endphp
                                <a href="{{ route('admin.cotizaciones.pendientes.index', $params) }}"
                                   class="btn btn-sm {{ $activo ? 'btn-primary' : 'btn-outline-secondary' }} admin-cotizacion-pendientes-filtro-btn">
                                    {{ $info['etiqueta'] }}
                                    <span class="badge {{ $activo ? 'bg-light text-primary' : 'bg-secondary' }} ms-1">
                                        {{ $info['conteo'] }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.cotizaciones.pendientes.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-cotizaciones-pendientes-search-form"
                                  role="search">
                                @if ($filtroAccion !== null)
                                    <input type="hidden" name="accion" value="{{ $filtroAccion }}">
                                @endif
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-cotizaciones-pendientes-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-cotizaciones-pendientes-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, número, cliente o estatus…"
                                           aria-label="Buscar cotizaciones pendientes"
                                           aria-describedby="admin-cotizaciones-pendientes-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '' || $filtroAccion !== null)
                                        <a href="{{ route('admin.cotizaciones.pendientes.index') }}"
                                           class="btn btn-outline-secondary">
                                            Limpiar
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        @if ($terminoBusqueda !== '')
                            <div class="col-lg-5 col-md-4 text-md-end">
                                <p class="mb-0 text-muted small admin-productos-search-summary">
                                    {{ $cotizaciones->total() }} resultado{{ $cotizaciones->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        Solicitudes activas que requieren atención (recibidas o en revisión).
                        Orden: primero en entrar, primero en salir.
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-cotizaciones-pendientes-table-wrap">
                        <table class="table theme-table table-cotizacion-pendientes">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total ref.</th>
                                    <th>Estatus</th>
                                    <th>Próxima acción</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cotizaciones as $cotizacion)
                                    @php
                                        $accion = $cotizacion->accion_cotizacion;
                                        $etiquetaAccion = $cotizacionService->etiquetaAccion($accion);
                                        $estatusNombre = $cotizacion->estatus?->Nom_Estatus ?? '—';
                                    @endphp
                                    <tr>
                                        <td>{{ $cotizacion->Id_Cotizacion }}</td>
                                        <td>{{ $cotizacion->Cot_Numero }}</td>
                                        <td>{{ $cotizacion->Cot_NombreCliente }}</td>
                                        <td>
                                            {{ $cotizacion->usuario?->Usu_Nombre ?? '—' }}
                                            @if ($cotizacion->usuario?->Usu_Correo)
                                                <span class="text-muted small d-block">{{ $cotizacion->usuario->Usu_Correo }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $cotizacion->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="td-price">Q {{ number_format((float) $cotizacion->Cot_Total, 2) }}</td>
                                        <td>
                                            <span class="admin-cotizacion-badge">{{ $estatusNombre }}</span>
                                        </td>
                                        <td>
                                            <span class="admin-cotizacion-accion-label">{{ $etiquetaAccion }}</span>
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    @if ($accion === AdminCotizacionService::ACCION_EN_REVISION)
                                                        <a href="{{ route('admin.cotizaciones.emitir', $cotizacion) }}"
                                                           title="Emitir cotización">
                                                            <i class="ri-file-upload-line"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}"
                                                           title="Revisar y marcar en revisión">
                                                            <i class="ri-play-list-add-line"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}"
                                                       title="Ver detalle">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '' || $filtroAccion !== null)
                                                No hay cotizaciones que coincidan con los filtros aplicados.
                                            @else
                                                No hay cotizaciones pendientes de atención.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($cotizaciones->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $cotizaciones->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
