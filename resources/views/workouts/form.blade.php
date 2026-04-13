@extends('layouts.app')
@section('title', isset($workout) ? 'Editar Rutina' : 'Nueva Rutina')
@section('header-actions')
    <a href="{{ route('workouts.index') }}" class="btn btn-ghost">← Volver</a>
@endsection
@section('content')
<div style="max-width:560px">
    <div class="card">
        <form method="POST" action="{{ isset($workout) ? route('workouts.update', $workout) : route('workouts.store') }}">
            @csrf
            @if(isset($workout)) @method('PUT') @endif

            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif

            <div class="form-group">
                <label>Título</label>
                <input type="text" name="title"
                    value="{{ old('title', isset($workout) ? $workout->title : '') }}"
                    required placeholder="Full Body Strength, HIIT 30min...">
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="description" rows="3"
                    placeholder="Describe los ejercicios, series, repeticiones...">{{ old('description', isset($workout) ? $workout->description : '') }}</textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Cliente</label>
                    <select name="client_id" required>
                        <option value="">Seleccionar...</option>
                        @foreach($clients as $c)
                        <option value="{{ $c->id }}"
                            {{ old('client_id', isset($workout) ? $workout->client_id : request('client_id')) == $c->id ? 'selected' : '' }}>
                            {{ $c->user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Entrenador</label>
                    @if(auth()->user()->isTrainer())
                        <input type="hidden" name="trainer_id" value="{{ auth()->user()->trainer->id }}">
                        <input type="text" value="{{ auth()->user()->name }}" disabled style="opacity:.5">
                    @else
                        <select name="trainer_id" required>
                            <option value="">Seleccionar...</option>
                            @foreach($trainers as $t)
                            <option value="{{ $t->id }}"
                                {{ old('trainer_id', isset($workout) ? $workout->trainer_id : '') == $t->id ? 'selected' : '' }}>
                                {{ $t->user->name }}
                            </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="form-group">
                    <label>Fecha programada</label>
                    <input type="date" name="scheduled_date"
                        value="{{ old('scheduled_date', isset($workout) ? optional($workout->scheduled_date)->format('Y-m-d') : '') }}">
                </div>

                @if(isset($workout))
                <div class="form-group">
                    <label>Estado</label>
                    <select name="status">
                        <option value="pending"   {{ $workout->status === 'pending'   ? 'selected' : '' }}>Pendiente</option>
                        <option value="completed" {{ $workout->status === 'completed' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelled" {{ $workout->status === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                @endif
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('workouts.index') }}" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($workout) ? 'Guardar cambios' : 'Crear rutina' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection