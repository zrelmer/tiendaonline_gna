<div class="row g-2">
    <div class="col-12">
        <div class="payment-method">
            <div class="form-floating mb-lg-3 mb-2 theme-form-floating">
                <input type="text" class="form-control" id="credit-{{ $metodo->Id_MetodoPago }}" placeholder="Número de tarjeta" disabled>
                <label for="credit-{{ $metodo->Id_MetodoPago }}">Número de tarjeta</label>
            </div>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="form-floating mb-lg-3 mb-2 theme-form-floating">
            <input type="text" class="form-control" id="expiry-{{ $metodo->Id_MetodoPago }}" placeholder="MM/AA" disabled>
            <label for="expiry-{{ $metodo->Id_MetodoPago }}">Fecha de expiración</label>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="form-floating mb-lg-3 mb-2 theme-form-floating">
            <input type="text" class="form-control" id="cvv-{{ $metodo->Id_MetodoPago }}" placeholder="CVV" disabled>
            <label for="cvv-{{ $metodo->Id_MetodoPago }}">CVV Código de seguridad</label>
        </div>
    </div>

    <div class="button-group mt-0">
        <ul>
            <li>
                <button type="button" class="btn btn-light shopping-button" disabled>Cancelar</button>
            </li>
            <li>
                <button type="button" class="btn btn-animation" disabled>Usar esta tarjeta</button>
            </li>
        </ul>
    </div>
</div>
