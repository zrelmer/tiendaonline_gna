@extends('emails.base')

@section('title', 'Resumen semanal de inventario')

@section('content')
<h2 style="color:#333; margin-top:0;">Resumen semanal de inventario</h2>

<p style="color:#555;">
    Productos con stock bajo (≤ {{ $umbral }} unidades disponibles) o sin stock al {{ $fecha }}.
</p>

@if($productosBajoStock->isNotEmpty())
    <h3 style="color:#333; margin-bottom:8px;">Stock bajo ({{ $productosBajoStock->count() }})</h3>
    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin-bottom:20px;">
        <thead>
            <tr style="background:#f6f6f6; border:1px solid #e5e5e5;">
                <th align="left" style="border:1px solid #e5e5e5;">Producto</th>
                <th align="center" style="border:1px solid #e5e5e5;">Disponible</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productosBajoStock as $producto)
                @php
                    $inv = $producto->inventario;
                    $disponible = $inv ? max(0, (int) $inv->Stock - (int) $inv->Stock_Reservado) : 0;
                @endphp
                <tr style="border:1px solid #e5e5e5;">
                    <td style="border:1px solid #e5e5e5;">{{ $producto->Prod_Nombre }}</td>
                    <td align="center" style="border:1px solid #e5e5e5;">{{ $disponible }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if($productosSinStock->isNotEmpty())
    <h3 style="color:#333; margin-bottom:8px;">Sin stock ({{ $productosSinStock->count() }})</h3>
    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin-bottom:20px;">
        <thead>
            <tr style="background:#f6f6f6; border:1px solid #e5e5e5;">
                <th align="left" style="border:1px solid #e5e5e5;">Producto</th>
                <th align="center" style="border:1px solid #e5e5e5;">Disponible</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productosSinStock as $producto)
                @php
                    $inv = $producto->inventario;
                    $disponible = $inv ? max(0, (int) $inv->Stock - (int) $inv->Stock_Reservado) : 0;
                @endphp
                <tr style="border:1px solid #e5e5e5;">
                    <td style="border:1px solid #e5e5e5;">{{ $producto->Prod_Nombre }}</td>
                    <td align="center" style="border:1px solid #e5e5e5;">{{ $disponible }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if($productosBajoStock->isEmpty() && $productosSinStock->isEmpty())
    <p style="color:#555;">No hay productos en alerta de inventario esta semana.</p>
@endif

<p style="margin-top:24px;">
    <a href="{{ $accionUrl }}" style="display:inline-block; background:#ff3c00; color:#fff; text-decoration:none; padding:12px 20px; border-radius:6px;">
        Ver inventario en panel admin
    </a>
</p>

<p style="color:#888; font-size:12px; margin-top:24px;">
    Resumen automático semanal para el equipo administrativo de GNA Core.
</p>
@endsection
