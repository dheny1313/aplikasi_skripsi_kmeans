<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Codero | Sistem K-Means</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Hero Section Styling */
        .hero-section {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            padding: 120px 0 100px; /* Padding disesuaikan agar tidak menabrak navbar */
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.2);
            position: relative;
        }

        /* Feature Cards Styling */
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
            border: none;
            background-color: white;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
        }
        .icon-box {
            width: 65px; height: 65px;
            background: #e0e7ff; color: #4f46e5;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin-bottom: 20px;
        }

        /* Footer Styling */
        .site-footer {
            background-color: #1e293b;
            color: #94a3b8;
            padding: 40px 0 20px;
            margin-top: auto; /* Memaksa footer selalu di bawah */
        }
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent position-absolute w-100 mt-3 z-3">
            <div class="container">
                <a class="navbar-brand fw-bold text-white fs-4" href="{{ url('/') }}">
                <img src="{{ asset('images/LOGO_codero.png') }}" alt="Logo Codero" height="40" class="d-inline-block align-text-top me-2">
                <img src="{{ asset('images/stmik-baru.png') }}" alt="Logo stmik" height="40" class="d-inline-block align-text-top me-2">
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <div class="d-flex flex-column flex-lg-row gap-3 mt-3 mt-lg-0">
                        @auth
                            @if(auth()->user()->role == 'teacher')
                                <a href="{{ route('teacher.kmeans.index') }}" class="btn btn-light fw-bold text-primary rounded-pill px-4 shadow-sm">Masuk Dashboard Guru</a>
                            @else
                                <a href="{{ route('admin.kmeans.index') }}" class="btn btn-light fw-bold text-primary rounded-pill px-4 shadow-sm">Masuk Dashboard Admin</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light fw-bold rounded-pill px-4">Masuk</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero-section text-center">
            <div class="container mt-4">
                <h1 class="display-4 fw-bolder mb-4">Optimalkan Potensi Siswa dengan <br> <span class="text-warning">K-Means Clustering</span></h1>
                <p class="lead mb-5 opacity-75 mx-auto" style="max-width: 650px; font-weight: 400;">
                    Sistem cerdas untuk menganalisis dan mengelompokkan siswa berdasarkan multi-kriteria guna memberikan data sebagai bahan pertimbangan alokasi pengajar Coding yang paling tepat di Codero.
                </p>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-warning btn-lg fw-bold rounded-pill px-5 py-3 shadow-lg" style="transition: transform 0.2s;">Mulai Analisis Sekarang ➔</a>
                @endguest
            </div>
        </section>

        <section class="features-section container" style="margin-top: -60px; position: relative; z-index: 10; margin-bottom: 80px;">
            <div class="row g-4">
                <article class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 p-4">
                        <div class="icon-box">📊</div>
                        <h5 class="fw-bold text-dark">Agregasi Multi-Rater</h5>
                        <p class="text-muted mb-0">Menggabungkan penilaian dari berbagai guru secara otomatis untuk mendapatkan data siswa yang valid dan objektif.</p>
                    </div>
                </article>

                <article class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 p-4">
                        <div class="icon-box">🤖</div>
                        <h5 class="fw-bold text-dark">Algoritma K-Means</h5>
                        <p class="text-muted mb-0">Pemrosesan klastering yang cepat dan deterministik dengan validasi akurasi menggunakan metode DBI.</p>
                    </div>
                </article>

                <article class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 p-4">
                        <div class="icon-box">📑</div>
                        <h5 class="fw-bold text-dark">Validasi DBI & Export</h5>
                        <p class="text-muted mb-0">Evaluasi otomatis menggunakan Davies-Bouldin Index (DBI) dan fitur cetak laporan lengkap (PDF/Excel).</p>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <footer class="site-footer text-center">
        <div class="container">
            <h5 class="text-white fw-bold mb-3"> <img src="{{ asset('images/LOGO_codero.png') }}" alt="Logo Codero" height="40" class="d-inline-block align-text-top me-2">
                <img src="{{ asset('images/stmik-baru.png') }}" alt="Logo stmik" height="40" class="d-inline-block align-text-top me-2"> Codero App</h5>
            <p class="small mb-4 opacity-75" style="max-width: 500px; margin: 0 auto;">
                Platform analisis cerdas yang didedikasikan untuk meningkatkan kualitas pendidikan melalui pengelompokan siswa berbasis data.
            </p>
            <hr class="border-secondary opacity-25 my-4">
            <p class="small mb-0">
                &copy; {{ date('Y') }} Codero. Dibuat untuk Sistem Alokasi Pengajar. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
