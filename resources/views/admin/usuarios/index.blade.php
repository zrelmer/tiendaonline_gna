@extends('layouts.appadmin')

@section('title', 'Listado de usuarios')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Listado de usuarios</h5>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.usuarios.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-usuarios-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-usuarios-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-usuarios-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por nombre, correo, teléfono, rol o dirección…"
                                           aria-label="Buscar usuarios"
                                           aria-describedby="admin-usuarios-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.usuarios.index') }}"
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
                                    {{ $usuarios->total() }} resultado{{ $usuarios->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive admin-productos-table-wrap">
                        <table class="table theme-table table-usuario">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Rol</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->Usu_Nombre }}</td>
                                        <td>{{ $usuario->Usu_Correo }}</td>
                                        <td>{{ $usuario->Usu_Telefono ?: '—' }}</td>
                                        <td>{{ $usuario->textoDireccionPrincipal() }}</td>
                                        <td>{{ $usuario->roles?->Rol_Nombre ?? '—' }}</td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                                       title="Editar rol">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay usuarios que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay usuarios registrados.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($usuarios->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $usuarios->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
