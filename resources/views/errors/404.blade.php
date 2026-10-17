<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | RentVentures</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
{{-- Halaman ini sengaja berdiri sendiri (tanpa layout/auth) agar tetap aman tampil saat terjadi error --}}
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div style="font-size: 5rem;">🧭</div>
        <h1 class="display-3 fw-bold text-success">404</h1>
        <h4 class="mb-3">Halaman tidak ditemukan</h4>
        <p class="text-muted mb-4">
            Sepertinya kamu tersesat di hutan. Halaman atau data yang dicari tidak ada atau sudah dipindahkan.
        </p>
        <a href="{{ url('/') }}" class="btn btn-success me-2">Kembali ke Beranda</a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">Halaman Sebelumnya</a>
    </div>
</body>
</html>
