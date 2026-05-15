@extends('emails.base')

@section('title', 'Bienvenido a GNA Core')

@section('content')

<h2 style="color:#333;">¡Hola {{ $usuario->Usu_Nombre }}! </h2>

<p style="color:#555;">
    Te damos la bienvenida a <strong>GNA Core</strong>.
</p>

<p style="color:#555;">
    Tu cuenta ha sido creada exitosamente. Ahora puedes explorar nuestra tienda,
    agregar productos al carrito y realizar tus compras de forma segura.
</p>

{{--  <div style="text-align:center; margin:30px 0;">
    <a href="{{ url('/') }}" style="
        background:#ff3c00;
        color:#ffffff;
        padding:12px 25px;
        text-decoration:none;
        border-radius:5px;
        font-weight:bold;
    ">
        Ir a la tienda
    </a>
</div>  --}}

<p style="color:#777;">
    Si no creaste esta cuenta, puedes ignorar este mensaje.
</p>

<p style="color:#333;">
    ¡Gracias por confiar en nosotros! Esperamos que disfrutes de tu experiencia de compra en GNA Core.
</p>

@endsection
