@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Kelola Transaksi' : 'Riwayat Sewa')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h3 class="fw-bold mb-0">{{ $isAdmin ? 'Kelola Transaksi Sewa' : 'Riwayat Sewa Saya' }}</h3>
        <p class="text-muted mb-0">
            {{ $isAdmin ? 'Setujui dan pantau seluruh transaksi penyewaan.' : 'Pantau status penyewaan alat kamu.' }}
        </p>
    </div>

    @unless ($isAdmin)
        <a href="{{ route('rentals.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Sewa Alat
        </a>
    @endunless
</div>

{{-- Filter status --}}
<form action="{{ route('rentals.index') }}" method="GET" class="row g-2 mb-3">
    <div class="col-8 col-md-4">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            @foreach ($statusList as $status)
                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-4 col-md-2 d-grid">
        <button type="submit" class="btn btn-outline-success">Filter</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    @if ($isAdmin) <th>Penyewa</th> @endif
                    <th>Alat</th>
                    <th class="text-center">Jumlah</th>
                    <th>Tgl Sewa</th>
                    <th>Tgl Kembali</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rentals as $rental)
                    <tr>
                        <td>{{ $rentals->firstItem() + $loop->index }}</td>

                        @if ($isAdmin)
                            <td>
                                {{ $rental->user->name }}<br>
                                <small class="text-muted">{{ $rental->user->email }}</small>
                            </td>
                        @endif

                        <td class="fw-semibold">{{ $rental->equipment->nama }}</td>
                        <td class="text-center">{{ $rental->jumlah }}</td>
                        <td>{{ $rental->tgl_sewa->format('d/m/Y') }}</td>
                        <td>{{ $rental->tgl_kembali->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($rental->total_harga, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $rental->status_badge }}">{{ ucfirst($rental->status) }}</span></td>

                        <td class="text-end text-nowrap">
                            @if ($isAdmin)
                                @if ($rental->status === 'pending')
                                    @include('rentals._status-button', ['rental' => $rental, 'status' => 'disetujui', 'label' => 'Setujui', 'class' => 'btn-primary'])
                                @endif
                                @if ($rental->status === 'disetujui')
                                    @include('rentals._status-button', ['rental' => $rental, 'status' => 'selesai', 'label' => 'Dikembalikan', 'class' => 'btn-success'])
                                @endif
                                @if (in_array($rental->status, ['pending', 'disetujui']))
                                    @include('rentals._status-button', ['rental' => $rental, 'status' => 'dibatalkan', 'label' => 'Batalkan', 'class' => 'btn-outline-danger', 'confirm' => 'Batalkan transaksi ini? Stok akan dikembalikan.'])
                                @endif
                                @if (in_array($rental->status, ['selesai', 'dibatalkan']))
                                    <span class="text-muted small">-</span>
                                @endif
                            @else
                                @if ($rental->status === 'pending')
                                    @include('rentals._status-button', ['rental' => $rental, 'status' => 'dibatalkan', 'label' => 'Batalkan', 'class' => 'btn-outline-warning', 'confirm' => 'Batalkan pengajuan sewa ini?'])
                                @endif

                                @if (in_array($rental->status, ['pending', 'dibatalkan']))
                                    <form action="{{ route('rentals.destroy', $rental->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus transaksi ini dari riwayat?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    @if ($rental->status !== 'pending')
                                        <span class="text-muted small">-</span>
                                    @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 9 : 8 }}" class="text-center py-5 text-muted">
                            <div class="display-6">📭</div>
                            Belum ada transaksi sewa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $rentals->links() }}
</div>
@endsection
