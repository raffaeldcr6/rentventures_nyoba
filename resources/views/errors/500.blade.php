<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Terjadi Kesalahan Server | RentVentures</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
{{-- Halaman ini sengaja berdiri sendiri (tanpa layout/auth/database) agar tidak memicu error berantai --}}
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div style="font-size: 5rem;">🔥</div>
        <h1 class="display-3 fw-bold text-danger">500</h1>
        <h4 class="mb-3">Terjadi kesalahan pada server</h4>
        <p class="text-muted mb-4">
            Maaf, ada gangguan saat memproses permintaanmu. Silakan coba lagi beberapa saat lagi.
        </p>
        <a href="{{ url('/') }}" class="btn btn-success me-2">Kembali ke Beranda</a>
        <a href="javascript:location.reload()" class="btn btn-outline-secondary">Muat Ulang</a>
    </div>
</body>
</html>
