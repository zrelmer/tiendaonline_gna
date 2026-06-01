@extends('layouts.appadmin')

@section('title', 'Editar rol de usuario')

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

    @include('admin.usuarios._form', [
        'usuario' => $usuario,
        'roles' => $roles,
        'formAction' => route('admin.usuarios.update', $usuario),
        'formMethod' => 'PUT',
        'submitLabel' => 'Actualizar rol',
    ])
@endsection
