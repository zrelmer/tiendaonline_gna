@extends('layouts.appadmin')

@section('title', 'Listado de productos')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    {{-- ============================================================
         ZONA 1: Encabezado de página (título, breadcrumb)
         Pega aquí el bloque de tu plantilla Fastkart
    ============================================================ --}}
    <div class="title-header option-title d-sm-flex d-block">
        <h5>Listado de productos</h5>
        <div class="right-options">
            <ul>
                <li>
                    @php
                        $urlExportar = route('admin.productos.export', ['format' => 'csv']);
                        if ($terminoBusqueda !== '') {
                            $urlExportar .= '?q='.urlencode($terminoBusqueda);
                        }
                    @endphp
                    <a href="{{ $urlExportar }}" title="Descargar listado en CSV">
                        Exportar
                    </a>
                </li>
                <li>
                    <a class="btn btn-solid" href="{{ route('admin.productos.create') }}">+ Agregar producto</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- ============================================================
         ZONA 2: Tarjeta principal del listado
    ============================================================ --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    {{-- ZONA 2a: Barra superior (búsqueda) --}}
                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.productos.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-productos-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-productos-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, nombre, slug, categoría o marca…"
                                           aria-label="Buscar productos"
                                           aria-describedby="admin-productos-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.productos.index') }}"
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

                    {{-- ZONA 2b: Tabla de productos --}}
                    <div class="table-responsive admin-productos-table-wrap">
                        <table class="table theme-table table-product">
                            <thead>
                                <tr>
                                    <th>Imagen producto</th>
                                    <th>Nombre producto</th>
                                    <th>Categoría</th>
                                    <th>Cantidad actual</th>
                                    <th>Precio</th>
                                    <th>Estatus</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productos as $producto)
                                    @php
                                        $imagen = $producto->imagenes->sortBy('orden')->first();
                                        $imagenUrl = $imagen
                                            ? asset($imagen->url)
                                            : asset('storage/products/default.png');
                                        $stock = $producto->inventario?->Stock ?? 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="table-image product-list-thumb">
                                                <img src="{{ $imagenUrl }}"
                                                     width="48"
                                                     height="48"
                                                     alt="{{ $producto->Prod_Nombre }}">
                                            </div>
                                        </td>

                                        <td>{{ $producto->Prod_Nombre }}</td>

                                        <td>{{ $producto->categoria?->Cate_Nombre ?? '—' }}</td>

                                        <td>
                                            <a href="{{ route('admin.inventario.ajustar', $producto) }}"
                                               title="Ajustar stock en inventario">
                                                {{ $stock }}
                                            </a>
                                        </td>

                                        <td class="td-price">Q {{ number_format($producto->Prod_Precio, 2) }}</td>

                                        <td class="{{ $producto->Prod_Activo ? 'status-success' : 'status-danger' }}">
                                            <span>{{ $producto->Prod_Activo ? 'Activo' : 'Inactivo' }}</span>
                                        </td>

                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('product.details', ['idproducto' => $producto->Id_Producto, 'slug_producto' => $producto->Prod_Slug]) }}"
                                                       target="_blank"
                                                       rel="noopener"
                                                       title="Ver en tienda">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('admin.productos.edit', $producto) }}"
                                                       title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="javascript:void(0)"
                                                       title="Eliminar"
                                                       role="button"
                                                       onclick="if (confirm('¿Eliminar este producto? Esta acción no se puede deshacer.')) { document.getElementById('delete-producto-{{ $producto->Id_Producto }}').submit(); }">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </a>
                                                    <form id="delete-producto-{{ $producto->Id_Producto }}"
                                                          action="{{ route('admin.productos.destroy', $producto) }}"
                                                          method="POST"
                                                          class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay productos que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay productos registrados. Ejecuta los seeders o agrega el primero.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ZONA 2c: Paginación (mismo diseño que shop/index) --}}
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
