@extends('layouts.appadmin')

@section('title', 'Agregar marca')

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

    @include('admin.marcas._form', [
        'marca' => null,
        'formAction' => route('admin.marcas.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Guardar marca',
    ])
@endsection
