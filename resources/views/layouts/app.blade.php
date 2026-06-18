<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business->business_name ?? "Penitipan Parde's" }} - Sistem Parkir</title>
    <!-- FontAwesome for icons (using CDN for simplicity) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div>
                    <div class="sidebar-title">{{ $business->business_name ?? "Penitipan Parde's" }}</div>
                    <div class="sidebar-subtitle">Parkir Motor</div>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>

                <!-- Menu Staff & Admin -->
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                <li class="sidebar-item">
                    <a href="{{ route('kendaraan-masuk') }}" class="sidebar-link {{ request()->routeIs('kendaraan-masuk') ? 'active' : '' }}">
                        <i class="fas fa-arrow-right-to-bracket"></i> Kendaraan Masuk
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('cari-kendaraan') }}" class="sidebar-link {{ request()->routeIs('cari-kendaraan') ? 'active' : '' }}">
                        <i class="fas fa-search"></i> Cari Kendaraan
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('kendaraan-keluar') }}" class="sidebar-link {{ request()->routeIs('kendaraan-keluar') ? 'active' : '' }}">
                        <i class="fas fa-arrow-right-from-bracket"></i> Kendaraan Keluar
                    </a>
                </li>
                @endif

                <!-- Menu Khusus Admin -->
                @if(auth()->user()->role === 'admin')
                <li class="sidebar-item mt-4">
                    <a href="{{ route('laporan') }}" class="sidebar-link {{ request()->routeIs('laporan') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Laporan
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('pengaturan-tarif') }}" class="sidebar-link {{ request()->routeIs('pengaturan-tarif') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave"></i> Pengaturan Tarif
                    </a>
                </li>
                @endif

                <!-- Manajemen Akun (Admin & Staff) -->
                <li class="sidebar-item mt-4">
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i> Manajemen Akun
                    </a>
                </li>

                <!-- Pengaturan Bisnis (Admin Only) -->
                @if(auth()->user()->role === 'admin')
                <li class="sidebar-item">
                    <a href="{{ route('pengaturan-bisnis') }}" class="sidebar-link {{ request()->routeIs('pengaturan-bisnis') ? 'active' : '' }}">
                        <i class="fas fa-building"></i> Pengaturan Bisnis
                    </a>
                </li>
                @endif
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-wrapper">
            <!-- Header -->
            <header class="app-header">
                <div class="header-date">
                    <i class="fas fa-times" style="color: var(--text-muted); cursor: pointer; margin-right: 1rem;"></i>
                    {{ now()->translatedFormat('l, d F Y') }} <br>
                    {{ now()->format('H.i.s') }}
                </div>
                
                <div class="header-user">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ ucfirst(auth()->user()->role == 'admin' ? 'Pemilik' : 'Petugas') }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Keluar</button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
