@extends('layouts.app')
@section('title', 'Clientes')

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Nuevo Cliente</a>
    @endif
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" style="display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap">
    <input name="search" value="{{ request('search') }}" placeholder="Buscar cliente..." style="flex:1;min-width:200px">
    <select name="status" style="width:auto">
        <option value="">Todos</option>
        <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Activos</option>
        <option value="expiring"  {{ request('status') === 'expiring'  ? 'selected' : '' }}>Por vencer</option>
        <option value="expired"   {{ request('status') === 'expired'   ? 'selected' : '' }}>Expirados</option>
    </select>
    <button type="submit" class="btn btn-ghost">Filtrar</button>
    @if(request('search') || request('status'))
        <a href="{{ route('clients.index') }}" class="btn btn-ghost">✕ Limpiar</a>
    @endif
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Membresía</th>
                    <th>Estado</th>
                    <th>Vence</th>
                    <th>Peso / Altura</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar">{{ strtoupper(substr($client->user->name, 0, 2)) }}</div>
                            <div>
                                <div style="font-weight:600">{{ $client->user->name }}</div>
                                <div style="font-size:12px;color:var(--dim)">{{ $client->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $client->membership?->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $client->membershipStatus() }}">
                            {{ ['active'=>'Activa','expiring'=>'Por vencer','expired'=>'Expirada','none'=>'Sin plan'][$client->membershipStatus()] }}
                        </span>
                    </td>
                    <td style="color:var(--dim);font-size:13px">
                        {{ $client->membership_expires_at?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td style="font-size:13px;color:var(--dim)">
                        {{ $client->weight ? $client->weight.' kg' : '—' }} /
                        {{ $client->height ? $client->height.' cm' : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-ghost btn-sm">Ver</a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-ghost btn-sm">✏️</a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('¿Eliminar cliente?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:40px">No se encontraron clientes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div style="margin-top:16px">{{ $clients->withQueryString()->links() }}</div>

@endsection
