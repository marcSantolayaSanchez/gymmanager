<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GymManager – @yield('title', 'Panel')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:       #09090F;
            --surface:  #101018;
            --card:     #14141E;
            --border:   #1C1C2E;
            --accent:   #C8FF00;
            --accent-d: #C8FF0018;
            --red:      #FF3B5C;
            --blue:     #3B82F6;
            --purple:   #9B5CF6;
            --green:    #22C55E;
            --amber:    #F59E0B;
            --text:     #E0E0F0;
            --muted:    #52527A;
            --dim:      #8888AA;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
        }
        .app { display: flex; height: 100vh; overflow: hidden; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 240px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 24px 0;
            flex-shrink: 0;
        }
        .sidebar-logo {
            display: flex; align-items: center; gap: 10px;
            padding: 0 20px 28px;
        }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--accent); display: flex;
            align-items: center; justify-content: center; font-size: 17px;
        }
        .logo-text { font-family: 'Syne', sans-serif; font-weight: 900; font-size: 17px; letter-spacing: -.03em; }
        .logo-sub  { font-size: 10px; color: var(--muted); letter-spacing: .08em; }
        nav { flex: 1; padding: 0 10px; display: flex; flex-direction: column; gap: 3px; }
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 14px; border-radius: 10px;
            color: var(--dim); font-size: 13.5px; font-weight: 500;
            text-decoration: none; transition: all .15s;
            border-left: 3px solid transparent;
        }
        .nav-link:hover { background: var(--accent-d); color: var(--accent); }
        .nav-link.active {
            background: var(--accent-d); color: var(--accent);
            border-left-color: var(--accent); font-weight: 700;
        }
        .nav-link .icon { font-size: 16px; }
        .sidebar-user {
            margin: 20px 10px 0;
            padding: 16px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--accent-d); border: 2px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-weight: 800;
            font-size: 12px; color: var(--accent); flex-shrink: 0;
        }
        .user-name  { font-size: 13px; font-weight: 600; color: var(--text); }
        .user-role  { font-size: 11px; color: var(--accent); }

        /* ── MAIN ── */
        .main { flex: 1; overflow-y: auto; padding: 36px 40px; }
        .page-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 32px;
        }
        .page-date  { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
        .page-title { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 900; letter-spacing: -.03em; }

        /* ── CARDS & GRIDS ── */
        .card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 16px; padding: 24px;
        }
        .grid-metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .metric-card  {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; padding: 22px 24px;
        }
        .metric-icon  { font-size: 22px; margin-bottom: 10px; }
        .metric-value { font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 900; line-height: 1; }
        .metric-label { font-size: 12px; color: var(--dim); text-transform: uppercase; letter-spacing: .06em; margin-top: 6px; }
        .metric-sub   { font-size: 11px; color: var(--muted); margin-top: 4px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .section-title {
            font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 800;
            color: var(--text); margin-bottom: 18px; letter-spacing: -.02em;
        }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 10px 14px;
            font-size: 11px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .07em;
            border-bottom: 1px solid var(--border);
        }
        td { padding: 14px; border-bottom: 1px solid var(--border); font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #ffffff05; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 99px;
            font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
        }
        .badge-active   { background: #22C55E18; color: var(--green);  border: 1px solid #22C55E33; }
        .badge-expiring { background: #F59E0B18; color: var(--amber);  border: 1px solid #F59E0B33; }
        .badge-expired  { background: #FF3B5C18; color: var(--red);    border: 1px solid #FF3B5C33; }
        .badge-none     { background: #52527A18; color: var(--muted);  border: 1px solid #52527A33; }
        .badge-pending   { background: #3B82F618; color: var(--blue);   border: 1px solid #3B82F633; }
        .badge-completed { background: #22C55E18; color: var(--green);  border: 1px solid #22C55E33; }
        .badge-cancelled { background: #FF3B5C18; color: var(--red);    border: 1px solid #FF3B5C33; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 18px; border-radius: 10px; border: none;
            font-family: 'Syne', sans-serif; font-weight: 700;
            font-size: 13px; cursor: pointer; text-decoration: none;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        .btn-primary  { background: var(--accent); color: #000; }
        .btn-ghost    { background: transparent; color: var(--dim); border: 1px solid var(--border); }
        .btn-danger   { background: #FF3B5C18; color: var(--red); border: 1px solid #FF3B5C33; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ── FORMS ── */
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 12px; font-weight: 600; color: var(--dim); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%; background: var(--bg); border: 1px solid var(--border);
            border-radius: 10px; padding: 11px 14px; color: var(--text);
            font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none;
            transition: border-color .15s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); }
        input::placeholder, textarea::placeholder { color: var(--muted); }
        select option { background: var(--card); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* ── ALERT ── */
        .alert {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
        }
        .alert-warning { background: #F59E0B11; border: 1px solid #F59E0B33; }
        .alert-success { background: #22C55E11; border: 1px solid #22C55E33; }
        .alert-error   { background: #FF3B5C11; border: 1px solid #FF3B5C33; }
        .alert-title   { font-weight: 700; font-size: 14px; }
        .alert-body    { font-size: 13px; color: var(--dim); margin-top: 2px; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

        /* ── MODAL ── */
        .modal-backdrop {
            display: none; position: fixed; inset: 0;
            background: #00000080; z-index: 100;
            align-items: center; justify-content: center;
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 20px; padding: 32px; width: 100%; max-width: 520px;
            max-height: 90vh; overflow-y: auto;
        }
        .modal-title { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 900; margin-bottom: 24px; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
    </style>
    @stack('styles')
</head>
<body>
<div class="app">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🏋️</div>
            <div>
                <div class="logo-text">GymManager</div>
                <div class="logo-sub">ADMIN PANEL</div>
            </div>
        </div>
        <nav>
            <a href="{{ route('dashboard') }}"    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="icon">▦</span> Dashboard
            </a>
            <a href="{{ route('clients.index') }}"     class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <span class="icon">👥</span> Clientes
            </a>
            <a href="{{ route('trainers.index') }}"    class="nav-link {{ request()->routeIs('trainers.*') ? 'active' : '' }}">
                <span class="icon">🏋️</span> Entrenadores
            </a>
            <a href="{{ route('workouts.index') }}"    class="nav-link {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                <span class="icon">📋</span> Rutinas
            </a>
            <a href="{{ route('memberships.index') }}" class="nav-link {{ request()->routeIs('memberships.*') ? 'active' : '' }}">
                <span class="icon">💳</span> Membresías
            </a>
        </nav>
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">● {{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">
        <div class="page-header">
            <div>
                <div class="page-date">{{ now()->locale('es')->isoFormat('dddd, D MMMM YYYY') }}</div>
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                @yield('header-actions')
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Salir</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span>
                <div><div class="alert-title">{{ session('success') }}</div></div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <span>❌</span>
                <div><div class="alert-title">{{ session('error') }}</div></div>
            </div>
        @endif

        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
