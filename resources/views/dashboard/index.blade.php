@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Métricas --}}
<div class="grid-metrics">
    <div class="metric-card">
        <div class="metric-icon">👥</div>
        <div class="metric-value" style="color:#C8FF00">{{ $metrics['total_clients'] }}</div>
        <div class="metric-label">Clientes totales</div>
    </div>
    <div class="metric-card">
        <div class="metric-icon">✅</div>
        <div class="metric-value" style="color:#22C55E">{{ $metrics['active_clients'] }}</div>
        <div class="metric-label">Membresías activas</div>
        <div class="metric-sub">{{ $metrics['expiring_clients'] }} por vencer</div>
    </div>
    <div class="metric-card">
        <div class="metric-icon">💰</div>
        <div class="metric-value" style="color:#9B5CF6">€{{ number_format($metrics['monthly_revenue'], 0) }}</div>
        <div class="metric-label">Ingresos mensuales</div>
        <div class="metric-sub">Recurrente</div>
    </div>
    <div class="metric-card">
        <div class="metric-icon">🏋️</div>
        <div class="metric-value" style="color:#3B82F6">{{ $metrics['total_trainers'] }}</div>
        <div class="metric-label">Entrenadores</div>
    </div>
    <div class="metric-card">
        <div class="metric-icon">📋</div>
        <div class="metric-value" style="color:#FF3B5C">{{ $metrics['total_workouts'] }}</div>
        <div class="metric-label">Rutinas activas</div>
    </div>
</div>

{{-- Alerta membresías por vencer --}}
@if($expiringSoon->count() > 0)
<div class="alert alert-warning">
    <span>⚠️</span>
    <div>
        <div class="alert-title">{{ $expiringSoon->count() }} membresía(s) por vencer</div>
        <div class="alert-body">
            {{ $expiringSoon->pluck('user.name')->join(', ') }} – Se enviará email de renovación automáticamente.
        </div>
    </div>
</div>
@endif

<div class="grid-2">
    {{-- Últimos clientes --}}
    <div class="card">
        <div class="section-title">🕐 Últimos Clientes</div>
        @foreach($recentClients as $client)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)">
            <div class="avatar">{{ strtoupper(substr($client->user->name, 0, 2)) }}</div>
            <div style="flex:1">
                <div style="font-weight:600;font-size:14px">{{ $client->user->name }}</div>
                <div style="font-size:12px;color:var(--dim)">{{ $client->membership?->name ?? '—' }} · {{ $client->workouts->first()?->trainer->user->name ?? '—' }}</div>
            </div>
            <span class="badge badge-{{ $client->membershipStatus() }}">
                {{ ['active'=>'Activa','expiring'=>'Por vencer','expired'=>'Expirada','none'=>'Sin plan'][$client->membershipStatus()] }}
            </span>
        </div>
        @endforeach
        <div style="margin-top:14px">
            <a href="{{ route('clients.index') }}" class="btn btn-ghost btn-sm">Ver todos →</a>
        </div>
    </div>

    {{-- Rutinas recientes --}}
    <div class="card">
        <div class="section-title">🏃 Rutinas Recientes</div>
        @foreach($recentWorkouts as $workout)
        <div style="padding:12px 0;border-bottom:1px solid var(--border)">
            <div style="font-weight:600;font-size:14px">{{ $workout->title }}</div>
            <div style="font-size:12px;color:var(--dim);margin-top:4px">
                {{ $workout->client->user->name }} ·
                <span style="color:var(--accent)">{{ $workout->trainer->user->name }}</span>
            </div>
        </div>
        @endforeach
        <div style="margin-top:14px">
            <a href="{{ route('workouts.index') }}" class="btn btn-ghost btn-sm">Ver todas →</a>
        </div>
    </div>
</div>

{{-- Distribución membresías --}}
<div class="card" style="margin-top:24px">
    <div class="section-title">📊 Distribución de Membresías</div>
    <div style="display:flex;gap:24px;flex-wrap:wrap">
        @foreach($membershipDistribution as $m)
        @php $pct = $metrics['total_clients'] > 0 ? round(($m['active_count'] / $metrics['total_clients']) * 100) : 0; @endphp
        <div style="flex:1;min-width:140px">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                <span style="font-size:13px;font-weight:600">{{ $m['name'] }}</span>
                <span style="font-size:13px;color:var(--accent);font-weight:700">{{ $m['active_count'] }} ({{ $pct }}%)</span>
            </div>
            <div style="height:8px;border-radius:99px;background:var(--bg);overflow:hidden">
                <div style="height:100%;width:{{ $pct }}%;background:var(--accent);border-radius:99px"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
