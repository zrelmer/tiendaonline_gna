@extends('layouts.appadmin')

@section('title', 'Listado de pedidos')

@php
    use App\Support\EstatusCatalog;
@endphp

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Listado de pedidos</h5>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.pedidos.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-pedidos-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-pedidos-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-pedidos-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, número, usuario, estatus o método de pago…"
                                           aria-label="Buscar pedidos"
                                           aria-describedby="admin-pedidos-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.pedidos.index') }}"
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
                        Orden: primero en entrar, primero en salir (más antiguos arriba).
                        Los pedidos ocultos solo desaparecen del panel admin; el usuario sigue viéndolos.
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-pedidos-table-wrap">
                        <table class="table theme-table table-pedido">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Método de pago</th>
                                    <th>Estatus pedido</th>
                                    <th>Estatus pago</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pedidos as $pedido)
                                    @php
                                        $estatusId = (int) $pedido->Id_Estatus;
                                        $estatusNombre = $pedido->estatus?->Nom_Estatus ?? '—';
                                        $pagoEstatus = $pedido->pago?->estatus?->Nom_Estatus ?? '—';
                                        $metodoPago = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? '—';
                                        $esCancelado = $estatusId === EstatusCatalog::PEDIDO_CANCELADO;
                                        $badgePedidoClass = match ($estatusId) {
                                            EstatusCatalog::PEDIDO_ENTREGADO => 'admin-pedido-badge success-bg',
                                            EstatusCatalog::PEDIDO_CANCELADO => 'admin-pedido-badge danger',
                                            default => 'admin-pedido-badge',
                                        };
                                    @endphp
                                    <tr>
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
                                        <td>{{ $metodoPago }}</td>
                                        <td>
                                            <span class="{{ $badgePedidoClass }}">{{ $estatusNombre }}</span>
                                        </td>
                                        <td>{{ $pagoEstatus }}</td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.pedidos.show', $pedido) }}"
                                                       title="Ver detalle">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.pedidos.historial', $pedido) }}"
                                                       title="Ver historial">
                                                        <i class="ri-time-line"></i>
                                                    </a>
                                                </li>
                                                @if (! $esCancelado && $estatusId !== EstatusCatalog::PEDIDO_ENTREGADO)
                                                    <li>
                                                        <a href="{{ route('admin.pedidos.seguimiento', $pedido) }}"
                                                           title="Gestionar seguimiento">
                                                            <i class="ri-truck-line"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($esCancelado)
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                           title="Ocultar del panel admin"
                                                           role="button"
                                                           onclick="if (confirm('¿Ocultar este pedido del panel administrativo? El pedido seguirá visible para el usuario.')) { document.getElementById('ocultar-pedido-{{ $pedido->Id_Pedido }}').submit(); }">
                                                            <i class="ri-eye-off-line"></i>
                                                        </a>
                                                        <form id="ocultar-pedido-{{ $pedido->Id_Pedido }}"
                                                              action="{{ route('admin.pedidos.destroy', $pedido) }}"
                                                              method="POST"
                                                              class="d-none">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay pedidos que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay pedidos registrados.
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
