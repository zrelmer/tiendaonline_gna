@php
    use App\Support\EstatusCatalog;

    $pasosSeguimiento = [
        EstatusCatalog::PEDIDO_PENDIENTE => ['label' => 'Pendiente', 'icon' => 'clock'],
        EstatusCatalog::PEDIDO_CONFIRMADO => ['label' => 'Confirmado', 'icon' => 'check-circle'],
        EstatusCatalog::PEDIDO_EN_PREPARACION => ['label' => 'En preparación', 'icon' => 'package'],
        EstatusCatalog::PEDIDO_ENVIADO => ['label' => 'Enviado', 'icon' => 'truck'],
        EstatusCatalog::PEDIDO_ENTREGADO => ['label' => 'Entregado', 'icon' => 'check'],
    ];
@endphp

<div class="dashboard-tracking">
    <div class="title">
        <h2>Seguimiento de órdenes</h2>
        <span class="title-leaf title-leaf-gray">
            <svg class="icon-width bg-gray">
                <use xlink:href="{{ asset('assets/svg/leaf.svg') }}#leaf"></use>
            </svg>
        </span>
    </div>

    <p class="text-content mb-4">
        Consulta el avance de tus pedidos. Los cambios de estado los registra el equipo de la tienda.
    </p>

    <div class="dashboard-tracking-list">
        @forelse ($pedidos as $pedido)
            @php
                $estatusId = (int) $pedido->Id_Estatus;
                $cancelado = $estatusId === EstatusCatalog::PEDIDO_CANCELADO;
                $estatusNombre = $pedido->estatus?->Nom_Estatus ?? 'Sin estatus';
                $historial = ($pedido->historial ?? collect())
                    ->sortBy('Fecha_Cambio')
                    ->values();
                $envio = $pedido->envio;
                $pagoEstatus = $pedido->pago?->estatus?->Nom_Estatus;
                $mostrarEnvio = $envio && (
                    filled($envio->Numero_Guia)
                    || filled($envio->Empresa_Envio)
                    || $estatusId >= EstatusCatalog::PEDIDO_ENVIADO
                    || (int) ($envio->Id_Estatus ?? 0) >= EstatusCatalog::ENVIO_PENDIENTE
                );
            @endphp

            <div class="dashboard-bg-box dashboard-tracking-card mb-4">
                <div class="dashboard-tracking-card-header">
                    <div>
                        <h4 class="dashboard-tracking-order mb-1">{{ $pedido->Ped_Numero }}</h4>
                        <p class="text-content small mb-0">
                            Realizado el {{ $pedido->created_at?->format('d/m/Y H:i') ?? '—' }}
                            @if ($pagoEstatus)
                                · Pago: {{ $pagoEstatus }}
                            @endif
                        </p>
                    </div>
                    <span @class([
                        'dashboard-tracking-badge',
                        'success-bg' => $estatusId === EstatusCatalog::PEDIDO_ENTREGADO,
                        'danger' => $cancelado,
                    ])>{{ $estatusNombre }}</span>
                </div>

                @if ($cancelado)
                    <div class="alert alert-danger py-2 px-3 mb-3 small">
                        Este pedido fue cancelado. Puedes revisar el historial de movimientos abajo.
                    </div>
                @else
                    <div class="dashboard-tracking-progress mb-4">
                        @foreach ($pasosSeguimiento as $pasoId => $paso)
                            @php
                                $completado = $estatusId > $pasoId;
                                $activo = $estatusId === $pasoId;
                            @endphp
                            <div @class([
                                'dashboard-tracking-step',
                                'is-done' => $completado,
                                'is-active' => $activo,
                            ])>
                                <div class="dashboard-tracking-step-icon">
                                    <i data-feather="{{ $paso['icon'] }}"></i>
                                </div>
                                <span class="dashboard-tracking-step-label">{{ $paso['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($mostrarEnvio)
                    <div class="dashboard-tracking-shipping mb-4">
                        <h6 class="mb-2">Información de envío</h6>
                        <ul class="dashboard-tracking-shipping-list mb-0">
                            @if ($envio->Empresa_Envio)
                                <li><strong>Transportista:</strong> {{ $envio->Empresa_Envio }}</li>
                            @endif
                            @if ($envio->Numero_Guia)
                                <li><strong>N.º de guía:</strong> {{ $envio->Numero_Guia }}</li>
                            @endif
                            @if ($envio->Direccion_Envio)
                                <li><strong>Dirección:</strong> {{ $envio->Direccion_Envio }}</li>
                            @endif
                            @if ($envio->estatus?->Nom_Estatus)
                                <li><strong>Estado logístico:</strong> {{ $envio->estatus->Nom_Estatus }}</li>
                            @endif
                            @if ($envio->Fecha_Envio)
                                <li><strong>Fecha de envío:</strong> {{ \Illuminate\Support\Carbon::parse($envio->Fecha_Envio)->format('d/m/Y H:i') }}</li>
                            @endif
                            @if ($envio->Fecha_Entrega)
                                <li><strong>Fecha de entrega:</strong> {{ \Illuminate\Support\Carbon::parse($envio->Fecha_Entrega)->format('d/m/Y H:i') }}</li>
                            @endif
                        </ul>
                    </div>
                @endif

                <div class="dashboard-tracking-timeline-wrap">
                    <h6 class="mb-3">Historial del pedido</h6>

                    @if ($historial->isNotEmpty())
                        <ul class="dashboard-tracking-timeline">
                            @foreach ($historial as $evento)
                                @php
                                    $eventoEstatusId = (int) $evento->Id_Estatus;
                                    $eventoNombre = $evento->estatus?->Nom_Estatus ?? 'Actualización';
                                    $eventoIcon = match ($eventoEstatusId) {
                                        EstatusCatalog::PEDIDO_PENDIENTE => 'clock',
                                        EstatusCatalog::PEDIDO_CONFIRMADO => 'check-circle',
                                        EstatusCatalog::PEDIDO_EN_PREPARACION => 'package',
                                        EstatusCatalog::PEDIDO_ENVIADO => 'truck',
                                        EstatusCatalog::PEDIDO_ENTREGADO => 'check',
                                        EstatusCatalog::PEDIDO_CANCELADO => 'x-circle',
                                        default => 'info',
                                    };
                                    $fechaEvento = $evento->Fecha_Cambio
                                        ? \Illuminate\Support\Carbon::parse($evento->Fecha_Cambio)->format('d/m/Y H:i')
                                        : '';
                                @endphp
                                <li @class([
                                    'dashboard-tracking-timeline-item',
                                    'is-current' => $loop->last,
                                ])>
                                    <div class="dashboard-tracking-timeline-marker">
                                        <i data-feather="{{ $eventoIcon }}"></i>
                                    </div>
                                    <div class="dashboard-tracking-timeline-body">
                                        <h6 class="dashboard-tracking-timeline-title">{{ $eventoNombre }}</h6>
                                        @if (filled($evento->Comentario))
                                            <p class="text-content small mb-1">{{ $evento->Comentario }}</p>
                                        @endif
                                        @if ($fechaEvento !== '')
                                            <span class="dashboard-tracking-timeline-date">{{ $fechaEvento }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-content small mb-0">
                            Estado actual: <strong>{{ $estatusNombre }}</strong>.
                            Aún no hay movimientos registrados en el historial.
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="dashboard-bg-box p-4 text-center">
                <p class="text-content mb-3">No tienes pedidos para rastrear.</p>
                <a href="{{ route('shop.index') }}" class="btn theme-bg-color text-white btn-sm">
                    Ir a la tienda
                </a>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var trackingTab = document.getElementById('pills-tracking-tab');
        if (trackingTab && window.feather) {
            trackingTab.addEventListener('shown.bs.tab', function () {
                window.feather.replace();
            });
        }
    });
</script>
@endpush
