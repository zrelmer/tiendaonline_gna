@php
    use App\Support\EstatusCatalog;

    $estatusId = (int) $pedido->Id_Estatus;
    $avisarReposicion = in_array($estatusId, [
        EstatusCatalog::PEDIDO_CONFIRMADO,
        EstatusCatalog::PEDIDO_EN_PREPARACION,
        EstatusCatalog::PEDIDO_ENVIADO,
    ], true);
    $mensajeConfirm = $avisarReposicion
        ? '¿Cancelar este pedido? Se repone el stock en inventario si ya se había descontado.'
        : '¿Cancelar este pedido?';
@endphp

@if ($puedeCancelar ?? false)
    <div class="card mt-4 border-danger">
        <div class="card-body">
            <div class="card-header-2 mb-3">
                <h5 class="text-danger">Cancelar pedido</h5>
            </div>
            <p class="text-muted small mb-3">
                El pedido pasará a estatus <strong>Cancelado</strong>.
                @if ($avisarReposicion)
                    Si el stock ya fue descontado al confirmar, se registrará una devolución en inventario.
                @endif
            </p>
            <form method="POST"
                  action="{{ route('admin.pedidos.cancelar', $pedido) }}"
                  onsubmit="return confirm(@json($mensajeConfirm));">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="comentario-cancelar-pedido-{{ $pedido->Id_Pedido }}">
                        Comentario para el historial (opcional)
                    </label>
                    <textarea class="form-control"
                              id="comentario-cancelar-pedido-{{ $pedido->Id_Pedido }}"
                              name="comentario"
                              rows="2"
                              maxlength="500"
                              placeholder="Ej.: Cancelado a solicitud del cliente.">{{ old('comentario') }}</textarea>
                </div>
                <button type="submit" class="btn btn-outline-danger">
                    <i class="ri-close-circle-line me-1"></i>
                    Cancelar pedido
                </button>
            </form>
        </div>
    </div>
@endif
