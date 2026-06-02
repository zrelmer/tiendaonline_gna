@extends('layouts.appadmin')

@section('title', 'Listado de marcas')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Listado de marcas</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-solid" href="{{ route('admin.marcas.create') }}">+ Agregar marca</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.marcas.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-marcas-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-marcas-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-marcas-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, nombre, slug o descripción…"
                                           aria-label="Buscar marcas"
                                           aria-describedby="admin-marcas-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.marcas.index') }}"
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
                                    {{ $marcas->total() }} resultado{{ $marcas->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive admin-productos-table-wrap">
                        <table class="table theme-table table-marca">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre marca</th>
                                    <th>Slug</th>
                                    <th>Productos</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($marcas as $marca)
                                    <tr>
                                        <td>{{ $marca->Id_Marca }}</td>
                                        <td>{{ $marca->Nom_Marca }}</td>
                                        <td>{{ $marca->slug_Marca }}</td>
                                        <td>{{ $marca->productos_count }}</td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('shop.index', ['brand' => $marca->Id_Marca]) }}"
                                                       target="_blank"
                                                       rel="noopener"
                                                       title="Ver en tienda">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('admin.marcas.edit', $marca) }}"
                                                       title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="javascript:void(0)"
                                                       title="Eliminar"
                                                       role="button"
                                                       onclick="if (confirm('¿Eliminar esta marca? Esta acción no se puede deshacer.')) { document.getElementById('delete-marca-{{ $marca->Id_Marca }}').submit(); }">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </a>
                                                    <form id="delete-marca-{{ $marca->Id_Marca }}"
                                                          action="{{ route('admin.marcas.destroy', $marca) }}"
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
                                                No hay marcas que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay marcas registradas.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($marcas->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $marcas->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
