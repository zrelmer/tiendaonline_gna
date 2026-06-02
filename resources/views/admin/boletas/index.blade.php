@extends('layouts.appadmin')

@section('title', 'Boletas de pago')

@php
    use App\Support\EstatusCatalog;
@endphp

@section('content')
    <div class="title-header option-title d-sm-flex d-block align-items-center justify-content-between gap-2">
        <h5>Boletas de pago</h5>
        @if ($conteoPendientes > 0)
            <span class="badge bg-warning text-dark admin-boleta-pendientes-badge">
                {{ $conteoPendientes }} pendiente{{ $conteoPendientes === 1 ? '' : 's' }} de verificación
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="admin-boleta-filtros mb-3">
                        <p class="text-muted small mb-2">Filtrar por estado:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $queryBase = request()->only('q');
                                $filtros = [
                                    '' => ['etiqueta' => 'Todas', 'conteo' => null],
                                    'pendiente' => ['etiqueta' => 'Pendientes de verificación', 'conteo' => $conteoPendientes],
                                    'procesada' => ['etiqueta' => 'Procesadas', 'conteo' => null],
                                ];
                            @endphp
                            @foreach ($filtros as $clave => $info)
                                @php
                                    $params = $queryBase;
                                    if ($clave !== '') {
                                        $params['estado'] = $clave;
                                    }
                                    $activo = $filtroEstado === $clave;
                                @endphp
                                <a href="{{ route('admin.boletas.index', $params) }}"
                                   class="btn btn-sm {{ $activo ? 'btn-primary' : 'btn-outline-secondary' }} admin-boleta-filtro-btn">
                                    {{ $info['etiqueta'] }}
                                    @if ($info['conteo'] !== null)
                                        <span class="badge {{ $activo ? 'bg-light text-primary' : 'bg-secondary' }} ms-1">
                                            {{ $info['conteo'] }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.boletas.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-boletas-search-form"
                                  role="search">
                                @if ($filtroEstado !== '')
                                    <input type="hidden" name="estado" value="{{ $filtroEstado }}">
                                @endif
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-boletas-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-boletas-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID boleta, ID pedido, número o usuario…"
                                           aria-label="Buscar boletas de pago"
                                           aria-describedby="admin-boletas-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '' || $filtroEstado !== '')
                                        <a href="{{ route('admin.boletas.index') }}"
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
                                    {{ $boletas->total() }} resultado{{ $boletas->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        Comprobantes de transferencia bancaria. Al aprobar una boleta pendiente también se confirma el pedido (pago Pagado + estado Confirmado).
                        @if ($filtroEstado === 'pendiente')
                            Orden: primero en entrar, primero en salir.
                        @endif
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-boletas-table-wrap">
                        <table class="table theme-table table-boleta">
                            <thead>
                                <tr>
                                    <th>ID boleta</th>
                                    <th>Pedido</th>
                                    <th>Usuario</th>
                                    <th>Fecha carga</th>
                                    <th>Estatus pago</th>
                                    <th>Estatus pedido</th>
                                    <th>Verificación</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($boletas as $boleta)
                                    @php
                                        $pagoEstatus = $boleta->pedido?->pago?->estatus?->Nom_Estatus ?? '—';
                                        $pedidoEstatus = $boleta->pedido?->estatus?->Nom_Estatus ?? '—';
                                        $pagoEstatusId = (int) ($boleta->pedido?->pago?->Id_Estatus ?? 0);
                                        $pedidoEstatusId = (int) ($boleta->pedido?->Id_Estatus ?? 0);
                                    @endphp
                                    <tr @class(['admin-boleta-row-pendiente' => $boleta->puede_aprobar])>
                                        <td>{{ $boleta->Id_Boletapago }}</td>
                                        <td>
                                            #{{ $boleta->pedido?->Ped_Numero ?? $boleta->Id_Pedido }}
                                            <span class="text-muted small d-block">ID {{ $boleta->Id_Pedido }}</span>
                                        </td>
                                        <td>{{ $boleta->pedido?->usuario?->Usu_Nombre ?? '—' }}</td>
                                        <td>{{ $boleta->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td>{{ $pagoEstatus }}</td>
                                        <td>
                                            <span @class([
                                                'admin-pedido-badge',
                                                'success-bg' => $pedidoEstatusId === EstatusCatalog::PEDIDO_ENTREGADO,
                                                'danger' => $pedidoEstatusId === EstatusCatalog::PEDIDO_CANCELADO,
                                            ])>{{ $pedidoEstatus }}</span>
                                        </td>
                                        <td>
                                            @if ($boleta->puede_aprobar)
                                                <span class="admin-boleta-verificacion-badge pendiente">
                                                    Pendiente
                                                </span>
                                            @elseif ($pagoEstatusId === EstatusCatalog::PAGO_PAGADO)
                                                <span class="admin-boleta-verificacion-badge aprobada">
                                                    Aprobada
                                                </span>
                                            @elseif ($pagoEstatusId === EstatusCatalog::PAGO_RECHAZADO)
                                                <span class="admin-boleta-verificacion-badge rechazada">
                                                    Rechazada
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.boletas.show', $boleta) }}"
                                                       title="{{ $boleta->puede_aprobar ? 'Revisar y aprobar' : 'Ver detalle' }}">
                                                        <i class="{{ $boleta->puede_aprobar ? 'ri-checkbox-circle-line' : 'ri-eye-line' }}"></i>
                                                    </a>
                                                </li>
                                                @if ($boleta->pedido && $pedidoEstatusId !== EstatusCatalog::PEDIDO_CANCELADO)
                                                    <li>
                                                        <a href="{{ route('admin.pedidos.seguimiento', $boleta->pedido) }}"
                                                           title="Gestionar seguimiento">
                                                            <i class="ri-truck-line"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '' || $filtroEstado !== '')
                                                No hay boletas que coincidan con los filtros aplicados.
                                            @else
                                                No hay boletas de pago registradas.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($boletas->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $boletas->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
