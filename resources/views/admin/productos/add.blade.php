@extends('layouts.appadmin')

@section('title', 'Agregar producto')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('admin.productos._form', [
        'producto' => null,
        'categorias' => $categorias,
        'marcas' => $marcas,
        'estatusProducto' => $estatusProducto,
        'formAction' => route('admin.productos.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Guardar producto',
    ])
@endsection
