<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f4; font-family: Arial, sans-serif;">

    <div style="max-width:600px; margin:auto; background:#ffffff; border-radius:10px; overflow:hidden;">

        <!-- HEADER -->
        <div style="background:#ff3c00; padding:20px; text-align:center;">
            {{--  <img src="{{ asset('assets/images/logo/3.png') }}" width="120" alt="GNA Core">  --}}
        </div>

        <!-- CONTENIDO -->
        <div style="padding:30px;">
            @yield('content')
        </div>

        <!-- FOOTER -->
        <div style="background:#f9f9f9; padding:15px; text-align:center; font-size:12px; color:#777;">
            © {{ date('Y') }} GNA Core - Todos los derechos reservados
        </div>

    </div>

</body>
</html>
