@extends('layouts.appadmin')

@section('title', 'Agregar departamento')

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

    @include('admin.departamentos._form', [
        'departamento' => null,
        'formAction' => route('admin.departamentos.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Guardar departamento',
    ])
@endsection
