@extends('layouts.appadmin')

@section('title', 'Detalle de boleta de pago')

@php
    use App\Support\EstatusCatalog;
@endphp

@section('content')
    @php
        $pedido = $boleta->pedido;
        $usuario = $pedido?->usuario;
        $pago = $pedido?->pago;
        $pagoEstatusId = (int) ($pago?->Id_Estatus ?? 0);
        $pedidoEstatusId = (int) ($pedido?->Id_Estatus ?? 0);
    @endphp

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->boleta->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->boleta->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Boleta de pago #{{ $boleta->Id_Boletapago }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.boletas.index') }}">Volver al listado</a>
                </li>
                @if ($pedido)
                    <li>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.show', $pedido) }}">Ver pedido</a>
                    </li>
                    @if ($pedidoEstatusId !== EstatusCatalog::PEDIDO_CANCELADO)
                        <li>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.seguimiento', $pedido) }}">Seguimiento</a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Información del comprobante</h5>
                    </div>

                    <dl class="mb-0 admin-boleta-meta">
                        <dt>ID boleta</dt>
                        <dd>{{ $boleta->Id_Boletapago }}</dd>

                        <dt>ID pedido</dt>
                        <dd>{{ $boleta->Id_Pedido }}</dd>

                        <dt>Número de pedido</dt>
                        <dd>{{ $pedido?->Ped_Numero ?? '—' }}</dd>

                        <dt>Usuario</dt>
                        <dd>{{ $usuario?->Usu_Nombre ?? '—' }}</dd>

                        <dt>Correo</dt>
                        <dd>{{ $usuario?->Usu_Correo ?? '—' }}</dd>

                        <dt>Fecha de carga</dt>
                        <dd>{{ $boleta->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                        <dt>Formato</dt>
                        <dd>{{ $boleta->archivoDisponible() ? $boleta->etiquetaFormato() : '—' }}</dd>

                        <dt>Estatus pago</dt>
                        <dd>{{ $pago?->estatus?->Nom_Estatus ?? '—' }}</dd>

                        <dt>Estatus pedido</dt>
                        <dd>
                            <span @class([
                                'admin-pedido-badge',
                                'success-bg' => $pedidoEstatusId === EstatusCatalog::PEDIDO_ENTREGADO,
                                'danger' => $pedidoEstatusId === EstatusCatalog::PEDIDO_CANCELADO,
                            ])>{{ $pedido?->estatus?->Nom_Estatus ?? '—' }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Comprobante</h5>
                    </div>

                    @if ($boleta->archivoDisponible())
                        @if ($boleta->esImagen())
                            <div class="admin-boleta-preview-wrap mb-3 text-center">
                                <img src="{{ $boleta->urlArchivo() }}"
                                     alt="Comprobante de transferencia"
                                     class="admin-boleta-preview-thumb img-fluid">
                            </div>
                        @else
                            <div class="admin-boleta-download-box mb-3">
                                <div class="admin-boleta-download-icon mb-3">
                                    <i class="ri-file-pdf-line text-danger"></i>
                                </div>
                                <p class="text-muted mb-0">Comprobante en formato PDF.</p>
                            </div>
                        @endif

                        <a href="{{ route('admin.boletas.download', $boleta) }}"
                           class="btn btn-outline-secondary">
                            <i class="ri-download-2-line me-1"></i>
                            Descargar comprobante
                        </a>
                    @else
                        <p class="text-muted mb-0">
                            El archivo del comprobante no está disponible. Puede haber sido movido o eliminado del almacenamiento.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Verificación de transferencia</h5>
                    </div>

                    @if ($puedeAprobar || $puedeRechazar)
                        <p class="text-muted small">
                            Al aprobar el comprobante también se confirma el pedido: el pago pasa a <strong>Pagado</strong>
                            y el pedido a <strong>Confirmado</strong> (se descuenta inventario).
                            Si rechazas, el cliente deberá subir un nuevo comprobante.
                        </p>
                        <div class="row g-4">
                            @if ($puedeAprobar)
                                <div class="col-lg-6">
                                    <form method="POST"
                                          action="{{ route('admin.boletas.aprobar', $boleta) }}"
                                          onsubmit="return confirm('¿Aprobar este comprobante y confirmar el pedido {{ $pedido?->Ped_Numero }}?');">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label" for="comentario-aprobar">Comentario para el historial (opcional)</label>
                                            <textarea class="form-control"
                                                      id="comentario-aprobar"
                                                      name="comentario"
                                                      rows="2"
                                                      maxlength="500"
                                                      placeholder="Ej.: Comprobante verificado. Pedido confirmado.">{{ old('comentario') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-solid">
                                            <i class="ri-checkbox-circle-line me-1"></i>
                                            Aprobar y confirmar pedido
                                        </button>
                                    </form>
                                </div>
                            @endif
                            @if ($puedeRechazar)
                                <div class="col-lg-6">
                                    <form method="POST"
                                          action="{{ route('admin.boletas.rechazar', $boleta) }}"
                                          onsubmit="return confirm('¿Rechazar este comprobante? El cliente deberá subir uno nuevo.');">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label" for="motivo-rechazar">Motivo del rechazo *</label>
                                            <textarea class="form-control"
                                                      id="motivo-rechazar"
                                                      name="motivo"
                                                      rows="2"
                                                      maxlength="500"
                                                      required
                                                      placeholder="Ej.: El monto no coincide con el total del pedido.">{{ old('motivo') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="ri-close-circle-line me-1"></i>
                                            Rechazar comprobante
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @elseif ($pagoEstatusId === EstatusCatalog::PAGO_PAGADO && $pedidoEstatusId >= EstatusCatalog::PEDIDO_CONFIRMADO)
                        <div class="alert alert-success mb-0">
                            Este comprobante ya fue aprobado. El pedido está en «{{ $pedido?->estatus?->Nom_Estatus ?? 'Confirmado' }}».
                            @if ($pedido && $pedidoEstatusId !== EstatusCatalog::PEDIDO_CANCELADO)
                                Puedes continuar el flujo desde
                                <a href="{{ route('admin.pedidos.seguimiento', $pedido) }}">Seguimiento del pedido</a>.
                            @endif
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            {{ $motivoNoAprobable ?? 'Esta boleta no admite aprobación en este momento.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
