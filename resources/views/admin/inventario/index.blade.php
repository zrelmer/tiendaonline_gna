@extends('layouts.appadmin')

@section('title', 'Inventario')

@php
    use App\Services\AdminInventarioService;
@endphp

@inject('inventarioService', App\Services\AdminInventarioService::class)

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (($inventarioBajoStock ?? 0) > 0 && $filtro !== AdminInventarioService::FILTRO_BAJO_STOCK)
        <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>
                Hay <strong>{{ $inventarioBajoStock }}</strong> producto{{ $inventarioBajoStock === 1 ? '' : 's' }}
                con stock bajo (≤ {{ $inventarioUmbralBajoStock ?? $umbral }} disponibles).
            </span>
            <a href="{{ route('admin.inventario.index', ['filtro' => AdminInventarioService::FILTRO_BAJO_STOCK]) }}"
               class="btn btn-sm btn-outline-dark">
                Ver stock bajo
            </a>
        </div>
    @endif

    <div class="title-header option-title d-sm-flex d-block align-items-center justify-content-between gap-2">
        <h5>Inventario</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.inventario.historial.index') }}" class="btn btn-sm btn-outline-secondary">
                Historial de movimientos
            </a>
            <a href="{{ route('admin.inventario.ventas.index') }}" class="btn btn-sm btn-outline-secondary">
                Ventas por producto
            </a>
            <span class="text-muted small align-self-center">
                Umbral stock bajo: ≤ {{ $umbral }} unidades disponibles
            </span>
        </div>
    </div>

    <div class="row g-3 mb-4 admin-inventario-kpis">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 admin-inventario-kpi-card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Productos en catálogo</p>
                    <h4 class="mb-0">{{ number_format($kpis['total_productos']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.inventario.index', ['filtro' => AdminInventarioService::FILTRO_BAJO_STOCK]) }}"
               class="text-decoration-none d-block h-100 admin-inventario-kpi-card-link">
                <div class="card h-100 admin-inventario-kpi-card admin-inventario-kpi-card--warning">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Stock bajo</p>
                        <h4 class="mb-0">{{ number_format($kpis['bajo_stock']) }}</h4>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 admin-inventario-kpi-card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Sin registro de inventario</p>
                    <h4 class="mb-0">{{ number_format($kpis['sin_inventario']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 admin-inventario-kpi-card admin-inventario-kpi-card--success">
                <div class="card-body">
                    <p class="text-muted small mb-1">Unidades disponibles (total)</p>
                    <h4 class="mb-0">{{ number_format($kpis['unidades_disponibles']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="admin-inventario-filtros mb-3">
                        <p class="text-muted small mb-2">Filtrar:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $queryBase = request()->only('q');
                                $filtros = [
                                    null => [
                                        'etiqueta' => 'Todos',
                                        'conteo' => $kpis['total_productos'],
                                    ],
                                    AdminInventarioService::FILTRO_BAJO_STOCK => [
                                        'etiqueta' => 'Stock bajo',
                                        'conteo' => $conteosFiltro[AdminInventarioService::FILTRO_BAJO_STOCK],
                                    ],
                                    AdminInventarioService::FILTRO_CON_STOCK => [
                                        'etiqueta' => 'Con stock',
                                        'conteo' => $conteosFiltro[AdminInventarioService::FILTRO_CON_STOCK],
                                    ],
                                    AdminInventarioService::FILTRO_SIN_STOCK => [
                                        'etiqueta' => 'Sin stock',
                                        'conteo' => $conteosFiltro[AdminInventarioService::FILTRO_SIN_STOCK],
                                    ],
                                    AdminInventarioService::FILTRO_SIN_INVENTARIO => [
                                        'etiqueta' => 'Sin registro',
                                        'conteo' => $conteosFiltro[AdminInventarioService::FILTRO_SIN_INVENTARIO],
                                    ],
                                ];
                            @endphp
                            @foreach ($filtros as $clave => $info)
                                @php
                                    $params = $queryBase;
                                    if ($clave !== null) {
                                        $params['filtro'] = $clave;
                                    }
                                    $activo = $filtro === $clave || ($clave === null && $filtro === null);
                                @endphp
                                <a href="{{ route('admin.inventario.index', $params) }}"
                                   class="btn btn-sm {{ $activo ? 'btn-primary' : 'btn-outline-secondary' }} admin-inventario-filtro-btn">
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
                            <form action="{{ route('admin.inventario.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-inventario-search-form"
                                  role="search">
                                @if ($filtro !== null)
                                    <input type="hidden" name="filtro" value="{{ $filtro }}">
                                @endif
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-inventario-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-inventario-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, nombre, categoría o marca…"
                                           aria-label="Buscar inventario"
                                           aria-describedby="admin-inventario-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '' || $filtro !== null)
                                        <a href="{{ route('admin.inventario.index') }}"
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
                                    {{ $productos->total() }} resultado{{ $productos->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        Control de stock por producto. Usa <strong>Ajustar</strong> para entradas, salidas o correcciones.
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-inventario-table-wrap">
                        <table class="table theme-table table-inventario">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Reservado</th>
                                    <th>Disponible</th>
                                    <th>Estado</th>
                                    <th>Última act.</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productos as $producto)
                                    @php
                                        $inventario = $producto->inventario;
                                        $stock = (int) ($inventario?->Stock ?? 0);
                                        $reservado = (int) ($inventario?->Stock_Reservado ?? 0);
                                        $disponible = $inventario
                                            ? max(0, $stock - $reservado)
                                            : null;
                                        $sinRegistro = ! $inventario;
                                        $esBajoStock = ! $sinRegistro && $disponible <= $umbral;
                                        $esSinStock = $sinRegistro || $disponible <= 0;
                                        $estadoClass = match (true) {
                                            $sinRegistro => 'admin-inventario-badge admin-inventario-badge--muted',
                                            $esSinStock => 'admin-inventario-badge danger',
                                            $esBajoStock => 'admin-inventario-badge warning-bg',
                                            default => 'admin-inventario-badge success-bg',
                                        };
                                        $estadoLabel = match (true) {
                                            $sinRegistro => 'Sin registro',
                                            $esSinStock => 'Sin stock',
                                            $esBajoStock => 'Stock bajo',
                                            default => 'OK',
                                        };
                                        $ultimaAct = $inventario?->Ultima_Actualizacion;
                                    @endphp
                                    <tr @class(['admin-inventario-row--alerta' => $esBajoStock || $sinRegistro])>
                                        <td>{{ $producto->Id_Producto }}</td>
                                        <td>{{ $producto->Prod_Nombre }}</td>
                                        <td>{{ $producto->categoria?->Cate_Nombre ?? '—' }}</td>
                                        <td>{{ $sinRegistro ? '—' : number_format($stock) }}</td>
                                        <td>{{ $sinRegistro ? '—' : number_format($reservado) }}</td>
                                        <td class="fw-semibold">
                                            {{ $sinRegistro ? '—' : number_format($disponible) }}
                                        </td>
                                        <td>
                                            <span class="{{ $estadoClass }}">{{ $estadoLabel }}</span>
                                        </td>
                                        <td>
                                            @if ($ultimaAct)
                                                {{ \Illuminate\Support\Carbon::parse($ultimaAct)->format('d/m/Y H:i') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <ul>
                                        <li>
                                            <a href="{{ route('admin.inventario.historial.index', ['producto' => $producto->Id_Producto]) }}"
                                               title="Historial del producto">
                                                <i class="ri-history-line"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.inventario.ajustar', $producto) }}"
                                                       title="Ajustar stock">
                                                        <i class="ri-stack-line"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.productos.edit', $producto) }}"
                                                       title="Ver ficha del producto">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '' || $filtro !== null)
                                                No hay productos que coincidan con los filtros aplicados.
                                            @else
                                                No hay productos en el catálogo.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($productos->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $productos->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
