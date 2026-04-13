@extends('layouts.app')
@section('title', isset($trainer) ? 'Editar Entrenador' : 'Nuevo Entrenador')
@section('header-actions')
    <a href="{{ route('trainers.index') }}" class="btn btn-ghost">← Volver</a>
@endsection
@section('content')
<div style="max-width:560px">
    <div class="card">
        <form method="POST" action="{{ isset($trainer) ? route('trainers.update', $trainer) : route('trainers.store') }}">
            @csrf
            @if(isset($trainer)) @method('PUT') @endif

            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif

            <div class="form-group">
                <label>Nombre completo</label>
                <input type="text" name="name"
                    value="{{ old('name', isset($trainer) ? $trainer->user->name : '') }}"
                    required
                    {{ isset($trainer) ? 'readonly style="opacity:.5"' : '' }}>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                    value="{{ old('email', isset($trainer) ? $trainer->user->email : '') }}"
                    required
                    {{ isset($trainer) ? 'readonly style="opacity:.5"' : '' }}>
            </div>
            @if(!isset($trainer))
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required minlength="8">
            </div>
            @endif
            <div class="form-group">
                <label>Especialidad</label>
                <input type="text" name="speciality"
                    value="{{ old('speciality', isset($trainer) ? $trainer->speciality : '') }}"
                    placeholder="Musculación, CrossFit, Cardio..." required>
            </div>
            <div class="form-group">
                <label>Biografía</label>
                <textarea name="bio" rows="4"
                    placeholder="Describe la experiencia del entrenador...">{{ old('bio', isset($trainer) ? $trainer->bio : '') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('trainers.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($trainer) ? 'Guardar cambios' : 'Crear entrenador' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection