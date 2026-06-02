@extends('layouts.appadmin')

@section('title', 'Historial de pedidos')

@section('content')
    <div class="title-header option-title d-sm-flex d-block">
        <h5>Historial de pedidos</h5>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.pedidos.historial.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-pedidos-historial-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-pedidos-historial-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-pedidos-historial-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por pedido, usuario, estatus o comentario…"
                                           aria-label="Buscar historial de pedidos"
                                           aria-describedby="admin-pedidos-historial-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.pedidos.historial.index') }}"
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
                                    {{ $eventos->total() }} resultado{{ $eventos->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        Registro cronológico de movimientos (misma información que ve el usuario en Seguimiento).
                        Orden: del evento más antiguo al más reciente.
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-pedidos-historial-table-wrap">
                        <table class="table theme-table table-pedido-historial">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Pedido</th>
                                    <th>Usuario</th>
                                    <th>Estatus registrado</th>
                                    <th>Comentario</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($eventos as $evento)
                                    @php
                                        $pedido = $evento->pedido;
                                    @endphp
                                    <tr>
                                        <td>{{ $evento->Fecha_Cambio ? \Illuminate\Support\Carbon::parse($evento->Fecha_Cambio)->format('d/m/Y H:i') : '—' }}</td>
                                        <td>
                                            @if ($pedido)
                                                <a href="{{ route('admin.pedidos.show', $pedido) }}">{{ $pedido->Ped_Numero }}</a>
                                                <span class="text-muted small d-block">ID {{ $pedido->Id_Pedido }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $pedido?->usuario?->Usu_Nombre ?? '—' }}</td>
                                        <td>{{ $evento->estatus?->Nom_Estatus ?? '—' }}</td>
                                        <td>{{ $evento->Comentario ?: '—' }}</td>
                                        <td>
                                            @if ($pedido)
                                                <ul>
                                                    <li>
                                                        <a href="{{ route('admin.pedidos.historial', $pedido) }}"
                                                           title="Ver historial completo del pedido">
                                                            <i class="ri-time-line"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay eventos que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay movimientos registrados en el historial.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($eventos->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $eventos->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
