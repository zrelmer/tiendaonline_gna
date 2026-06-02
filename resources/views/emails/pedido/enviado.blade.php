@extends('emails.base')

@section('title', 'Pedido enviado')

@section('content')
@php
    $envio = $pedido->envio;
    $empresaEnvio = $envio?->Empresa_Envio ?? '—';
    $numeroGuia = $envio?->Numero_Guia ?? '—';
    $fechaEnvio = $envio?->Fecha_Envio
        ? \Illuminate\Support\Carbon::parse($envio->Fecha_Envio)->format('d/m/Y H:i')
        : now()->format('d/m/Y H:i');
    $urlSeguimiento = url('/dashboard');
@endphp

<h2 style="color:#333; margin-top:0;">
    Hola {{ $pedido->usuario?->Usu_Nombre ?? 'cliente' }},
</h2>

<p style="color:#555;">
    Tu pedido <strong>{{ $pedido->Ped_Numero }}</strong> ya fue enviado y está en camino.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 18px 0; border:1px solid #e5e5e5; border-radius:8px; overflow:hidden;">
    <tr>
        <td style="padding:14px 16px; background:#f6f6f6; border-bottom:1px solid #e5e5e5;">
            <strong style="color:#333;">Información de envío</strong>
        </td>
    </tr>
    <tr>
        <td style="padding:16px;">
            <p style="color:#555; margin:0 0 8px 0;"><strong>Transportista:</strong> {{ $empresaEnvio }}</p>
            <p style="color:#555; margin:0 0 8px 0;"><strong>N.º de guía:</strong> {{ $numeroGuia }}</p>
            <p style="color:#555; margin:0;"><strong>Fecha de envío:</strong> {{ $fechaEnvio }}</p>
        </td>
    </tr>
</table>

<p style="color:#555;">
    Puedes consultar el avance completo en tu panel, pestaña <strong>Seguimiento de órdenes</strong>:
</p>

<p style="margin: 0 0 18px 0;">
    <a href="{{ $urlSeguimiento }}"
       style="display:inline-block; background:#ff3c00; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:6px; font-weight:bold;">
        Ver seguimiento
    </a>
</p>

<p style="color:#777; font-size:14px; margin:0;">
    Gracias por tu compra en GNA Core.
</p>

@endsection
