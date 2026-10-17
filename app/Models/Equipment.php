<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    // WAJIB: kata "equipment" bersifat uncountable di Laravel, jadi nama tabel harus ditulis manual
    protected $table = 'equipments';

    protected $fillable = [
        'nama',
        'kategori',
        'harga_sewa_per_hari',
        'stok',
        'deskripsi',
        'foto',
    ];

    // Daftar kategori yang dipakai di form & filter katalog
    public const KATEGORI = [
        'Tenda',
        'Tas & Carrier',
        'Perlengkapan Tidur',
        'Alat Masak',
        'Penerangan',
        'Lainnya',
    ];

    // Satu alat bisa muncul di banyak transaksi sewa
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    // URL foto untuk ditampilkan di Blade: {{ $equipment->foto_url }}
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
