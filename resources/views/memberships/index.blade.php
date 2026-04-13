@extends('layouts.app')
@section('title', 'Membresías')
@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('memberships.create') }}" class="btn btn-primary">+ Nueva Membresía</a>
    @endif
@endsection
@section('content')

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-bottom:32px">
    @php $colors = ['#3B82F6','#9B5CF6','#C8FF00']; @endphp
    @foreach($memberships as $i => $m)
    @php $color = $colors[$i % 3]; @endphp
    <div class="card" style="border-color:{{ $color }}44;position:relative;overflow:hidden">
        <div style="position:absolute;inset:0;background:radial-gradient(circle at top right,{{ $color }}10,transparent 60%);pointer-events:none"></div>
        <div style="font-size:12px;font-weight:700;color:{{ $color }};text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px">{{ $m->name }}</div>
        <div style="font-family:'Syne',sans-serif;font-size:38px;font-weight:900;line-height:1">
            €{{ $m->price }}<span style="font-size:14px;color:var(--dim);font-family:'DM Sans',sans-serif">/mes</span>
        </div>
        <div style="color:var(--dim);font-size:13px;margin:10px 0 20px">{{ $m->duration_days }} días · {{ $m->clients_count }} clientes activos</div>
        @if($m->description)
        <p style="font-size:13px;color:var(--dim);line-height:1.6;margin-bottom:20px">{{ $m->description }}</p>
        @endif
        @if(auth()->user()->isAdmin())
        <div style="display:flex;gap:8px">
            <a href="{{ route('memberships.edit', $m) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center">✏️ Editar</a>
            <form method="POST" action="{{ route('memberships.destroy', $m) }}" onsubmit="return confirm('¿Eliminar membresía?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">🗑</button>
            </form>
        </div>
        @endif
    </div>
    @endforeach
</div>

{{-- Distribución --}}
<div class="card">
    <div class="section-title">📊 Distribución de Clientes por Membresía</div>
    @php $total = $memberships->sum('clients_count') ?: 1; @endphp
    <div style="display:flex;flex-direction:column;gap:14px">
        @foreach($memberships as $i => $m)
        @php $pct = round(($m->clients_count / $total) * 100); $color = $colors[$i % 3]; @endphp
        <div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                <span style="font-size:14px;font-weight:600">{{ $m->name }}</span>
                <span style="font-size:14px;color:{{ $color }};font-weight:700">{{ $m->clients_count }} clientes ({{ $pct }}%)</span>
            </div>
            <div style="height:8px;border-radius:99px;background:var(--bg)">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:99px;transition:width .5s"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
