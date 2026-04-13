@extends('layouts.app')
@section('title', 'Rutinas')
@section('header-actions')
    @if(auth()->user()->isAdmin() || auth()->user()->isTrainer())
    <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Nueva Rutina</a>
    @endif
@endsection
@section('content')

<form method="GET" style="display:flex;gap:10px;margin-bottom:24px">
    <select name="status" style="width:auto">
        <option value="">Todos los estados</option>
        <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pendientes</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completadas</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
    </select>
    <button type="submit" class="btn btn-ghost">Filtrar</button>
    @if(request('status'))
        <a href="{{ route('workouts.index') }}" class="btn btn-ghost">✕ Limpiar</a>
    @endif
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Rutina</th>
                    <th>Cliente</th>
                    <th>Entrenador</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($workouts as $workout)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $workout->title }}</div>
                        <div style="font-size:12px;color:var(--dim);margin-top:2px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $workout->description }}</div>
                    </td>
                    <td>{{ $workout->client->user->name }}</td>
                    <td style="color:var(--accent)">{{ $workout->trainer->user->name }}</td>
                    <td style="font-size:13px;color:var(--dim)">{{ $workout->scheduled_date?->format('d/m/Y') ?? '—' }}</td>
                    <td><span class="badge badge-{{ $workout->status }}">{{ $workout->status }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px">
                            @if(auth()->user()->isAdmin() || auth()->user()->isTrainer())
                            <a href="{{ route('workouts.edit', $workout) }}" class="btn btn-ghost btn-sm">✏️</a>
                            <form method="POST" action="{{ route('workouts.destroy', $workout) }}" onsubmit="return confirm('¿Eliminar rutina?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:40px">No hay rutinas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div style="margin-top:16px">{{ $workouts->withQueryString()->links() }}</div>
@endsection
