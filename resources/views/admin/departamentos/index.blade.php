@extends('layouts.appadmin')

@section('title', 'Listado de departamentos')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Listado de departamentos</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-solid" href="{{ route('admin.departamentos.create') }}">+ Agregar departamento</a>
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
                            <form action="{{ route('admin.departamentos.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-departamentos-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-departamentos-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-departamentos-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID o nombre…"
                                           aria-label="Buscar departamentos"
                                           aria-describedby="admin-departamentos-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.departamentos.index') }}"
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
                                    {{ $departamentos->total() }} resultado{{ $departamentos->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive admin-productos-table-wrap">
                        <table class="table theme-table table-departamento">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre departamento</th>
                                    <th>Municipios</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($departamentos as $departamento)
                                    <tr>
                                        <td>{{ $departamento->Id_Departamento }}</td>
                                        <td>{{ $departamento->Nom_Departamento }}</td>
                                        <td>{{ $departamento->municipios_count }}</td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.departamentos.edit', $departamento) }}"
                                                       title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="javascript:void(0)"
                                                       title="Eliminar"
                                                       role="button"
                                                       onclick="if (confirm('¿Eliminar este departamento? Esta acción no se puede deshacer.')) { document.getElementById('delete-departamento-{{ $departamento->Id_Departamento }}').submit(); }">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </a>
                                                    <form id="delete-departamento-{{ $departamento->Id_Departamento }}"
                                                          action="{{ route('admin.departamentos.destroy', $departamento) }}"
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
                                        <td colspan="4" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay departamentos que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay departamentos registrados.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($departamentos->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $departamentos->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
