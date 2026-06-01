@extends('layouts.appadmin')

@section('title', 'Editar municipio')

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

    @include('admin.municipios._form', [
        'municipio' => $municipio,
        'departamentos' => $departamentos,
        'formAction' => route('admin.municipios.update', $municipio),
        'formMethod' => 'PUT',
        'submitLabel' => 'Actualizar municipio',
    ])
@endsection
