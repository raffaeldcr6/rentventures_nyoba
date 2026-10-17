<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') | RentVentures</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f4f7f4; }
        .navbar-brand { font-weight: 700; letter-spacing: .5px; }
        .card { border: 0; border-radius: 14px; }
        .card-equipment { transition: all .2s ease; }
        .card-equipment:hover { transform: translateY(-3px); box-shadow: 0 .6rem 1.2rem rgba(0, 0, 0, .12) !important; }
        .equipment-img { height: 180px; object-fit: cover; border-radius: 14px 14px 0 0; }
        .equipment-placeholder {
            height: 180px; display: flex; align-items: center; justify-content: center;
            font-size: 3.5rem; background: linear-gradient(135deg, #d8f3dc, #95d5b2);
            border-radius: 14px 14px 0 0;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- ========== NAVBAR ========== --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-tree-fill"></i> RentVentures
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
                    aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('equipments.*') ? 'active fw-semibold' : '' }}"
                               href="{{ route('equipments.index') }}">
                                <i class="bi bi-backpack2"></i> Katalog Alat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rentals.*') ? 'active fw-semibold' : '' }}"
                               href="{{ route('rentals.index') }}">
                                <i class="bi bi-receipt"></i>
                                {{ auth()->user()->isAdmin() ? 'Kelola Transaksi' : 'Riwayat Sewa' }}
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav align-items-lg-center gap-lg-2">
                    @auth
                        <li class="nav-item text-white me-lg-2 py-2 py-lg-0">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            @if (auth()->user()->isAdmin())
                                <span class="badge bg-danger ms-1">Admin</span>
                            @else
                                <span class="badge bg-warning text-dark ms-1">Penyewa</span>
                            @endif
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm" href="{{ route('register') }}">Daftar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- ========== KONTEN ========== --}}
    <main class="container py-4 flex-grow-1">

        {{-- Flash message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                Input belum valid. Periksa kembali kolom yang ditandai merah di bawah.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-muted small py-3 border-top bg-white">
        &copy; {{ date('Y') }} RentVentures &middot; Sistem Penyewaan Alat Camping &amp; Outdoor
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
