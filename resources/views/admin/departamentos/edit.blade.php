@extends('layouts.appadmin')

@section('title', 'Editar departamento')

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

    @include('admin.departamentos._form', [
        'departamento' => $departamento,
        'formAction' => route('admin.departamentos.update', $departamento),
        'formMethod' => 'PUT',
        'submitLabel' => 'Actualizar departamento',
    ])
@endsection
