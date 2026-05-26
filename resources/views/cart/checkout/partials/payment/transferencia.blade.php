<p class="cod-review">
    <b>Instrucciones para depositar</b><br>
    Tienes 24 horas para realizar tu pago y enviar comprobante, de lo contrario no podemos asegurar la disponibilidad del producto.<br>
    <b>Cuentas Ahorros Karla Noemy Capriel Hernandez:</b><br>
    Banco Industrial:<br>
    Banrural:<br>
    G&T:<br>
</p>

@php
    $pedidosPendientesBoleta = $pedidosPendientesBoleta ?? collect();
@endphp

@if ($pedidosPendientesBoleta->isEmpty())
    <p class="text-content mt-3 mb-0">
        Cuando confirmes tu pedido con transferencia bancaria, podrás subir aquí tu comprobante
        (PNG, JPG o PDF, máximo 10 MB).
    </p>
@else
    <div class="mt-3">
        @if ($pedidosPendientesBoleta->count() === 1)
            <input type="hidden"
                   form="form-boleta-pago"
                   name="id_pedido"
                   value="{{ $pedidosPendientesBoleta->first()->Id_Pedido }}">
            <p class="text-content mb-2">
                <span class="text-title">Pedido:</span>
                #{{ $pedidosPendientesBoleta->first()->Ped_Numero ?? $pedidosPendientesBoleta->first()->Id_Pedido }}
            </p>
        @else
            <div class="form-floating mb-3 theme-form-floating">
                <select class="form-select @error('id_pedido') is-invalid @enderror"
                        id="id_pedido_{{ $metodo->Id_MetodoPago }}"
                        name="id_pedido"
                        form="form-boleta-pago"
                        required>
                    <option value="">Selecciona un pedido</option>
                    @foreach ($pedidosPendientesBoleta as $pedido)
                        <option value="{{ $pedido->Id_Pedido }}" @selected(old('id_pedido') == $pedido->Id_Pedido)>
                            Pedido #{{ $pedido->Ped_Numero ?? $pedido->Id_Pedido }}
                            — Q{{ number_format((float) $pedido->Ped_TotalPrecio, 2) }}
                        </option>
                    @endforeach
                </select>
                <label for="id_pedido_{{ $metodo->Id_MetodoPago }}">Pedido a pagar</label>
                @error('id_pedido')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <div class="mb-3">
            <label for="boleta_{{ $metodo->Id_MetodoPago }}" class="form-label">
                Comprobante de transferencia
            </label>
            <input type="file"
                   class="form-control @error('boleta') is-invalid @enderror"
                   id="boleta_{{ $metodo->Id_MetodoPago }}"
                   name="boleta"
                   form="form-boleta-pago"
                   accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf"
                   required>
            <div class="form-text">
                Formatos permitidos: PNG, JPG, PDF. Tamaño máximo: 10 MB.
            </div>
            @error('boleta')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" form="form-boleta-pago" class="btn btn-animation">
            Subir comprobante
        </button>
    </div>
@endif
