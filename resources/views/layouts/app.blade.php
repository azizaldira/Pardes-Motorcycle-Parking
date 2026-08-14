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

                <!-- Menu Khusus Staff -->
                @if(auth()->user()->role === 'staff')
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
                    <a href="{{ route('pengaturan-slot') }}" class="sidebar-link {{ request()->routeIs('pengaturan-slot*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i> Pengaturan Kapasitas Slot
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('pengaturan-tarif') }}" class="sidebar-link {{ request()->routeIs('pengaturan-tarif*') ? 'active' : '' }}">
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
        
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <main class="main-wrapper">
            <!-- Header -->
            <header class="app-header">
                <div class="header-date">
                    <i class="fas fa-times" id="sidebarToggle" style="color: var(--text-muted); cursor: pointer; margin-right: 1rem;"></i>
                    <div style="line-height: 1.2;">
                        <span id="current-date">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span><br>
                        <span id="live-clock">{{ date('H:i:s') }} WIB</span>
                    </div>
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

    <!-- Live Clock & Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live Clock Update
            function updateClock() {
                const now = new Date();
                
                // Format Time
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
            
            // Set locale date initially via JS to guarantee Indonesian translation if PHP locale fails
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const now = new Date();
            document.getElementById('current-date').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

            setInterval(updateClock, 1000);
            updateClock();

            // Sidebar Toggle & Responsive Logic
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                }
                adjustSidebarIcon();
            }

            sidebarToggle.addEventListener('click', toggleSidebar);

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                        adjustSidebarIcon();
                    }
                });
            }

            // Adjust icon on load and resize
            function adjustSidebarIcon() {
                if (window.innerWidth <= 768) {
                    if (sidebar.classList.contains('show')) {
                        sidebarToggle.className = 'fas fa-times';
                        sidebarToggle.style.color = 'var(--text-main)'; // or keep --text-muted
                    } else {
                        sidebarToggle.className = 'fas fa-bars';
                        sidebarToggle.style.color = 'var(--text-muted)';
                    }
                    // Reset desktop state
                    sidebar.classList.remove('collapsed');
                } else {
                    if (sidebar.classList.contains('collapsed')) {
                        sidebarToggle.className = 'fas fa-bars';
                    } else {
                        sidebarToggle.className = 'fas fa-times';
                    }
                    // Reset mobile state
                    sidebar.classList.remove('show');
                }
            }

            window.addEventListener('resize', adjustSidebarIcon);
            adjustSidebarIcon(); // call on load
        });
    </script>
    <!-- Global Confirmation Modal -->
    <div id="globalConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="modal-content" style="background-color: white; padding: 2.5rem 2rem; border-radius: 16px; width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); text-align: center; transform: scale(0.95); transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); border-top: 5px solid #3b82f6;">
            <!-- Ikon Peringatan -->
            <div style="width: 70px; height: 70px; background-color: #fef2f2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; font-size: 2rem;">
                <i class="fas fa-trash-alt"></i>
            </div>
            
            <!-- Judul -->
            <h3 id="confirmModalTitle" style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.75rem;">Konfirmasi Penghapusan</h3>
            
            <!-- Pesan -->
            <p id="confirmModalMessage" style="color: #6b7280; font-size: 1rem; margin-bottom: 2.5rem; line-height: 1.5;">
                Apakah Anda yakin?
            </p>
            
            <!-- Tombol Aksi -->
            <div style="display: flex; justify-content: center; gap: 1rem;">
                <button type="button" id="btnConfirmCancel" class="btn" style="background-color: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db; flex: 1; padding: 0.75rem 1rem; font-weight: 600; font-size: 1rem; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="button" id="btnConfirmDelete" class="btn" style="background-color: #ef4444; color: white; border: none; flex: 1; padding: 0.75rem 1rem; font-weight: 600; font-size: 1rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        let confirmActionCallback = null;
        let currentBtnText = 'Hapus';
        const globalConfirmModal = document.getElementById('globalConfirmModal');
        const btnConfirmCancel = document.getElementById('btnConfirmCancel');
        const btnConfirmDelete = document.getElementById('btnConfirmDelete');
        const confirmModalMessage = document.getElementById('confirmModalMessage');
        const confirmModalTitle = document.getElementById('confirmModalTitle');
        const confirmModalContent = globalConfirmModal.querySelector('.modal-content');

        function showConfirmModal(message, callback, btnText = 'Hapus', titleText = null, btnColor = '#ef4444') {
            confirmModalMessage.textContent = message;
            confirmActionCallback = callback;
            currentBtnText = btnText;
            
            if (confirmModalTitle) {
                confirmModalTitle.textContent = titleText || (btnText === 'Hapus' ? 'Konfirmasi Penghapusan' : 'Konfirmasi Tindakan');
            }
            btnConfirmDelete.style.backgroundColor = btnColor;
            
            // Reset button state
            btnConfirmDelete.innerHTML = btnText;
            btnConfirmDelete.disabled = false;
            btnConfirmCancel.disabled = false;
            btnConfirmDelete.style.opacity = '1';
            btnConfirmCancel.style.opacity = '1';

            globalConfirmModal.style.display = 'flex';
            // Animasi pop-in
            setTimeout(() => {
                confirmModalContent.style.transform = 'scale(1)';
            }, 10);
            
            // Fokus ke tombol hapus untuk Enter to confirm
            setTimeout(() => {
                btnConfirmDelete.focus();
            }, 100);
        }

        function closeConfirmModal() {
            confirmModalContent.style.transform = 'scale(0.95)';
            setTimeout(() => {
                globalConfirmModal.style.display = 'none';
                confirmActionCallback = null;
            }, 200);
        }

        btnConfirmCancel.addEventListener('click', closeConfirmModal);

        btnConfirmDelete.addEventListener('click', () => {
            if (confirmActionCallback) {
                const loadingText = currentBtnText === 'Hapus' ? 'Menghapus...' : 'Memproses...';
                btnConfirmDelete.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i> ' + loadingText;
                btnConfirmDelete.disabled = true;
                btnConfirmCancel.disabled = true;
                btnConfirmDelete.style.opacity = '0.7';
                btnConfirmCancel.style.opacity = '0.7';
                
                // Execute callback
                confirmActionCallback();
            }
        });

        // Close on backdrop click
        globalConfirmModal.addEventListener('click', (e) => {
            if (e.target === globalConfirmModal && !btnConfirmCancel.disabled) {
                closeConfirmModal();
            }
        });

        // Keyboard support (Esc to close, Enter to confirm)
        document.addEventListener('keydown', (e) => {
            if (globalConfirmModal.style.display === 'flex' && !btnConfirmCancel.disabled) {
                if (e.key === 'Escape') {
                    closeConfirmModal();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    btnConfirmDelete.click();
                }
            }
        });
    </script>
</body>
</html>
