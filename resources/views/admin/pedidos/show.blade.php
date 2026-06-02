@extends('layouts.appadmin')

@section('title', 'Detalle de pedido')

@php
    use App\Support\EstatusCatalog;

    $usuario = $pedido->usuario;
    $pago = $pedido->pago;
    $envio = $pedido->envio;
    $boleta = $pedido->boletaPago;
    $lineas = $pedido->detalle ?? collect();
    $subtotal = $lineas->sum(fn ($linea) => (float) $linea->DetaPed_SubTotal);
    $total = (float) $pedido->Ped_TotalPrecio;
    $costoEnvio = max(0, $total - $subtotal);
    $estatusId = (int) $pedido->Id_Estatus;
    $estatusNombre = $pedido->estatus?->Nom_Estatus ?? '—';
    $esCancelado = $estatusId === EstatusCatalog::PEDIDO_CANCELADO;
    $badgePedidoClass = match ($estatusId) {
        EstatusCatalog::PEDIDO_ENTREGADO => 'admin-pedido-badge success-bg',
        EstatusCatalog::PEDIDO_CANCELADO => 'admin-pedido-badge danger',
        default => 'admin-pedido-badge',
    };
    $direccionTexto = $envio?->Direccion_Envio
        ?? collect([
            $pedido->direccion?->Direccion,
            $pedido->direccion?->municipio?->Nom_Municipio,
            $pedido->direccion?->municipio?->departamento?->Nom_Departamento,
        ])->filter()->implode(', ');
