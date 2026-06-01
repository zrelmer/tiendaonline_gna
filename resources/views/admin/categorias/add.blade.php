@extends('layouts.appadmin')

@section('title', 'Agregar categoría')

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

    @include('admin.categorias._form', [
        'categoria' => null,
        'formAction' => route('admin.categorias.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Guardar categoría',
    ])
@endsection
