@extends('layouts.appadmin')

@section('title', 'Listado de municipios')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Listado de municipios</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-solid" href="{{ route('admin.municipios.create') }}">+ Agregar municipio</a>
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
                            <form action="{{ route('admin.municipios.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-municipios-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-municipios-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-municipios-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, nombre o departamento…"
                                           aria-label="Buscar municipios"
                                           aria-describedby="admin-municipios-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.municipios.index') }}"
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
                                    {{ $municipios->total() }} resultado{{ $municipios->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive admin-productos-table-wrap">
                        <table class="table theme-table table-municipio">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre municipio</th>
                                    <th>Departamento</th>
                                    <th>Direcciones</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($municipios as $municipio)
                                    <tr>
                                        <td>{{ $municipio->Id_Municipio }}</td>
                                        <td>{{ $municipio->Nom_Municipio }}</td>
                                        <td>{{ $municipio->departamento?->Nom_Departamento ?? '—' }}</td>
                                        <td>{{ $municipio->direcciones_count }}</td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.municipios.edit', $municipio) }}"
                                                       title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="javascript:void(0)"
                                                       title="Eliminar"
                                                       role="button"
                                                       onclick="if (confirm('¿Eliminar este municipio? Esta acción no se puede deshacer.')) { document.getElementById('delete-municipio-{{ $municipio->Id_Municipio }}').submit(); }">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </a>
                                                    <form id="delete-municipio-{{ $municipio->Id_Municipio }}"
                                                          action="{{ route('admin.municipios.destroy', $municipio) }}"
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
                                                No hay municipios que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay municipios registrados.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($municipios->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $municipios->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
