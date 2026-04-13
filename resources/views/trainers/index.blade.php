@extends('layouts.app')
@section('title', 'Entrenadores')

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('trainers.create') }}" class="btn btn-primary">+ Nuevo Entrenador</a>
    @endif
@endsection

@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px">
    @forelse($trainers as $trainer)
    <div class="card" style="position:relative;overflow:hidden">
        <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:#C8FF0010;filter:blur(30px)"></div>
        <div style="display:flex;gap:14px;margin-bottom:18px;align-items:flex-start">
            <div class="avatar" style="width:52px;height:52px;font-size:16px;background:#C8FF0022">
                {{ strtoupper(substr($trainer->user->name, 0, 2)) }}
            </div>
            <div style="flex:1">
                <div style="font-family:'Syne',sans-serif;font-size:17px;font-weight:800">{{ $trainer->user->name }}</div>
                <div style="color:var(--accent);font-size:13px;font-weight:600;margin-top:2px">{{ $trainer->speciality }}</div>
            </div>
        </div>
        <p style="color:var(--dim);font-size:13px;line-height:1.6;margin-bottom:18px">{{ $trainer->bio }}</p>
        <div style="display:flex;gap:10px">
            <div style="flex:1;background:var(--bg);border-radius:10px;padding:12px;text-align:center">
                <div style="font-family:'Syne',sans-serif;font-size:24px;font-weight:900;color:var(--accent)">{{ $trainer->workouts_count }}</div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Rutinas</div>
            </div>
        </div>
        @if(auth()->user()->isAdmin())
        <div style="display:flex;gap:8px;margin-top:14px">
            <a href="{{ route('trainers.edit', $trainer) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center">✏️ Editar</a>
            <form method="POST" action="{{ route('trainers.destroy', $trainer) }}" onsubmit="return confirm('¿Eliminar entrenador?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">🗑</button>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div style="color:var(--muted)">No hay entrenadores registrados.</div>
    @endforelse
</div>
@endsection
