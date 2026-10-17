<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman awal: arahkan sesuai status login
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('equipments.index')
        : redirect()->route('login');
})->name('home');

// ---------- Hanya untuk tamu (belum login) ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// ---------- Harus login ----------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // FITUR 1 - Katalog alat: semua user boleh melihat (Read)
    Route::resource('equipments', EquipmentController::class)->only(['index']);

    // FITUR 1 - Katalog alat: Create, Update, Delete khusus ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::resource('equipments', EquipmentController::class)
            ->except(['index', 'show']);
    });

    // FITUR 2 - Transaksi sewa: Read (admin lihat semua, penyewa lihat miliknya)
    // dan Update status (admin: setujui/selesai/batalkan, penyewa: batalkan miliknya)
    Route::resource('rentals', RentalController::class)
        ->only(['index', 'update']);

    // FITUR 2 - Transaksi sewa: Create & Delete khusus PENYEWA
    Route::middleware('role:penyewa')->group(function () {
        Route::resource('rentals', RentalController::class)
            ->only(['create', 'store', 'destroy']);
    });
});
