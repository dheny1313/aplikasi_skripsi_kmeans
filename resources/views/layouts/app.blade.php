<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Codero | Login</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f8fafc; /* Warna background abu-abu sangat muda khas SaaS */
        }
        .navbar-brand {
            font-weight: 800;
            color: #4f46e5 !important; /* Warna ungu/biru Indigo */
            letter-spacing: -0.5px;
        }
        .nav-link.custom-btn {
            font-weight: 600;
            border-radius: 50rem;
            padding: 8px 20px !important;
            transition: all 0.3s ease;
        }
        .nav-link.custom-btn-outline {
            color: #4f46e5;
            border: 2px solid #e0e7ff;
            background-color: white;
        }
        .nav-link.custom-btn-outline:hover {
            background-color: #e0e7ff;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-transparent py-3">
            <div class="container">
                 <a class="navbar-brand fw-bold text-white fs-4" href="{{ url('/') }}">
                <img src="{{ asset('images/LOGO_codero.png') }}" alt="Logo Codero" height="40" class="d-inline-block align-text-top me-2">
                <img src="{{ asset('images/stmik-baru.png') }}" alt="Logo stmik" height="40" class="d-inline-block align-text-top me-2">
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <ul class="navbar-nav ms-auto gap-2">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link custom-btn custom-btn-outline" href="{{ route('login') }}">{{ __('Masuk') }}</a>
                                </li>
                            @endif

                            {{-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link custom-btn text-white shadow-sm" style="background-color: #4f46e5;" href="{{ route('register') }}">{{ __('Daftar') }}</a>
                                </li>
                            @endif --}}
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle fw-bold text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    👋 {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item text-danger fw-semibold" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        🚪 Keluar
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-2">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
