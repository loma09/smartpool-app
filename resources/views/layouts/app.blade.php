<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartPool') — Sistem Kolam Renang Pintar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-width: 240px;
            --accent: #38bdf8;
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            padding: 0;
            z-index: 100;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand h6 { color: var(--accent); margin: 0; font-weight: 700; font-size: .95rem; letter-spacing: .5px; }
        .sidebar-brand small { color: rgba(255,255,255,.4); font-size: .72rem; }
        .sidebar-nav { flex: 1; padding: 1rem 0; overflow-y: auto; }
        .sidebar-label {
            padding: .25rem 1.5rem;
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            margin-top: .75rem;
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: .75rem;
            padding: .55rem 1.5rem;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .875rem;
            transition: all .15s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: #fff;
            background: rgba(56,189,248,.12);
            border-left: 3px solid var(--accent);
        }
        .sidebar-nav a i { font-size: 1rem; opacity: .8; }
        .sidebar-user {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-user .name { color: #fff; font-size: .825rem; font-weight: 600; }
        .sidebar-user .role-badge {
            font-size: .65rem; padding: 2px 8px;
            border-radius: 20px;
            background: rgba(56,189,248,.2);
            color: var(--accent);
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 1.5rem 2rem;
        }
        .topbar {
            background: #fff;
            border-radius: 12px;
            padding: .75rem 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            display: flex; align-items: center; justify-content: space-between;
        }
        .topbar h5 { margin: 0; font-weight: 700; color: #0f172a; font-size: 1rem; }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            border: 1px solid #e2e8f0;
        }
        .stat-card .icon-box {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .stat-card .value { font-size: 1.5rem; font-weight: 700; color: #0f172a; }
        .stat-card .label { font-size: .8rem; color: #64748b; }

        /* Status badges */
        .status-dot {
            display: inline-block; width: 8px; height: 8px;
            border-radius: 50%; margin-right: 6px;
        }
        .status-dot.online  { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.2); }
        .status-dot.offline { background: #ef4444; }

        /* Tables */
        .table-card {
            background: #fff; border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .table-card .table { margin: 0; }
        .table-card .table th {
            background: #f8fafc; font-size: .78rem;
            text-transform: uppercase; letter-spacing: .5px;
            color: #64748b; border-bottom: 1px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h6><i class="bi bi-water me-2"></i>SmartPool</h6>
        <small>Sistem Kolam Renang Pintar</small>
    </div>

    <nav class="sidebar-nav">
        @if(auth()->user()->isAdmin())
            <div class="sidebar-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-label">Monitoring</div>
            <a href="{{ route('admin.rain-logs') }}" class="{{ request()->routeIs('admin.rain-logs') ? 'active' : '' }}">
                <i class="bi bi-cloud-rain"></i> Log Hujan
            </a>
            <a href="{{ route('admin.chlorine-logs') }}" class="{{ request()->routeIs('admin.chlorine-logs') ? 'active' : '' }}">
                <i class="bi bi-droplet-half"></i> Log Kaporit
            </a>

            <div class="sidebar-label">Manajemen</div>
            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Kelola Pengguna
            </a>
            <a href="{{ route('admin.sensor-config') }}" class="{{ request()->routeIs('admin.sensor-config') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i> Konfigurasi Sensor
            </a>
        @else
            <div class="sidebar-label">Utama</div>
            <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-label">Monitoring</div>
            <a href="{{ route('user.rain-logs') }}" class="{{ request()->routeIs('user.rain-logs') ? 'active' : '' }}">
                <i class="bi bi-cloud-rain"></i> Log Hujan
            </a>
            <a href="{{ route('user.chlorine-logs') }}" class="{{ request()->routeIs('user.chlorine-logs') ? 'active' : '' }}">
                <i class="bi bi-droplet-half"></i> Log Kaporit
            </a>

            <div class="sidebar-label">Akun</div>
            <a href="{{ route('user.profile') }}" class="{{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> Profil Saya
            </a>
        @endif
    </nav>

    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-2">
            <div>
                <div class="name">{{ auth()->user()->name }}</div>
                <span class="role-badge">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-sm btn-link text-secondary p-0" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Main -->
<main class="main-content">
    <div class="topbar">
        <h5>@yield('page-title', 'Dashboard')</h5>
        <div class="d-flex align-items-center gap-3">
            <small class="text-muted" id="clock"></small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleString('id-ID', {
            weekday: 'short', day: '2-digit', month: 'short',
            year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    updateClock(); setInterval(updateClock, 1000);
</script>
@stack('scripts')
</body>
</html>
