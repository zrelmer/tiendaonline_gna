@extends('layouts.appadmin')

@section('title', 'Historial de inventario')

@inject('inventarioService', App\Services\AdminInventarioService::class)

@section('content')
    <div class="title-header option-title d-sm-flex d-block align-items-center justify-content-between gap-2">
        <h5>Historial de inventario</h5>
        <a href="{{ route('admin.inventario.index') }}" class="btn btn-sm btn-outline-secondary">
            Volver a stock
        </a>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <form action="{{ route('admin.inventario.historial.index') }}"
                          method="GET"
                          class="admin-inventario-historial-filtros mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label small text-muted" for="admin-inventario-historial-q">Buscar</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-inventario-historial-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Referencia, producto, tipo o ID pedido…">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label class="form-label small text-muted" for="admin-inventario-historial-movimiento">Tipo</label>
                                <select id="admin-inventario-historial-movimiento"
                                        name="movimiento"
                                        class="form-select">
                                    <option value="">Todos</option>
                                    @foreach ($tiposMovimiento as $tipo)
                                        <option value="{{ $tipo->Id_Movimiento }}"
                                            @selected($idMovimiento === (int) $tipo->Id_Movimiento)>
                                            {{ $tipo->Nom_Movimiento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label small text-muted" for="admin-inventario-historial-producto">Producto</label>
                                <select id="admin-inventario-historial-producto"
                                        name="producto"
                                        class="form-select">
                                    <option value="">Todos</option>
                                    @foreach ($productosFiltro as $producto)
                                        <option value="{{ $producto->Id_Producto }}"
                                            @selected($idProducto === (int) $producto->Id_Producto)>
                                            {{ $producto->Prod_Nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-1 col-md-3">
                                <label class="form-label small text-muted" for="admin-inventario-historial-desde">Desde</label>
                                <input type="date"
                                       id="admin-inventario-historial-desde"
                                       name="fecha_desde"
                                       value="{{ $fechaDesde }}"
                                       class="form-control">
                            </div>

                            <div class="col-lg-1 col-md-3">
                                <label class="form-label small text-muted" for="admin-inventario-historial-hasta">Hasta</label>
                                <input type="date"
                                       id="admin-inventario-historial-hasta"
                                       name="fecha_hasta"
                                       value="{{ $fechaHasta }}"
                                       class="form-control">
                            </div>

                            <div class="col-lg-1 col-md-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                                </div>
                            </div>
                        </div>

                        @if ($terminoBusqueda !== '' || $idMovimiento || $idProducto || $fechaDesde || $fechaHasta)
                            <div class="mt-2">
                                <a href="{{ route('admin.inventario.historial.index') }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Limpiar filtros
                                </a>
                            </div>
                        @endif
                    </form>

                    <p class="text-muted small mb-3">
                        Movimientos de stock registrados por ventas, ajustes manuales y devoluciones.
                        Orden: más recientes primero.
                    </p>

                    @if ($terminoBusqueda !== '')
                        <p class="text-muted small admin-productos-search-summary mb-3">
                            {{ $movimientos->total() }} resultado{{ $movimientos->total() === 1 ? '' : 's' }}
                            para «{{ $terminoBusqueda }}»
                        </p>
                    @endif

                    <div class="table-responsive admin-productos-table-wrap admin-inventario-historial-table-wrap">
                        <table class="table theme-table table-inventario-historial">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cant.</th>
                                    <th>Stock antes</th>
                                    <th>Stock después</th>
                                    <th>Referencia</th>
                                    <th>Enlace</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movimientos as $movimiento)
                                    @php
                                        $producto = $movimiento->inventario?->producto;
                                        $stockAntes = (int) $movimiento->Stock_Antes;
                                        $stockDespues = (int) $movimiento->Stock_Despues;
                                        $signo = $inventarioService->signoMovimiento($stockAntes, $stockDespues);
                                        $esEntrada = $inventarioService->esIncrementoStock($stockAntes, $stockDespues);
                                        $idPedido = $inventarioService->idPedidoDesdeReferencia($movimiento->Referencia);
                                        $signoClass = match (true) {
                                            $esEntrada => 'admin-inventario-historial-signo--entrada',
                                            $signo !== '' => 'admin-inventario-historial-signo--salida',
                                            default => '',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $movimiento->Id_InventarioHistorial }}</td>
                                        <td>
                                            {{ $movimiento->Fecha_Movimiento?->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td>
                                            @if ($producto)
                                                <span class="d-block">{{ $producto->Prod_Nombre }}</span>
                                                <span class="text-muted small">ID {{ $producto->Id_Producto }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <span class="admin-inventario-historial-tipo">
                                                {{ $movimiento->movimiento?->Nom_Movimiento ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="admin-inventario-historial-signo {{ $signoClass }}">
                                                {{ $signo }}{{ number_format((int) $movimiento->Cantidad) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($stockAntes) }}</td>
                                        <td>{{ number_format($stockDespues) }}</td>
                                        <td>
                                            <code class="admin-inventario-historial-ref small">
                                                {{ $movimiento->Referencia ?: '—' }}
                                            </code>
                                        </td>
                                        <td>
                                            <ul>
                                                @if ($idPedido)
                                                    <li>
                                                        <a href="{{ route('admin.pedidos.show', $idPedido) }}"
                                                           title="Ver pedido #{{ $idPedido }}">
                                                            <i class="ri-shopping-bag-3-line"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($producto)
                                                    <li>
                                                        <a href="{{ route('admin.inventario.ajustar', $producto) }}"
                                                           title="Ajustar stock">
                                                            <i class="ri-stack-line"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '' || $idMovimiento || $idProducto || $fechaDesde || $fechaHasta)
                                                No hay movimientos que coincidan con los filtros aplicados.
                                            @else
                                                Aún no hay movimientos registrados en el historial.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($movimientos->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $movimientos->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
