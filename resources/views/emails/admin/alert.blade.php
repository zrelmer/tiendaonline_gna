@extends('emails.base')

@section('title', $titulo)

@section('content')
<h2 style="color:#333; margin-top:0;">{{ $titulo }}</h2>

<p style="color:#555;">{{ $mensaje }}</p>

@if(!empty($detalles))
    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin: 16px 0;">
        @foreach($detalles as $etiqueta => $valor)
            <tr style="border:1px solid #e5e5e5;">
                <td style="border:1px solid #e5e5e5; background:#f6f6f6; width:38%;"><strong>{{ $etiqueta }}</strong></td>
                <td style="border:1px solid #e5e5e5;">{{ $valor }}</td>
            </tr>
        @endforeach
    </table>
@endif

@if(!empty($accionUrl))
    <p style="margin-top:24px;">
        <a href="{{ $accionUrl }}" style="display:inline-block; background:#ff3c00; color:#fff; text-decoration:none; padding:12px 20px; border-radius:6px;">
            {{ $accionTexto ?? 'Ver en panel admin' }}
        </a>
    </p>
@endif

<p style="color:#888; font-size:12px; margin-top:24px;">
    Este mensaje es interno para el equipo administrativo de GNA Core.
</p>
@endsection
