@extends('layouts.appadmin')

@section('title', 'Listado de categorías')

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
        <h5>Listado de categorías</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-solid" href="{{ route('admin.categorias.create') }}">+ Agregar categoría</a>
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
                            <form action="{{ route('admin.categorias.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-categorias-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-categorias-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-categorias-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, nombre, slug o descripción…"
                                           aria-label="Buscar categorías"
                                           aria-describedby="admin-categorias-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.categorias.index') }}"
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
                                    {{ $categorias->total() }} resultado{{ $categorias->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- ZONA 2b: Tabla de categorías --}}
                    <div class="table-responsive admin-productos-table-wrap">
                        <table class="table theme-table table-categoria">
                            <thead>
                                <tr>
                                    <th>Imagen Categoría</th>
                                    <th>Nombre Categoría</th>
                                    <th>Slug Categoría</th>
                                    <th>Descripción Categoría</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categorias as $categoria)
                                    @php
                                        $imagenUrl = $categoria->Cate_Imagen
                                            ? asset($categoria->Cate_Imagen)
                                            : asset('storage/products/p1.png');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="table-image product-list-thumb">
                                                <img src="{{ $imagenUrl }}"
                                                     width="48"
                                                     height="48"
                                                     alt="{{ $categoria->Cate_Nombre }}">
                                            </div>
                                        </td>

                                        <td>{{ $categoria->Cate_Nombre }}</td>

                                        <td>{{ $categoria->Cate_Slug }}</td>

                                        <td>{{ $categoria->Cate_Descripcion }}</td>

                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('shop.index', ['category' => $categoria->Id_Categoria]) }}"
                                                       target="_blank"
                                                       rel="noopener"
                                                       title="Ver en tienda">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('admin.categorias.edit', $categoria) }}"
                                                       title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="javascript:void(0)"
                                                       title="Eliminar"
                                                       role="button"
                                                       onclick="if (confirm('¿Eliminar esta categoría? Esta acción no se puede deshacer.')) { document.getElementById('delete-categoria-{{ $categoria->Id_Categoria }}').submit(); }">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </a>
                                                    <form id="delete-categoria-{{ $categoria->Id_Categoria }}"
                                                          action="{{ route('admin.categorias.destroy', $categoria) }}"
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
                                        <td colspan="5" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay categorías que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay categorías registradas.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ZONA 2c: Paginación (mismo diseño que shop/index) --}}
                    @if ($categorias->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $categorias->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
