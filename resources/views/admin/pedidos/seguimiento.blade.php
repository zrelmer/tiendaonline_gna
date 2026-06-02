@extends('layouts.appadmin')

@section('title', 'Seguimiento del pedido')

@php
    use App\Services\AdminPedidoSeguimientoService;
    use App\Support\EstatusCatalog;

    $pasosSeguimiento = [
        EstatusCatalog::PEDIDO_PENDIENTE => 'Pendiente',
        EstatusCatalog::PEDIDO_CONFIRMADO => 'Confirmado',
        EstatusCatalog::PEDIDO_EN_PREPARACION => 'En preparación',
        EstatusCatalog::PEDIDO_ENVIADO => 'Enviado',
        EstatusCatalog::PEDIDO_ENTREGADO => 'Entregado',
    ];

    $estatusId = (int) $pedido->Id_Estatus;
    $cancelado = $estatusId === EstatusCatalog::PEDIDO_CANCELADO;
    $entregado = $estatusId === EstatusCatalog::PEDIDO_ENTREGADO;
    $estatusNombre = $pedido->estatus?->Nom_Estatus ?? '—';
    $pagoEstatus = $pedido->pago?->estatus?->Nom_Estatus ?? '—';
    $metodoPago = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? '—';
    $badgePedidoClass = match ($estatusId) {
        EstatusCatalog::PEDIDO_ENTREGADO => 'admin-pedido-badge success-bg',
        EstatusCatalog::PEDIDO_CANCELADO => 'admin-pedido-badge danger',
        default => 'admin-pedido-badge',
    };
