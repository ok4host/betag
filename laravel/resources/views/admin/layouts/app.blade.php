<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - BeTaj Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body { background: #f4f6f9; }
        .sidebar { width: 260px; min-height: 100vh; background: linear-gradient(180deg, #1a1c23 0%, #2d3748 100%); position: fixed; right: 0; top: 0; z-index: 1000; transition: all 0.3s; }
        .sidebar .logo { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .logo h4 { color: #f59e0b; margin: 0; font-weight: 700; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; display: flex; align-items: center; gap: 10px; transition: all 0.3s; border-right: 3px solid transparent; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(245,158,11,0.1); border-right-color: #f59e0b; }
        .sidebar .nav-link i { font-size: 1.2rem; width: 24px; }
        .main-content { margin-right: 260px; padding: 20px; min-height: 100vh; }
        .top-bar { background: #fff; padding: 15px 25px; margin: -20px -20px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .stat-card { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .icon { width: 60px; height: 60px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-card .icon.primary { background: rgba(245,158,11,0.1); color: #f59e0b; }
        .stat-card .icon.success { background: rgba(16,185,129,0.1); color: #10b981; }
        .stat-card .icon.info { background: rgba(59,130,246,0.1); color: #3b82f6; }
        .stat-card .icon.danger { background: rgba(239,68,68,0.1); color: #ef4444; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card-header { background: #fff; border-bottom: 1px solid #eee; font-weight: 600; }
        .btn-primary { background: #f59e0b; border-color: #f59e0b; }
        .btn-primary:hover { background: #d97706; border-color: #d97706; }
        .table th { background: #f8fafc; font-weight: 600; }
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; }
        @media (max-width: 991px) { .sidebar { right: -260px; } .sidebar.show { right: 0; } .main-content { margin-right: 0; } }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <h4><i class="bi bi-building"></i> BeTaj Admin</h4>
        </div>
        <nav class="nav flex-column mt-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> لوحة التحكم
            </a>
            <a href="{{ route('admin.properties.index') }}" class="nav-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> العقارات
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> التصنيفات
            </a>
            <a href="{{ route('admin.locations.index') }}" class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> المواقع
            </a>
            <hr class="my-3 border-secondary">
            <a href="{{ url('/') }}" class="nav-link" target="_blank">
                <i class="bi bi-box-arrow-up-left"></i> زيارة الموقع
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <button class="btn btn-link d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="mb-0">@yield('title', 'لوحة التحكم')</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">{{ auth()->user()->name }}</span>
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> خروج
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
