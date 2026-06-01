@extends('layouts.appadmin')

@section('title', 'Detalle de boleta de pago')

@section('content')
    @php
        $pedido = $boleta->pedido;
        $usuario = $pedido?->usuario;
    @endphp

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Boleta de pago #{{ $boleta->Id_Boletapago }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.boletas.index') }}">Volver al listado</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
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
                    </dl>

                    <div class="admin-boleta-download-box mt-4">
                        @if ($boleta->archivoDisponible())
                            <div class="admin-boleta-download-icon mb-3">
                                @if ($boleta->esPdf())
                                    <i class="ri-file-pdf-line text-danger"></i>
                                @else
                                    <i class="ri-image-line text-primary"></i>
                                @endif
                            </div>
                            <p class="text-muted mb-3">
                                Descarga el comprobante para revisarlo en tu equipo (PNG, JPG o PDF).
                            </p>
                            <a href="{{ route('admin.boletas.download', $boleta) }}"
                               class="btn btn-solid">
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
        </div>
    </div>
@endsection
