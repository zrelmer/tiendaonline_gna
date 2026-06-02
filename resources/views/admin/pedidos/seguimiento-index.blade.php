@extends('layouts.appadmin')

@section('title', 'Seguimiento de pedidos')

@php
    use App\Services\AdminPedidoSeguimientoService;
@endphp

@inject('seguimientoService', App\Services\AdminPedidoSeguimientoService::class)

@section('content')
    <div class="title-header option-title d-sm-flex d-block align-items-center justify-content-between gap-2">
        <h5>Seguimiento de pedidos</h5>
        @if ($totalPendientes > 0)
            <span class="badge bg-primary admin-pedido-seguimiento-total-badge">
                {{ $totalPendientes }} pendiente{{ $totalPendientes === 1 ? '' : 's' }}
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="admin-pedido-seguimiento-filtros mb-3">
                        <p class="text-muted small mb-2">Filtrar por próxima acción:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $queryBase = request()->only('q');
                                $filtros = [
                                    null => ['etiqueta' => 'Todos', 'conteo' => $totalPendientes],
                                    AdminPedidoSeguimientoService::ACCION_CONFIRMAR => [
                                        'etiqueta' => 'Confirmar',
                                        'conteo' => $conteosAccion[AdminPedidoSeguimientoService::ACCION_CONFIRMAR],
                                    ],
                                    AdminPedidoSeguimientoService::ACCION_PREPARACION => [
                                        'etiqueta' => 'En preparación',
                                        'conteo' => $conteosAccion[AdminPedidoSeguimientoService::ACCION_PREPARACION],
                                    ],
                                    AdminPedidoSeguimientoService::ACCION_ENVIADO => [
                                        'etiqueta' => 'Enviar',
                                        'conteo' => $conteosAccion[AdminPedidoSeguimientoService::ACCION_ENVIADO],
                                    ],
                                    AdminPedidoSeguimientoService::ACCION_ENTREGADO => [
                                        'etiqueta' => 'Entregar',
                                        'conteo' => $conteosAccion[AdminPedidoSeguimientoService::ACCION_ENTREGADO],
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
                                <a href="{{ route('admin.pedidos.seguimiento.index', $params) }}"
                                   class="btn btn-sm {{ $activo ? 'btn-primary' : 'btn-outline-secondary' }} admin-pedido-seguimiento-filtro-btn">
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
                            <form action="{{ route('admin.pedidos.seguimiento.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-pedidos-seguimiento-search-form"
                                  role="search">
                                @if ($filtroAccion !== null)
                                    <input type="hidden" name="accion" value="{{ $filtroAccion }}">
                                @endif
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-pedidos-seguimiento-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-pedidos-seguimiento-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, número, usuario o estatus…"
                                           aria-label="Buscar pedidos pendientes de seguimiento"
                                           aria-describedby="admin-pedidos-seguimiento-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '' || $filtroAccion !== null)
                                        <a href="{{ route('admin.pedidos.seguimiento.index') }}"
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
                                    {{ $pedidos->total() }} resultado{{ $pedidos->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        Pedidos activos que requieren una acción de seguimiento (pendiente → entregado).
                        Orden: primero en entrar, primero en salir.
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-pedidos-seguimiento-table-wrap">
                        <table class="table theme-table table-pedido-seguimiento">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estatus pedido</th>
                                    <th>Estatus pago</th>
                                    <th>Próxima acción</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pedidos as $pedido)
                                    @php
                                        $estatusId = (int) $pedido->Id_Estatus;
                                        $estatusNombre = $pedido->estatus?->Nom_Estatus ?? '—';
                                        $pagoEstatus = $pedido->pago?->estatus?->Nom_Estatus ?? '—';
                                        $accion = $pedido->accion_seguimiento;
                                        $bloqueo = $pedido->bloqueo_seguimiento;
                                        $etiquetaAccion = $seguimientoService->etiquetaAccion($accion);
                                    @endphp
                                    <tr class="{{ $bloqueo ? 'admin-pedido-seguimiento-row-bloqueado' : '' }}">
                                        <td>{{ $pedido->Id_Pedido }}</td>
                                        <td>{{ $pedido->Ped_Numero }}</td>
                                        <td>
                                            {{ $pedido->usuario?->Usu_Nombre ?? '—' }}
                                            @if ($pedido->usuario?->Usu_Correo)
                                                <span class="text-muted small d-block">{{ $pedido->usuario->Usu_Correo }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $pedido->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="td-price">Q {{ number_format((float) $pedido->Ped_TotalPrecio, 2) }}</td>
                                        <td>
                                            <span class="admin-pedido-badge">{{ $estatusNombre }}</span>
                                        </td>
                                        <td>{{ $pagoEstatus }}</td>
                                        <td>
                                            <span class="admin-pedido-seguimiento-accion-label">{{ $etiquetaAccion }}</span>
                                            @if ($bloqueo)
                                                <span class="text-warning small d-block mt-1" title="{{ $bloqueo }}">
                                                    <i class="ri-error-warning-line"></i> {{ $bloqueo }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.pedidos.seguimiento', $pedido) }}"
                                                       title="Gestionar seguimiento">
                                                        <i class="ri-truck-line"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.pedidos.show', $pedido) }}"
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
                                                No hay pedidos pendientes que coincidan con los filtros aplicados.
                                            @else
                                                No hay pedidos pendientes de seguimiento.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($pedidos->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $pedidos->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
