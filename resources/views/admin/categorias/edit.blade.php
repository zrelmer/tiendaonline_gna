@extends('layouts.appadmin')

@section('title', 'Editar categoría')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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
        'categoria' => $categoria,
        'formAction' => route('admin.categorias.update', $categoria),
        'formMethod' => 'PUT',
        'submitLabel' => 'Actualizar categoría',
    ])
@endsection