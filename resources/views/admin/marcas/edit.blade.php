@extends('layouts.appadmin')

@section('title', 'Editar marca')

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

    @include('admin.marcas._form', [
        'marca' => $marca,
        'formAction' => route('admin.marcas.update', $marca),
        'formMethod' => 'PUT',
        'submitLabel' => 'Actualizar marca',
    ])
@endsection