@endphp

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="title-header option-title d-sm-flex d-block">
        <h5>Pedido {{ $pedido->Ped_Numero }}</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.index') }}">Volver al listado</a>
                </li>
                <li>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pedidos.historial', $pedido) }}">
                        Ver historial
                    </a>
                </li>
                @if (! $esCancelado && $estatusId !== EstatusCatalog::PEDIDO_ENTREGADO)
                    <li>
                        <a class="btn btn-solid" href="{{ route('admin.pedidos.seguimiento', $pedido) }}">
                            Gestionar seguimiento
                        </a>
                    </li>
                @endif
                @if ($esCancelado && ! $pedido->Ped_OcultoAdmin)
                    <li>
                        <a href="javascript:void(0)"
                           class="btn btn-outline-danger"
                           role="button"
                           onclick="if (confirm('¿Ocultar este pedido del panel administrativo? El pedido seguirá visible para el usuario.')) { document.getElementById('ocultar-pedido-detalle').submit(); }">
                            Ocultar pedido
                        </a>
                        <form id="ocultar-pedido-detalle"
                              action="{{ route('admin.pedidos.destroy', $pedido) }}"
                              method="POST"
                              class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    @if ($pedido->Ped_OcultoAdmin)
        <div class="alert alert-warning">
            Este pedido está oculto del listado administrativo. El usuario aún lo ve en su panel.
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Resumen del pedido</h5>
                    </div>
                    <dl class="mb-0 admin-pedido-meta">
                        <dt>ID pedido</dt>
                        <dd>{{ $pedido->Id_Pedido }}</dd>

                        <dt>Número</dt>
                        <dd>{{ $pedido->Ped_Numero }}</dd>

                        <dt>Fecha</dt>
                        <dd>{{ $pedido->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                        <dt>Estatus</dt>
                        <dd><span class="{{ $badgePedidoClass }}">{{ $estatusNombre }}</span></dd>

                        <dt>Total</dt>
                        <dd class="fw-bold">Q {{ number_format($total, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Cliente</h5>
                    </div>
                    <dl class="mb-0 admin-pedido-meta">
                        <dt>Nombre</dt>
                        <dd>{{ $usuario?->Usu_Nombre ?? '—' }}</dd>

                        <dt>Correo</dt>
                        <dd>{{ $usuario?->Usu_Correo ?? '—' }}</dd>

                        <dt>Teléfono</dt>
                        <dd>{{ $usuario?->Usu_Telefono ?? '—' }}</dd>

                        <dt>ID usuario</dt>
                        <dd>{{ $pedido->Id_Usuario }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Pago</h5>
                    </div>
                    <dl class="mb-0 admin-pedido-meta">
                        <dt>Método</dt>
                        <dd>{{ $pago?->metodoPago?->MetPag_Descripcion ?? '—' }}</dd>

                        <dt>Estatus pago</dt>
                        <dd>{{ $pago?->estatus?->Nom_Estatus ?? '—' }}</dd>

                        @if ($pago?->Transaccion_Id)
                            <dt>ID transacción</dt>
                            <dd>{{ $pago->Transaccion_Id }}</dd>
                        @endif

                        @if ($boleta)
                            <dt>Comprobante</dt>
                            <dd>
                                <a href="{{ route('admin.boletas.show', $boleta) }}">
                                    Ver boleta #{{ $boleta->Id_Boletapago }}
                                </a>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Entrega</h5>
                    </div>
                    <dl class="mb-0 admin-pedido-meta">
                        <dt>Dirección</dt>
                        <dd>{{ $direccionTexto !== '' ? $direccionTexto : '—' }}</dd>

                        @if ($envio)
                            @if ($envio->Empresa_Envio)
                                <dt>Transportista</dt>
                                <dd>{{ $envio->Empresa_Envio }}</dd>
                            @endif
                            @if ($envio->Numero_Guia)
                                <dt>N.º de guía</dt>
                                <dd>{{ $envio->Numero_Guia }}</dd>
                            @endif
                            @if ($envio->estatus?->Nom_Estatus)
                                <dt>Estatus envío</dt>
                                <dd>{{ $envio->estatus->Nom_Estatus }}</dd>
                            @endif
                            @if ($envio->Fecha_Envio)
                                <dt>Fecha de envío</dt>
                                <dd>{{ \Illuminate\Support\Carbon::parse($envio->Fecha_Envio)->format('d/m/Y H:i') }}</dd>
                            @endif
                            @if ($envio->Fecha_Entrega)
                                <dt>Fecha de entrega</dt>
                                <dd>{{ \Illuminate\Support\Carbon::parse($envio->Fecha_Entrega)->format('d/m/Y H:i') }}</dd>
                            @endif
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="card-header-2 mb-3">
                        <h5>Productos del pedido</h5>
                    </div>

                    <div class="table-responsive admin-pedidos-table-wrap">
                        <table class="table theme-table table-pedido">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Producto</th>
                                    <th>Precio unit.</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lineas as $linea)
                                    @php
                                        $producto = $linea->producto;
                                        $imagen = $producto?->imagenes?->sortBy('orden')->first();
                                        $imagenUrl = $imagen
                                            ? asset($imagen->url)
                                            : asset('storage/products/default.png');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="table-image product-list-thumb">
                                                <img src="{{ $imagenUrl }}"
                                                     width="48"
                                                     height="48"
                                                     alt="{{ $producto?->Prod_Nombre ?? 'Producto' }}">
                                            </div>
                                        </td>
                                        <td>
                                            @if ($producto)
                                                <span class="d-block">{{ $producto->Prod_Nombre }}</span>
                                                <span class="text-muted small">ID {{ $producto->Id_Producto }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>Q {{ number_format((float) $linea->DetaPed_Precio, 2) }}</td>
                                        <td>{{ (int) $linea->DetaPed_Cantidad }}</td>
                                        <td class="td-price">Q {{ number_format((float) $linea->DetaPed_SubTotal, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Este pedido no tiene líneas de detalle.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($lineas->isNotEmpty())
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold">Subtotal productos</td>
                                        <td class="td-price">Q {{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold">Envío</td>
                                        <td class="td-price">Q {{ number_format($costoEnvio, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total</td>
                                        <td class="td-price fw-bold">Q {{ number_format($total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.pedidos._cancelar-pedido', ['pedido' => $pedido, 'puedeCancelar' => $puedeCancelar])
@endsection
