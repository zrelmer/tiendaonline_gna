@php
    use App\Support\EstatusCatalog;
@endphp

<div class="dashboard-order">
    <div class="title">
        <h2>Mis pedidos</h2>
        <span class="title-leaf title-leaf-gray">
            <svg class="icon-width bg-gray">
                <use xlink:href="{{ asset('assets/svg/leaf.svg') }}#leaf"></use>
            </svg>
        </span>
    </div>

    @if ($errors->any() && session('tab') === 'orders')
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="order-contain">
        @forelse ($pedidos as $pedido)
            @php
                $esGestionable = app(\App\Services\PedidoService::class)->esGestionablePorCliente($pedido);
                $lineas = $pedido->detalle ?? collect();
                $subtotal = $lineas->sum(fn ($linea) => (float) $linea->DetaPed_SubTotal);
                $total = (float) $pedido->Ped_TotalPrecio;
                $envio = max(0, $total - $subtotal);
                $estatusNombre = $pedido->estatus?->Nom_Estatus ?? 'Sin estatus';
                $estatusId = (int) $pedido->Id_Estatus;
                $badgeClass = match ($estatusId) {
                    EstatusCatalog::PEDIDO_ENTREGADO => 'success-bg',
                    EstatusCatalog::PEDIDO_CANCELADO => 'danger',
                    default => '',
                };
                $metodoPago = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? 'No disponible';
                $direccionTexto = $pedido->envio?->Direccion_Envio
                    ?? collect([
                        $pedido->direccion?->Direccion,
                        $pedido->direccion?->municipio?->Nom_Municipio,
                        $pedido->direccion?->municipio?->departamento?->Nom_Departamento,
                    ])->filter()->implode(', ');
            @endphp

            <div class="order-box dashboard-bg-box dashboard-order-item">
                <div class="order-container dashboard-order-header">
                    <div class="order-icon flex-shrink-0">
                        <i data-feather="box"></i>
                    </div>

                    <div class="order-detail flex-grow-1 min-w-0">
                        <h4 class="dashboard-order-title">
                            <span class="dashboard-order-number">{{ $pedido->Ped_Numero }}</span>
                            <span @if ($badgeClass !== '') class="dashboard-order-badge {{ $badgeClass }}" @else class="dashboard-order-badge" @endif>{{ $estatusNombre }}</span>
                        </h4>
                        <h6 class="text-content mb-1 dashboard-order-meta">
                            {{ $pedido->created_at?->format('d/m/Y H:i') ?? '' }}
                            · {{ $metodoPago }}
                            · Total: Q {{ number_format($total, 2) }}
                        </h6>
                        @if ($direccionTexto !== '')
                            <h6 class="text-content mb-0 dashboard-order-address">
                                <i data-feather="map-pin"></i>
                                <span>{{ $direccionTexto }}</span>
                            </h6>
                        @endif
                    </div>

                    @if ($esGestionable)
                        <div class="dashboard-order-actions">
                            <button type="button"
                                    class="btn btn-sm theme-bg-color text-white"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editar-pedido-{{ $pedido->Id_Pedido }}">
                                <i data-feather="edit-2"></i> Editar
                            </button>
                            <form method="POST"
                                  action="{{ route('dashboard.pedidos.cancel', $pedido) }}"
                                  onsubmit="return confirm('¿Cancelar este pedido? Los productos volverán a tu carrito.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Cancelar
                                </button>
                            </form>
                        </div>
                    @elseif ($estatusId === EstatusCatalog::PEDIDO_PENDIENTE)
                        <p class="text-content small mb-0 dashboard-order-note">
                            Este pedido ya no puede editarse desde el panel.
                        </p>
                    @endif
                </div>

                @foreach ($lineas as $linea)
                    @php
                        $producto = $linea->producto;
                        $imagen = $producto?->imagenes?->sortBy('orden')->first();
                        $imagenUrl = $imagen
                            ? asset($imagen->url)
                            : asset('storage/products/default.png');
                        $productoUrl = $producto
                            ? route('product.details', [
                                'idproducto' => $producto->Id_Producto,
                                'slug_producto' => $producto->Prod_Slug,
                            ])
                            : '#';
                    @endphp

                    <div class="product-order-detail dashboard-order-product">
                        <a href="{{ $productoUrl }}" class="order-image dashboard-order-thumb">
                            <img src="{{ $imagenUrl }}" class="blur-up lazyload dashboard-order-thumb-img" alt="{{ $producto?->Prod_Nombre ?? 'Producto' }}">
                        </a>

                        <div class="order-wrap dashboard-order-product-info">
                            <a href="{{ $productoUrl }}">
                                <h3>{{ $producto?->Prod_Nombre ?? 'Producto' }}</h3>
                            </a>
                            <ul class="product-size dashboard-order-product-meta">
                                <li>
                                    <div class="size-box">
                                        <h6 class="text-content">Precio :</h6>
                                        <h5>Q {{ number_format((float) $linea->DetaPed_Precio, 2) }}</h5>
                                    </div>
                                </li>
                                <li>
                                    <div class="size-box">
                                        <h6 class="text-content">Cantidad :</h6>
                                        <h5>{{ (int) $linea->DetaPed_Cantidad }}</h5>
                                    </div>
                                </li>
                                <li>
                                    <div class="size-box">
                                        <h6 class="text-content">Subtotal :</h6>
                                        <h5>Q {{ number_format((float) $linea->DetaPed_SubTotal, 2) }}</h5>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endforeach

                <div class="dashboard-order-summary">
                    <p class="text-content mb-0">
                        <strong>Subtotal:</strong> Q {{ number_format($subtotal, 2) }}
                        · <strong>Envío:</strong> Q {{ number_format($envio, 2) }}
                        · <strong>Total:</strong> Q {{ number_format($total, 2) }}
                    </p>
                </div>
            </div>

            @if ($esGestionable)
                <div class="modal fade" id="editar-pedido-{{ $pedido->Id_Pedido }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('dashboard.pedidos.update', $pedido) }}">
                                @csrf
                                @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar pedido {{ $pedido->Ped_Numero }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-content">
                                        Solo puedes modificar pedidos pendientes de confirmación.
                                        Para eliminar un producto, coloca cantidad 0.
                                    </p>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold" for="direccion-{{ $pedido->Id_Pedido }}">Dirección de envío</label>
                                        <select class="form-select"
                                                id="direccion-{{ $pedido->Id_Pedido }}"
                                                name="id_direccion"
                                                required>
                                            @forelse ($direcciones as $direccion)
                                                @php
                                                    $textoDir = collect([
                                                        $direccion->Direccion,
                                                        $direccion->municipio?->Nom_Municipio,
                                                        $direccion->municipio?->departamento?->Nom_Departamento,
                                                    ])->filter()->implode(', ');
                                                @endphp
                                                <option value="{{ $direccion->Id_Direccion }}"
                                                    @selected((int) $pedido->Id_Direccion === (int) $direccion->Id_Direccion)>
                                                    {{ $textoDir }}
                                                </option>
                                            @empty
                                                <option value="" disabled selected>No tienes direcciones registradas</option>
                                            @endforelse
                                        </select>
                                        @if ($direcciones->isEmpty())
                                            <p class="text-danger small mt-2 mb-0">
                                                Agrega una dirección en la pestaña Direcciones antes de editar el pedido.
                                            </p>
                                        @endif
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th style="width:120px;">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lineas as $index => $linea)
                                                    <tr>
                                                        <td>
                                                            {{ $linea->producto?->Prod_Nombre ?? 'Producto' }}
                                                            <input type="hidden"
                                                                   name="items[{{ $index }}][id_detalle]"
                                                                   value="{{ $linea->Id_DetallePedido }}">
                                                        </td>
                                                        <td>
                                                            <input type="number"
                                                                   class="form-control"
                                                                   name="items[{{ $index }}][cantidad]"
                                                                   min="0"
                                                                   max="999"
                                                                   value="{{ (int) $linea->DetaPed_Cantidad }}"
                                                                   required>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit"
                                            class="btn theme-bg-color text-white btn-sm"
                                            @disabled($direcciones->isEmpty())>
                                        Guardar cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="dashboard-bg-box p-4 text-center">
                <p class="text-content mb-3">Aún no tienes pedidos registrados.</p>
                <a href="{{ route('shop.index') }}" class="btn theme-bg-color text-white btn-sm">
                    Ir a la tienda
                </a>
            </div>
        @endforelse
    </div>
</div>
