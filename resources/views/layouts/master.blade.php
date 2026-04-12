<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - DSS K-Means</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            overflow-x: hidden;
        }

        /* Sidebar Default (Desktop) */
        .sidebar {
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            transition: transform 0.3s ease-in-out;
            width: 260px;
            z-index: 1045;
        }
        .sidebar-brand {
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: -0.5px;
        }
        .sidebar a {
            color: #4b5563;
            text-decoration: none;
            padding: 10px 16px;
            display: block;
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: background-color 0.2s, color 0.2s;
        }
        .sidebar a:hover {
            background-color: #f3f4f6;
            color: #111827;
        }
        .sidebar .active {
            background-color: #e0e7ff;
            color: #4f46e5;
            font-weight: 600;
        }

        .content-area {
            width: 100%;
            transition: margin-left 0.3s ease-in-out;
        }
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }
        .saas-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        /* Overlay Gelap untuk Mobile (Background saat sidebar terbuka) */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        /* Responsiveness untuk Mobile */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                transform: translateX(-100%); /* Sembunyikan ke kiri layar */
                height: 100vh;
            }
            .sidebar.show {
                transform: translateX(0); /* Munculkan saat class 'show' aktif */
            }
            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="sidebar p-3" id="sidebar">
            <div class="text-center mb-4 mt-2">
                <a class="navbar-brand fw-bold text-white fs-4" href="{{ url('/') }}">
                <img src="{{ asset('images/LOGO_codero.png') }}" alt="Logo Codero" height="40" class="d-inline-block align-text-top me-2">
                <img src="{{ asset('images/stmik-baru.png') }}" alt="Logo stmik" height="40" class="d-inline-block align-text-top me-2">
                </a>
                {{-- <h4 class="sidebar-brand mb-0">Codero</h4> --}}
                <small class="text-muted" style="font-size: 13px; font-weight: 500;">Sistem Pengelompokan</small>
            </div>

           <ul class="nav flex-column mb-auto mt-4">
                <li class="nav-item mb-3 text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding-left: 16px;">Menu Utama</li>

                @if(Auth::check() && Auth::user()->role === 'admin')
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a></li>
                    <li><a href="{{ route('admin.user.index') }}" class="{{ request()->routeIs('admin.user.*') ? 'active' : '' }}">👥 Manajemen Pengguna</a></li>
                    <li><a href="{{ route('admin.student.index') }}" class="{{ request()->routeIs('admin.student.*') ? 'active' : '' }}">👨‍🎓 Master Siswa</a></li>
                    <li><a href="{{ route('admin.criteria.index') }}" class="{{ request()->routeIs('admin.criteria.*') ? 'active' : '' }}">📊 Master Kriteria</a></li>
                    <li><a href="{{ route('admin.score.index') }}" class="{{ request()->routeIs('admin.score.*') ? 'active' : '' }}">📝 Manajemen Nilai</a></li>
                    <li><a href="{{ route('admin.kmeans.index') }}" class="{{ request()->routeIs('admin.kmeans.*') ? 'active' : '' }}">⚙️ Eksekusi K-Means</a></li>
                    <li><a href="{{ route('admin.report.index') }}" class="{{ request()->routeIs('admin.report.*') ? 'active' : '' }}">📄 Cetak Laporan</a></li>
                @elseif(Auth::check() && Auth::user()->role === 'teacher')
                    <li><a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">🏠 Dashboard</a></li>
                    <li><a href="{{ route('teacher.score.index') }}">📝 Input Nilai Siswa</a></li>
                    <li><a href="{{ route('teacher.kmeans.index') }}" class="{{ request()->routeIs('teacher.kmeans.*') ? 'active' : '' }}">📊 Laporan Klastering</a></li>

                @endif

            </ul>

            <div class="mt-5 px-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light w-100 text-start" style="color: #ef4444; font-weight: 500;">
                        🚪 Keluar Sistem
                    </button>
                </form>
            </div>
        </div>

        <div class="content-area">
            <nav class="navbar navbar-expand-lg top-navbar py-3 px-3 px-md-4">
                <div class="container-fluid">

                    <button class="btn btn-light d-md-none me-3 border-0 shadow-sm" id="sidebarToggle" style="background-color: #f3f4f6;">
                        <span class="fs-5">☰</span>
                    </button>

                    <span class="navbar-brand mb-0 h5 fw-bold d-none d-sm-block" style="color: #111827;">@yield('title')</span>

                    <span class="navbar-brand mb-0 h6 fw-bold d-block d-sm-none ms-auto me-auto" style="color: #111827;">Codero</span>

                    <div class="d-flex align-items-center ms-auto">
                        <div class="d-flex align-items-center bg-light rounded-pill px-3 py-1">
                            <span class="me-2 text-muted d-none d-sm-block" style="font-size: 0.9rem;">Halo,</span>
                            <span class="fw-semibold" style="color: #4f46e5; font-size: 0.95rem;">{{ Auth::user()->name }}</span>
                            <span class="badge bg-secondary ms-2 rounded-pill d-none d-sm-block">{{ ucfirst(Auth::user()->role) }}</span>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-3 p-md-5">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            // Fungsi untuk membuka/menutup sidebar
            function toggleSidebar() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

            // Jika tombol hamburger diklik
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            // Jika area gelap (overlay) diklik, tutup sidebar
            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>
</html>
