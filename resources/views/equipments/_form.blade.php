{{--
    Form bersama untuk create & edit alat.
    Variabel: $kategoriList, $equipment (null saat create)
--}}
<div class="mb-3">
    <label for="nama" class="form-label">Nama Alat</label>
    <input type="text" id="nama" name="nama" value="{{ old('nama', $equipment->nama ?? '') }}"
           class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: Tenda Dome 4 Orang">
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="kategori" class="form-label">Kategori</label>
    <select id="kategori" name="kategori" class="form-select @error('kategori') is-invalid @enderror">
        <option value="">-- Pilih kategori --</option>
        @foreach ($kategoriList as $kategori)
            <option value="{{ $kategori }}"
                {{ old('kategori', $equipment->kategori ?? '') === $kategori ? 'selected' : '' }}>
                {{ $kategori }}
            </option>
        @endforeach
    </select>
    @error('kategori')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="harga_sewa_per_hari" class="form-label">Harga Sewa per Hari (Rp)</label>
        <input type="number" id="harga_sewa_per_hari" name="harga_sewa_per_hari" min="0"
               value="{{ old('harga_sewa_per_hari', $equipment->harga_sewa_per_hari ?? '') }}"
               class="form-control @error('harga_sewa_per_hari') is-invalid @enderror">
        @error('harga_sewa_per_hari')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="stok" class="form-label">Stok (unit)</label>
        <input type="number" id="stok" name="stok" min="0"
               value="{{ old('stok', $equipment->stok ?? '') }}"
               class="form-control @error('stok') is-invalid @enderror">
        @error('stok')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="deskripsi" class="form-label">Deskripsi <span class="text-muted">(opsional)</span></label>
    <textarea id="deskripsi" name="deskripsi" rows="3"
              class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $equipment->deskripsi ?? '') }}</textarea>
    @error('deskripsi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="foto" class="form-label">Foto Alat <span class="text-muted">(opsional, maks 2 MB)</span></label>

    @if (! empty($equipment?->foto_url))
        <div class="mb-2">
            <img src="{{ $equipment->foto_url }}" alt="Foto saat ini" class="rounded border"
                 style="max-height: 120px;">
            <div class="form-text">Foto saat ini. Pilih file baru hanya jika ingin menggantinya.</div>
        </div>
    @endif

    <input type="file" id="foto" name="foto" accept="image/*"
           class="form-control @error('foto') is-invalid @enderror">
    @error('foto')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
