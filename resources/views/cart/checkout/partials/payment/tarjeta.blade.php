<div class="row g-3">
    <div class="col-12">
        <div class="form-floating theme-form-floating">
            <input type="text" class="form-control" id="cc-name" placeholder="Nombre como aparece en la tarjeta">
            <label for="cc-name">Nombre en la tarjeta</label>
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating theme-form-floating">
            <input type="email" class="form-control" id="cc-email" value="{{ auth()->user()->email ?? '' }}" placeholder="nombre@ejemplo.com">
            <label for="cc-email">Correo Electrónico</label>
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating theme-form-floating">
            <input type="text" class="form-control" id="cc-number" placeholder="0000 0000 0000 0000" maxlength="19" autocomplete="cc-number">
            <label for="cc-number">Número de Tarjeta</label>
        </div>
    </div>

    <div class="col-sm-4 col-6">
        <div class="form-floating theme-form-floating">
            <input type="text" class="form-control" id="cc-expiry-month" placeholder="MM" maxlength="2">
            <label for="cc-expiry-month">Mes (MM)</label>
        </div>
    </div>

    <div class="col-sm-4 col-6">
        <div class="form-floating theme-form-floating">
            <input type="text" class="form-control" id="cc-expiry-year" placeholder="AA" maxlength="2">
            <label for="cc-expiry-year">Año (AA)</label>
        </div>
    </div>

    <div class="col-sm-4 col-12">
        <div class="form-floating theme-form-floating">
            <input type="password" class="form-control" id="cc-cvv" placeholder="CVV" maxlength="4" autocomplete="cc-csc">
            <label for="cc-cvv">CVV</label>
        </div>
    </div>

    <input type="hidden" name="recurrente_card_token" id="recurrente_card_token">
</div>

<div id="card-errors" class="text-danger small mt-2 d-none" role="alert"></div>