@extends('layouts.app')
@section('title', isset($membership) ? 'Editar Membresía' : 'Nueva Membresía')
@section('header-actions')
    <a href="{{ route('memberships.index') }}" class="btn btn-ghost">← Volver</a>
@endsection
@section('content')
<div style="max-width:480px">
    <div class="card">
        <form method="POST" action="{{ isset($membership) ? route('memberships.update', $membership) : route('memberships.store') }}">
            @csrf
            @if(isset($membership)) @method('PUT') @endif
            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" value="{{ old('name', $membership->name ?? '') }}" required placeholder="Básica, Premium, VIP...">
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Precio (€/mes)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $membership->price ?? '') }}" required placeholder="29.99">
                </div>
                <div class="form-group">
                    <label>Duración (días)</label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', $membership->duration_days ?? 30) }}" required placeholder="30">
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="description" rows="3" placeholder="Qué incluye esta membresía...">{{ old('description', $membership->description ?? '') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('memberships.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">{{ isset($membership) ? 'Guardar cambios' : 'Crear membresía' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