@endphp

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->seguimiento->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->seguimiento->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($errors->pedido->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->pedido->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Seguimiento · {{ $pedido->Ped_Numero }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.seguimiento.index') }}">Volver a seguimiento</a>
                </li>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.show', $pedido) }}">Ver detalle</a>
                </li>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.historial', $pedido) }}">Ver historial</a>
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
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Estado actual (vista usuario)</h5>
                    </div>

                    <dl class="mb-3 admin-pedido-meta">
                        <dt>Cliente</dt>
                        <dd>{{ $pedido->usuario?->Usu_Nombre ?? '—' }}</dd>

                        <dt>Método de pago</dt>
                        <dd>{{ $metodoPago }}</dd>

                        <dt>Estatus pago</dt>
                        <dd>{{ $pagoEstatus }}</dd>

                        <dt>Estatus pedido</dt>
                        <dd><span class="{{ $badgePedidoClass }}">{{ $estatusNombre }}</span></dd>
                    </dl>

                    @if ($cancelado)
                        <div class="alert alert-danger py-2 px-3 mb-0 small">
                            Pedido cancelado. No admite avance de seguimiento.
                        </div>
                    @elseif (! $entregado)
                        <div class="admin-pedido-seguimiento-progress mb-0">
                            @foreach ($pasosSeguimiento as $pasoId => $pasoLabel)
                                @php
                                    $completado = $estatusId > $pasoId;
                                    $activo = $estatusId === $pasoId;
                                @endphp
                                <div @class([
                                    'admin-pedido-seguimiento-step',
                                    'is-done' => $completado,
                                    'is-active' => $activo,
                                ])>
                                    <span class="admin-pedido-seguimiento-step-dot"></span>
                                    <span class="admin-pedido-seguimiento-step-label">{{ $pasoLabel }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-success mb-0 small fw-semibold">
                            Pedido completado. El usuario ve la barra en «Entregado».
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Acción disponible</h5>
                    </div>

                    @if ($accionDisponible === AdminPedidoSeguimientoService::ACCION_CONFIRMAR)
                        <p class="text-muted small">
                            Confirma el pedido. En transferencia también aprueba el pago (11 → Pagado) y descuenta inventario.
                            También puedes hacerlo desde
                            <a href="{{ route('admin.boletas.index', ['estado' => 'pendiente']) }}">Boletas de pago</a>.
                        </p>
                        <form method="POST" action="{{ route('admin.pedidos.seguimiento.confirmar', $pedido) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="comentario-confirmar">Comentario para el historial (opcional)</label>
                                <textarea class="form-control"
                                          id="comentario-confirmar"
                                          name="comentario"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Ej.: Pedido confirmado. Iniciando preparación.">{{ old('comentario') }}</textarea>
                            </div>
                            @if ($pedido->boletaPago)
                                <p class="small text-muted mb-3">
                                    <a href="{{ route('admin.boletas.show', $pedido->boletaPago) }}">Ver comprobante de transferencia</a>
                                </p>
                            @endif
                            <button type="submit" class="btn btn-solid">Confirmar pedido</button>
                        </form>
                    @elseif ($accionDisponible === AdminPedidoSeguimientoService::ACCION_PREPARACION)
                        <p class="text-muted small">Marca que el pedido está siendo preparado.</p>
                        <form method="POST" action="{{ route('admin.pedidos.seguimiento.preparacion', $pedido) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="comentario-preparacion">Comentario (opcional)</label>
                                <textarea class="form-control"
                                          id="comentario-preparacion"
                                          name="comentario"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Ej.: Empacando productos.">{{ old('comentario') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-solid">Marcar en preparación</button>
                        </form>
                    @elseif ($accionDisponible === AdminPedidoSeguimientoService::ACCION_ENVIADO)
                        <p class="text-muted small">
                            Registra transportista y guía. El usuario verá la información de envío y recibirá
                            notificación por correo y WhatsApp (si está configurado).
                        </p>
                        <form method="POST" action="{{ route('admin.pedidos.seguimiento.enviado', $pedido) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="empresa_envio">Transportista *</label>
                                <input type="text"
                                       class="form-control"
                                       id="empresa_envio"
                                       name="empresa_envio"
                                       value="{{ old('empresa_envio', $pedido->envio?->Empresa_Envio) }}"
                                       maxlength="200"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="numero_guia">N.º de guía *</label>
                                <input type="text"
                                       class="form-control"
                                       id="numero_guia"
                                       name="numero_guia"
                                       value="{{ old('numero_guia', $pedido->envio?->Numero_Guia) }}"
                                       maxlength="200"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="comentario-enviado">Comentario (opcional)</label>
                                <textarea class="form-control"
                                          id="comentario-enviado"
                                          name="comentario"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Ej.: Pedido en camino.">{{ old('comentario') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-solid">Marcar como enviado</button>
                        </form>
                    @elseif ($accionDisponible === AdminPedidoSeguimientoService::ACCION_ENTREGADO)
                        <p class="text-muted small">
                            Confirma entrega al cliente. Contra entrega marca el pago como cobrado.
                        </p>
                        <form method="POST" action="{{ route('admin.pedidos.seguimiento.entregado', $pedido) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="comentario-entregado">Comentario (opcional)</label>
                                <textarea class="form-control"
                                          id="comentario-entregado"
                                          name="comentario"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Ej.: Entrega confirmada.">{{ old('comentario') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-solid">Marcar como entregado</button>
                        </form>
                    @else
                        <p class="text-muted mb-0">
                            @if ($cancelado)
                                No hay acciones disponibles para pedidos cancelados.
                            @elseif ($entregado)
                                Este pedido ya fue entregado.
                            @else
                                No hay acciones de seguimiento disponibles en este momento.
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Historial registrado (sincronizado con el usuario)</h5>
                    </div>

                    @if ($eventos->isNotEmpty())
                        <ul class="admin-pedido-historial-timeline">
                            @foreach ($eventos as $evento)
                                @php
                                    $eventoNombre = $evento->estatus?->Nom_Estatus ?? 'Actualización';
                                    $fechaEvento = $evento->Fecha_Cambio
                                        ? \Illuminate\Support\Carbon::parse($evento->Fecha_Cambio)->format('d/m/Y H:i')
                                        : '';
                                @endphp
                                <li @class([
                                    'admin-pedido-historial-timeline-item',
                                    'is-current' => $loop->last,
                                ])>
                                    <div class="admin-pedido-historial-timeline-marker">
                                        <i class="ri-history-line"></i>
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
                        <p class="text-muted mb-0">Sin movimientos en el historial.</p>
                    @endif
                </div>
            </div>
        </div>

        @include('admin.pedidos._cancelar-pedido', ['pedido' => $pedido, 'puedeCancelar' => $puedeCancelar])
    </div>
@endsection
