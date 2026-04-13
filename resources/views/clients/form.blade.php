@extends('layouts.app')
@section('title', isset($client) ? 'Editar Cliente' : 'Nuevo Cliente')
 
@section('header-actions')
    <a href="{{ route('clients.index') }}" class="btn btn-ghost">← Volver</a>
@endsection
 
@section('content')
<div style="max-width:640px">
    <div class="card">
        <form method="POST" action="{{ isset($client) ? route('clients.update', $client) : route('clients.store') }}">
            @csrf
            @if(isset($client)) @method('PUT') @endif
 
            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px">
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
            @endif
 
            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" name="name"
                        value="{{ old('name', isset($client) ? $client->user->name : '') }}"
                        required
                        {{ isset($client) ? 'readonly style="opacity:.5"' : '' }}>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                        value="{{ old('email', isset($client) ? $client->user->email : '') }}"
                        required
                        {{ isset($client) ? 'readonly style="opacity:.5"' : '' }}>
                </div>
            </div>
 
            @if(!isset($client))
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required minlength="8">
            </div>
            @endif
 
            <div class="form-grid">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', isset($client) ? $client->phone : '') }}"
                        placeholder="600000000">
                </div>
                <div class="form-group">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="birth_date"
                        value="{{ old('birth_date', isset($client) ? optional($client->birth_date)->format('Y-m-d') : '') }}">
                </div>
                <div class="form-group">
                    <label>Peso (kg)</label>
                    <input type="number" step="0.1" name="weight"
                        value="{{ old('weight', isset($client) ? $client->weight : '') }}"
                        placeholder="70">
                </div>
                <div class="form-group">
                    <label>Altura (cm)</label>
                    <input type="number" step="0.1" name="height"
                        value="{{ old('height', isset($client) ? $client->height : '') }}"
                        placeholder="175">
                </div>
            </div>
 
            <div class="form-group">
                <label>Membresía</label>
                <select name="membership_id">
                    <option value="">Sin membresía</option>
                    @foreach($memberships as $m)
                    <option value="{{ $m->id }}"
                        {{ old('membership_id', isset($client) ? $client->membership_id : '') == $m->id ? 'selected' : '' }}>
                        {{ $m->name }} – €{{ $m->price }}/mes ({{ $m->duration_days }} días)
                    </option>
                    @endforeach
                </select>
            </div>
 
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                <a href="{{ route('clients.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($client) ? 'Guardar cambios' : 'Crear cliente' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection