<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    protected $fillable = [
        'user_id',
        'equipment_id',
        'jumlah',
        'tgl_sewa',
        'tgl_kembali',
        'total_harga',
        'status',
    ];

    protected $casts = [
        'tgl_sewa' => 'date',
        'tgl_kembali' => 'date',
    ];

    public const STATUS = ['pending', 'disetujui', 'selesai', 'dibatalkan'];

    // Transaksi dimiliki oleh satu penyewa
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Transaksi menyewa satu jenis alat
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    // Warna badge Bootstrap sesuai status: {{ $rental->status_badge }}
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'disetujui' => 'bg-primary',
            'selesai' => 'bg-success',
            'dibatalkan' => 'bg-secondary',
            default => 'bg-light text-dark',
        };
    }
}
