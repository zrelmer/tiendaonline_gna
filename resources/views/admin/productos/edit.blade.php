@extends('layouts.appadmin')

@section('title', 'Editar producto')

@section('content')
    <div class="row">
        <div class="col-12">
            <p class="text-muted">Vista de edición pendiente (producto #{{ $producto->Id_Producto }}).</p>
        </div>
    </div>
@endsection
