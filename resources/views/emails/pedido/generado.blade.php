@extends('emails.base')

@section('title', $pagoConfirmado ? 'Pago confirmado' : 'Pedido generado')

@section('content')
@php
    $pago = $pedido->pago;
    $metodoPago = $pago?->metodoPago?->MetPag_Descripcion ?? 'No disponible';
    $lineas = $pedido->detalle ?? collect();
    $subtotal = $lineas->sum(fn ($linea) => (float) $linea->DetaPed_SubTotal);
    $total = (float) $pedido->Ped_TotalPrecio;
    $envio = max(0, $total - $subtotal);
@endphp

<h2 style="color:#333; margin-top:0;">
    Hola {{ $pedido->usuario?->Usu_Nombre ?? 'cliente' }},
</h2>

<p style="color:#555;">
    @if($pagoConfirmado)
        Tu pago para el pedido <strong>{{ $pedido->Ped_Numero }}</strong> fue confirmado.
    @else
        Hemos registrado tu pedido <strong>{{ $pedido->Ped_Numero }}</strong>.
    @endif
</p>

<p style="color:#555; margin-bottom:8px;"><strong>Método de pago:</strong> {{ $metodoPago }}</p>

@if(!empty($pago?->Transaccion_Id))
    <p style="color:#555; margin-top:0; margin-bottom:16px;">
        <strong>ID de transacción:</strong> {{ $pago->Transaccion_Id }}
    </p>
@endif

<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin: 10px 0 18px 0;">
    <thead>
        <tr style="background:#f6f6f6; border:1px solid #e5e5e5;">
            <th align="left" style="border:1px solid #e5e5e5;">Producto</th>
            <th align="center" style="border:1px solid #e5e5e5;">Cantidad</th>
            <th align="right" style="border:1px solid #e5e5e5;">Precio unitario</th>
            <th align="right" style="border:1px solid #e5e5e5;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lineas as $linea)
            <tr style="border:1px solid #e5e5e5;">
                <td style="border:1px solid #e5e5e5;">{{ $linea->producto?->Prod_Nombre ?? 'Producto' }}</td>
                <td align="center" style="border:1px solid #e5e5e5;">{{ (int) $linea->DetaPed_Cantidad }}</td>
                <td align="right" style="border:1px solid #e5e5e5;">Q {{ number_format((float) $linea->DetaPed_Precio, 2) }}</td>
                <td align="right" style="border:1px solid #e5e5e5;">Q {{ number_format((float) $linea->DetaPed_SubTotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="color:#555; margin:0 0 4px 0;"><strong>Subtotal:</strong> Q {{ number_format($subtotal, 2) }}</p>
<p style="color:#555; margin:0 0 4px 0;"><strong>Envío:</strong> Q {{ number_format($envio, 2) }}</p>
<p style="color:#333; margin:0;"><strong>Total:</strong> Q {{ number_format($total, 2) }}</p>

@endsection

