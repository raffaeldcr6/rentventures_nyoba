@extends('layouts.app')

@section('title', 'Katalog Alat')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h3 class="fw-bold mb-0">Katalog Alat Camping</h3>
        <p class="text-muted mb-0">
            {{ auth()->user()->isAdmin() ? 'Kelola data alat outdoor RentVentures.' : 'Pilih alat yang ingin kamu sewa.' }}
        </p>
    </div>

    @if (auth()->user()->isAdmin())
        <a href="{{ route('equipments.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Alat
        </a>
    @endif
</div>

{{-- Pencarian & filter --}}
<form action="{{ route('equipments.index') }}" method="GET" class="row g-2 mb-4">
    <div class="col-md-6">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama alat...">
    </div>
    <div class="col-md-4">
        <select name="kategori" class="form-select">
            <option value="">Semua Kategori</option>
            @foreach ($kategoriList as $kategori)
                <option value="{{ $kategori }}" {{ request('kategori') === $kategori ? 'selected' : '' }}>
                    {{ $kategori }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-outline-success"><i class="bi bi-search"></i> Cari</button>
    </div>
</form>

@if ($equipments->count() === 0)
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <div class="display-4">🔍</div>
            <h5 class="mt-2">Alat tidak ditemukan</h5>
            <p class="text-muted mb-0">Coba ubah kata kunci atau kategori pencarian.</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach ($equipments as $equipment)
            <div class="col-sm-6 col-lg-4">
                <div class="card card-equipment shadow-sm h-100">
                    @if ($equipment->foto_url)
                        <img src="{{ $equipment->foto_url }}" class="equipment-img" alt="{{ $equipment->nama }}">
                    @else
                        <div class="equipment-placeholder">⛺</div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-success-subtle text-success-emphasis align-self-start mb-2">
                            {{ $equipment->kategori }}
                        </span>
                        <h5 class="card-title fw-bold">{{ $equipment->nama }}</h5>
                        <p class="card-text text-muted small flex-grow-1">
                            {{ \Illuminate\Support\Str::limit($equipment->deskripsi, 90) ?: 'Belum ada deskripsi.' }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-success">
                                Rp {{ number_format($equipment->harga_sewa_per_hari, 0, ',', '.') }}
                                <small class="text-muted fw-normal">/ hari</small>
                            </span>
                            @if ($equipment->stok > 0)
                                <span class="badge bg-light text-dark border">Stok: {{ $equipment->stok }}</span>
                            @else
                                <span class="badge bg-danger">Stok habis</span>
                            @endif
                        </div>

                        @if (auth()->user()->isAdmin())
                            <div class="d-flex gap-2">
                                <a href="{{ route('equipments.edit', $equipment->id) }}"
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('equipments.destroy', $equipment->id) }}" method="POST"
                                      class="flex-fill"
                                      onsubmit="return confirm('Hapus alat &quot;{{ addslashes($equipment->nama) }}&quot;? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @else
                            @if ($equipment->stok > 0)
                                <a href="{{ route('rentals.create', ['equipment_id' => $equipment->id]) }}"
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-cart-plus"></i> Sewa Sekarang
                                </a>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>Tidak tersedia</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $equipments->links() }}
    </div>
@endif
@endsection
