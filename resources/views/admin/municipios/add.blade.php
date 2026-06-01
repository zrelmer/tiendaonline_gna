@extends('layouts.appadmin')

@section('title', 'Agregar municipio')

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

    @include('admin.municipios._form', [
        'municipio' => null,
        'departamentos' => $departamentos,
        'formAction' => route('admin.municipios.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Guardar municipio',
    ])
@endsection
