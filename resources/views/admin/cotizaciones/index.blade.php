@extends('layouts.appadmin')

@section('title', 'Listado de cotizaciones')

@php
    use App\Support\EstatusCatalog;
@endphp

@section('content')
    <div class="title-header option-title d-sm-flex d-block align-items-center justify-content-between gap-2">
        <h5>Listado de cotizaciones</h5>
        <a href="{{ route('admin.cotizaciones.pendientes.index') }}" class="btn btn-sm btn-outline-primary">
            Pendientes de atención
        </a>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.cotizaciones.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-cotizaciones-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-cotizaciones-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-cotizaciones-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID, número, cliente, NIT, correo o estatus…"
                                           aria-label="Buscar cotizaciones"
                                           aria-describedby="admin-cotizaciones-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.cotizaciones.index') }}"
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
                                    {{ $cotizaciones->total() }} resultado{{ $cotizaciones->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        Solicitudes creadas por los usuarios desde su panel. Orden: primero en entrar, primero en salir.
                        El admin no crea solicitudes; las revisa y emite desde el detalle (próximo paso).
                    </p>

                    <div class="table-responsive admin-productos-table-wrap admin-cotizaciones-table-wrap">
                        <table class="table theme-table table-cotizacion">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Usuario</th>
                                    <th>Fecha solicitud</th>
                                    <th>Total ref.</th>
                                    <th>Estatus</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cotizaciones as $cotizacion)
                                    @php
                                        $estatusId = (int) $cotizacion->Id_Estatus;
                                        $estatusNombre = $cotizacion->estatus?->Nom_Estatus ?? '—';
                                        $badgeClass = match ($estatusId) {
                                            EstatusCatalog::COTIZACION_EMITIDA, EstatusCatalog::COTIZACION_ACEPTADA => 'success-bg',
                                            EstatusCatalog::COTIZACION_RECHAZADA, EstatusCatalog::COTIZACION_VENCIDA => 'danger',
                                            EstatusCatalog::COTIZACION_EN_REVISION => 'warning-bg',
                                            default => '',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $cotizacion->Id_Cotizacion }}</td>
                                        <td>{{ $cotizacion->Cot_Numero }}</td>
                                        <td>
                                            {{ $cotizacion->Cot_NombreCliente }}
                                            @if ($cotizacion->Cot_Nit)
                                                <span class="text-muted small d-block">NIT {{ $cotizacion->Cot_Nit }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $cotizacion->usuario?->Usu_Nombre ?? '—' }}
                                            @if ($cotizacion->usuario?->Usu_Correo)
                                                <span class="text-muted small d-block">{{ $cotizacion->usuario->Usu_Correo }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $cotizacion->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="td-price">Q {{ number_format((float) $cotizacion->Cot_Total, 2) }}</td>
                                        <td>
                                            <span class="admin-cotizacion-badge {{ $badgeClass }}">{{ $estatusNombre }}</span>
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}"
                                                       title="Ver detalle">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay cotizaciones que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay solicitudes de cotización registradas.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($cotizaciones->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $cotizaciones->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
