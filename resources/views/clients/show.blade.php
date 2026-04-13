@extends('layouts.app')
@section('title', $client->user->name)

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-ghost">✏️ Editar</a>
    @endif
    <a href="{{ route('clients.index') }}" class="btn btn-ghost">← Volver</a>
@endsection

@section('content')

<div class="grid-2" style="margin-bottom:24px">
    {{-- Info personal --}}
    <div class="card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
            <div class="avatar" style="width:56px;height:56px;font-size:18px">
                {{ strtoupper(substr($client->user->name, 0, 2)) }}
            </div>
            <div>
                <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800">{{ $client->user->name }}</div>
                <div style="font-size:13px;color:var(--dim)">{{ $client->user->email }}</div>
                <div style="margin-top:6px">
                    <span class="badge badge-{{ $client->membershipStatus() }}">
                        {{ ['active'=>'Membresía Activa','expiring'=>'Por vencer','expired'=>'Expirada','none'=>'Sin plan'][$client->membershipStatus()] }}
                    </span>
                </div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            @foreach([
                ['Teléfono', $client->phone ?? '—'],
                ['Fecha nac.', $client->birth_date?->format('d/m/Y') ?? '—'],
                ['Peso', $client->weight ? $client->weight.' kg' : '—'],
                ['Altura', $client->height ? $client->height.' cm' : '—'],
            ] as [$label, $val])
            <div style="background:var(--bg);border-radius:10px;padding:12px">
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">{{ $label }}</div>
                <div style="font-size:15px;font-weight:600;margin-top:4px">{{ $val }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Membresía --}}
    <div class="card">
        <div class="section-title">💳 Membresía</div>
        @if($client->membership)
        <div style="background:var(--bg);border-radius:12px;padding:20px;margin-bottom:16px">
            <div style="font-family:'Syne',sans-serif;font-size:22px;font-weight:900;color:var(--accent)">{{ $client->membership->name }}</div>
            <div style="font-size:28px;font-weight:800;margin:8px 0">€{{ $client->membership->price }}<span style="font-size:14px;color:var(--dim)">/mes</span></div>
            <div style="font-size:13px;color:var(--dim)">
                Inicio: {{ $client->membership_starts_at?->format('d/m/Y') ?? '—' }}<br>
                Vence: {{ $client->membership_expires_at?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
        @else
        <div style="color:var(--muted);font-size:14px">Sin membresía asignada.</div>
        @endif

        @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('clients.assign-membership', $client) }}">
            @csrf
            <div style="display:flex;gap:10px;align-items:flex-end">
                <div style="flex:1">
                    <label>Cambiar membresía</label>
                    <select name="membership_id">
                        <option value="">Sin membresía</option>
                        @foreach($memberships as $m)
                        <option value="{{ $m->id }}" {{ $client->membership_id == $m->id ? 'selected' : '' }}>
                            {{ $m->name }} – €{{ $m->price }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Asignar</button>
            </div>
        </form>
        @endif
    </div>
</div>

{{-- Rutinas --}}
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
        <div class="section-title" style="margin:0">📋 Rutinas ({{ $client->workouts->count() }})</div>
        @if(auth()->user()->isAdmin() || auth()->user()->isTrainer())
        <a href="{{ route('workouts.create', ['client_id' => $client->id]) }}" class="btn btn-primary btn-sm">+ Añadir Rutina</a>
        @endif
    </div>
    @forelse($client->workouts as $workout)
    <div style="display:flex;align-items:center;gap:16px;padding:14px 0;border-bottom:1px solid var(--border)">
        <div style="flex:1">
            <div style="font-weight:600">{{ $workout->title }}</div>
            <div style="font-size:12px;color:var(--dim);margin-top:2px">{{ $workout->description }}</div>
        </div>
        <div style="text-align:right;font-size:12px;color:var(--dim)">
            {{ $workout->trainer->user->name }}<br>
            {{ $workout->scheduled_date?->format('d/m/Y') }}
        </div>
        <span class="badge badge-{{ $workout->status }}">{{ $workout->status }}</span>
    </div>
    @empty
    <div style="color:var(--muted);font-size:14px">Sin rutinas asignadas.</div>
    @endforelse
</div>

@endsection
