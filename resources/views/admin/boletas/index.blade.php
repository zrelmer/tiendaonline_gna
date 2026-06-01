@extends('layouts.appadmin')

@section('title', 'Boletas de pago')

@section('content')
    <div class="title-header option-title d-sm-flex d-block">
        <h5>Boletas de pago</h5>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <div class="table-header row align-items-center g-3 mb-3">
                        <div class="col-lg-7 col-md-8">
                            <form action="{{ route('admin.boletas.index') }}"
                                  method="GET"
                                  class="admin-productos-search-form admin-boletas-search-form"
                                  role="search">
                                <div class="input-group">
                                    <span class="input-group-text" id="admin-boletas-search-label">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="search"
                                           id="admin-boletas-q"
                                           name="q"
                                           value="{{ $terminoBusqueda }}"
                                           class="form-control"
                                           placeholder="Buscar por ID boleta, ID pedido, número o usuario…"
                                           aria-label="Buscar boletas de pago"
                                           aria-describedby="admin-boletas-search-label">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                    @if ($terminoBusqueda !== '')
                                        <a href="{{ route('admin.boletas.index') }}"
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
                                    {{ $boletas->total() }} resultado{{ $boletas->total() === 1 ? '' : 's' }}
                                    para «{{ $terminoBusqueda }}»
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive admin-productos-table-wrap">
                        <table class="table theme-table table-boleta">
                            <thead>
                                <tr>
                                    <th>ID boleta</th>
                                    <th>ID pedido</th>
                                    <th>Usuario</th>
                                    <th>Archivo</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($boletas as $boleta)
                                    <tr>
                                        <td>{{ $boleta->Id_Boletapago }}</td>
                                        <td>
                                            #{{ $boleta->pedido?->Ped_Numero ?? $boleta->Id_Pedido }}
                                            <span class="text-muted small d-block">ID {{ $boleta->Id_Pedido }}</span>
                                        </td>
                                        <td>{{ $boleta->pedido?->usuario?->Usu_Nombre ?? '—' }}</td>
                                        <td>
                                            @if ($boleta->archivoDisponible())
                                                <span class="admin-boleta-format-badge">
                                                    @if ($boleta->esPdf())
                                                        <i class="ri-file-pdf-line text-danger"></i>
                                                    @else
                                                        <i class="ri-image-line text-primary"></i>
                                                    @endif
                                                    {{ $boleta->etiquetaFormato() }}
                                                </span>
                                            @else
                                                <span class="text-muted small">No disponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul>
                                                @if ($boleta->archivoDisponible())
                                                    <li>
                                                        <a href="{{ route('admin.boletas.download', $boleta) }}"
                                                           title="Descargar comprobante">
                                                            <i class="ri-download-2-line"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="{{ route('admin.boletas.show', $boleta) }}"
                                                       title="Ver detalle">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            @if ($terminoBusqueda !== '')
                                                No hay boletas que coincidan con «{{ $terminoBusqueda }}».
                                            @else
                                                No hay boletas de pago registradas.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($boletas->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $boletas->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
