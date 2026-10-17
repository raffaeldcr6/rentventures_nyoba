<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Satu user (penyewa) punya banyak transaksi sewa
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPenyewa(): bool
    {
        return $this->role === 'penyewa';
    }
}
