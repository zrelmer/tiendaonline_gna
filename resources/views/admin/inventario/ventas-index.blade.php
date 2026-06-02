@extends('layouts.appadmin')

@section('title', 'Ventas por producto')

@section('content')
    <div class="title-header option-title d-sm-flex d-block align-items-center justify-content-between gap-2">
        <h5>Ventas por producto</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.inventario.index') }}" class="btn btn-sm btn-outline-secondary">
                Volver a stock
            </a>
            <a href="{{ route('admin.inventario.historial.index') }}" class="btn btn-sm btn-outline-secondary">
                Historial de movimientos
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4 admin-inventario-ventas-kpis">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 admin-inventario-kpi-card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Unidades vendidas</p>
                    <h4 class="mb-0">{{ number_format($totales['unidades']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 admin-inventario-kpi-card admin-inventario-kpi-card--success">
                <div class="card-body">
                    <p class="text-muted small mb-1">Monto vendido (subtotal líneas)</p>
                    <h4 class="mb-0 td-price">Q {{ number_format($totales['monto'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 admin-inventario-kpi-card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Productos con ventas</p>
                    <h4 class="mb-0">{{ number_format($totales['productos']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 admin-inventario-kpi-card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Pedidos en el periodo</p>
                    <h4 class="mb-0">{{ number_format($totales['pedidos']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <form action="{{ route('admin.inventario.ventas.index') }}"
                          method="GET"
                          class="admin-inventario-ventas-filtros mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label small text-muted" for="admin-inventario-ventas-q">Buscar producto</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-inventario-ventas-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Nombre o ID de producto…">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <label class="form-label small text-muted" for="admin-inventario-ventas-desde">Desde</label>
                                <input type="date"
                                       id="admin-inventario-ventas-desde"
                                       name="fecha_desde"
                                       value="{{ $fechaDesde }}"
                                       class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <label class="form-label small text-muted" for="admin-inventario-ventas-hasta">Hasta</label>
                                <input type="date"
                                       id="admin-inventario-ventas-hasta"
                                       name="fecha_hasta"
                                       value="{{ $fechaHasta }}"
                                       class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <button type="submit" class="btn btn-primary w-100">Aplicar</button>
                            </div>
                        </div>
                        @if ($terminoBusqueda !== '' || $fechaDesde || $fechaHasta)
                            <div class="mt-2">
                                <a href="{{ route('admin.inventario.ventas.index') }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Limpiar filtros
                                </a>
                            </div>
                        @endif
                    </form>

                    <p class="text-muted small mb-3">
                        Unidades y montos desde pedidos <strong>no cancelados</strong>.
                        Orden: más unidades vendidas primero.
                        @if ($fechaDesde || $fechaHasta)
                            Periodo:
                            {{ $fechaDesde ? \Illuminate\Support\Carbon::parse($fechaDesde)->format('d/m/Y') : 'inicio' }}
                            —
                            {{ $fechaHasta ? \Illuminate\Support\Carbon::parse($fechaHasta)->format('d/m/Y') : 'hoy' }}.
                        @else
                            Sin filtro de fechas: histórico completo.
                        @endif
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-inventario-ventas-table-wrap">
                        <table class="table theme-table table-inventario-ventas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Unidades</th>
                                    <th>Pedidos</th>
                                    <th>Monto</th>
                                    <th>Stock disp.</th>
                                    <th>Última venta</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ventas as $indice => $fila)
                                    @php
                                        $producto = $fila->producto;
                                        $rank = $ventas->firstItem() + $indice;
                                    @endphp
                                    <tr>
                                        <td>{{ $rank }}</td>
                                        <td>
                                            <span class="d-block">{{ $producto->Prod_Nombre }}</span>
                                            <span class="text-muted small">ID {{ $producto->Id_Producto }}</span>
                                        </td>
                                        <td>{{ $producto->categoria?->Cate_Nombre ?? '—' }}</td>
                                        <td class="fw-semibold">{{ number_format($fila->unidades_vendidas) }}</td>
                                        <td>{{ number_format($fila->pedidos_count) }}</td>
                                        <td class="td-price">Q {{ number_format($fila->monto_total, 2) }}</td>
                                        <td>{{ number_format($fila->disponible) }}</td>
                                        <td>
                                            {{ $fila->ultima_venta?->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.inventario.ajustar', $producto) }}"
                                                       title="Ajustar stock">
                                                        <i class="ri-stack-line"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.productos.edit', $producto) }}"
                                                       title="Ver producto">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '' || $fechaDesde || $fechaHasta)
                                                No hay ventas que coincidan con los filtros aplicados.
                                            @else
                                                Aún no hay ventas registradas en pedidos.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($ventas->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $ventas->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
