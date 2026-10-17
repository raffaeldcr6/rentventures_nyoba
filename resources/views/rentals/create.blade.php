@extends('layouts.app')

@section('title', 'Sewa Alat')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4"><i class="bi bi-cart-plus text-success"></i> Form Penyewaan Alat</h4>

                @if ($equipments->isEmpty())
                    <div class="text-center py-4">
                        <div class="display-5">😕</div>
                        <p class="text-muted mt-2">Saat ini semua alat sedang tidak tersedia (stok habis).</p>
                        <a href="{{ route('equipments.index') }}" class="btn btn-outline-success">Kembali ke Katalog</a>
                    </div>
                @else
                    <form action="{{ route('rentals.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="equipment_id" class="form-label">Pilih Alat</label>
                            <select id="equipment_id" name="equipment_id"
                                    class="form-select @error('equipment_id') is-invalid @enderror">
                                <option value="">-- Pilih alat --</option>
                                @foreach ($equipments as $equipment)
                                    <option value="{{ $equipment->id }}"
                                            data-harga="{{ $equipment->harga_sewa_per_hari }}"
                                            data-stok="{{ $equipment->stok }}"
                                        {{ (string) old('equipment_id', $selected) === (string) $equipment->id ? 'selected' : '' }}>
                                        {{ $equipment->nama }} - Rp {{ number_format($equipment->harga_sewa_per_hari, 0, ',', '.') }}/hari (stok {{ $equipment->stok }})
                                    </option>
                                @endforeach
                            </select>
                            @error('equipment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (unit)</label>
                            <input type="number" id="jumlah" name="jumlah" min="1" value="{{ old('jumlah', 1) }}"
                                   class="form-control @error('jumlah') is-invalid @enderror">
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tgl_sewa" class="form-label">Tanggal Sewa</label>
                                <input type="date" id="tgl_sewa" name="tgl_sewa" min="{{ date('Y-m-d') }}"
                                       value="{{ old('tgl_sewa') }}"
                                       class="form-control @error('tgl_sewa') is-invalid @enderror">
                                @error('tgl_sewa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
                                <input type="date" id="tgl_kembali" name="tgl_kembali"
                                       value="{{ old('tgl_kembali') }}"
                                       class="form-control @error('tgl_kembali') is-invalid @enderror">
                                @error('tgl_kembali')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-success d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calculator"></i> Estimasi total biaya</span>
                            <strong id="estimasi">-</strong>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Ajukan Sewa
                            </button>
                            <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Hitung estimasi total biaya secara langsung (hanya tampilan; perhitungan resmi tetap di server)
    (function () {
        var alat = document.getElementById('equipment_id');
        var jumlah = document.getElementById('jumlah');
        var sewa = document.getElementById('tgl_sewa');
        var kembali = document.getElementById('tgl_kembali');
        var hasil = document.getElementById('estimasi');

        if (!alat) { return; }

        function hitung() {
            var opt = alat.options[alat.selectedIndex];
            var harga = parseInt(opt ? opt.getAttribute('data-harga') : 0, 10) || 0;
            var qty = parseInt(jumlah.value, 10) || 0;

            if (!harga || !qty || !sewa.value || !kembali.value) {
                hasil.textContent = '-';
                return;
            }

            var hari = Math.round((new Date(kembali.value) - new Date(sewa.value)) / 86400000);
            if (hari < 1) {
                hasil.textContent = 'Tanggal kembali harus setelah tanggal sewa';
                return;
            }

            hasil.textContent = 'Rp ' + (harga * qty * hari).toLocaleString('id-ID') + ' (' + hari + ' hari)';
        }

        [alat, jumlah, sewa, kembali].forEach(function (el) {
            el.addEventListener('input', hitung);
            el.addEventListener('change', hitung);
        });
        hitung();
    })();
</script>
@endpush
