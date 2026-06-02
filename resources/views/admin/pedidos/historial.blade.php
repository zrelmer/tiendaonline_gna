@extends('layouts.appadmin')

@section('title', 'Historial del pedido')

@php
    use App\Support\EstatusCatalog;

    $usuario = $pedido->usuario;
    $estatusId = (int) $pedido->Id_Estatus;
    $estatusNombre = $pedido->estatus?->Nom_Estatus ?? '—';
    $badgePedidoClass = match ($estatusId) {
        EstatusCatalog::PEDIDO_ENTREGADO => 'admin-pedido-badge success-bg',
        EstatusCatalog::PEDIDO_CANCELADO => 'admin-pedido-badge danger',
        default => 'admin-pedido-badge',
    };
@endphp

@section('content')
    <div class="title-header option-title d-sm-flex d-block">
        <h5>Historial · {{ $pedido->Ped_Numero }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.historial.index') }}">
                        Historial general
                    </a>
                </li>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.show', $pedido) }}">
                        Ver detalle del pedido
                    </a>
                </li>
            </ul>
        </div>
    </div>

    @if ($pedido->Ped_OcultoAdmin)
        <div class="alert alert-warning">
            Este pedido está oculto del listado administrativo.
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Resumen</h5>
                    </div>
                    <dl class="mb-0 admin-pedido-meta">
                        <dt>Pedido</dt>
                        <dd>{{ $pedido->Ped_Numero }}</dd>

                        <dt>Cliente</dt>
                        <dd>{{ $usuario?->Usu_Nombre ?? '—' }}</dd>

                        <dt>Estado actual</dt>
                        <dd><span class="{{ $badgePedidoClass }}">{{ $estatusNombre }}</span></dd>

                        <dt>Total de eventos</dt>
                        <dd>{{ $eventos->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Línea de tiempo</h5>
                    </div>

                    @if ($eventos->isNotEmpty())
                        <ul class="admin-pedido-historial-timeline">
                            @foreach ($eventos as $evento)
                                @php
                                    $eventoEstatusId = (int) $evento->Id_Estatus;
                                    $eventoNombre = $evento->estatus?->Nom_Estatus ?? 'Actualización';
                                    $eventoIcon = match ($eventoEstatusId) {
                                        EstatusCatalog::PEDIDO_PENDIENTE => 'ri-time-line',
                                        EstatusCatalog::PEDIDO_CONFIRMADO => 'ri-checkbox-circle-line',
                                        EstatusCatalog::PEDIDO_EN_PREPARACION => 'ri-box-3-line',
                                        EstatusCatalog::PEDIDO_ENVIADO => 'ri-truck-line',
                                        EstatusCatalog::PEDIDO_ENTREGADO => 'ri-check-line',
                                        EstatusCatalog::PEDIDO_CANCELADO => 'ri-close-circle-line',
                                        default => 'ri-information-line',
                                    };
                                    $fechaEvento = $evento->Fecha_Cambio
                                        ? \Illuminate\Support\Carbon::parse($evento->Fecha_Cambio)->format('d/m/Y H:i')
                                        : '';
                                @endphp
                                <li @class([
                                    'admin-pedido-historial-timeline-item',
                                    'is-current' => $loop->last,
                                ])>
                                    <div class="admin-pedido-historial-timeline-marker">
                                        <i class="{{ $eventoIcon }}"></i>
                                    </div>
                                    <div class="admin-pedido-historial-timeline-body">
                                        <h6 class="admin-pedido-historial-timeline-title">{{ $eventoNombre }}</h6>
                                        @if (filled($evento->Comentario))
                                            <p class="text-muted small mb-1">{{ $evento->Comentario }}</p>
                                        @endif
                                        @if ($fechaEvento !== '')
                                            <span class="admin-pedido-historial-timeline-date">{{ $fechaEvento }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">
                            Estado actual: <strong>{{ $estatusNombre }}</strong>.
                            Aún no hay movimientos registrados en el historial de este pedido.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
