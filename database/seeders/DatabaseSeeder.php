<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Akun dummy ----------
        User::updateOrCreate(
            ['email' => 'admin@rentventures.test'],
            [
                'name' => 'Admin RentVentures',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'penyewa@rentventures.test'],
            [
                'name' => 'Budi Penyewa',
                'phone' => '089876543210',
                'role' => 'penyewa',
                'password' => Hash::make('password'),
            ]
        );

        // ---------- Katalog alat camping ----------
        $alat = [
            ['Tenda Dome 4 Orang', 'Tenda', 50000, 5, 'Tenda double layer anti air, muat 4 orang, mudah dipasang.'],
            ['Tenda Dome 2 Orang', 'Tenda', 35000, 6, 'Tenda ringan untuk 2 orang, cocok untuk pendakian santai.'],
            ['Carrier 60 Liter', 'Tas & Carrier', 30000, 8, 'Carrier 60L dengan back system nyaman dan rain cover.'],
            ['Sleeping Bag Polar', 'Perlengkapan Tidur', 15000, 10, 'Sleeping bag hangat berbahan polar, nyaman untuk suhu dingin.'],
            ['Matras Gulung', 'Perlengkapan Tidur', 8000, 12, 'Matras gulung ringan sebagai alas tidur di dalam tenda.'],
            ['Kompor Portable + Gas', 'Alat Masak', 20000, 7, 'Kompor lipat portable lengkap dengan 1 tabung gas.'],
            ['Nesting Cooking Set', 'Alat Masak', 15000, 6, 'Satu set panci dan wajan lipat untuk memasak di gunung.'],
            ['Headlamp LED', 'Penerangan', 10000, 10, 'Senter kepala LED terang dengan 3 mode cahaya.'],
        ];

        foreach ($alat as [$nama, $kategori, $harga, $stok, $deskripsi]) {
            Equipment::firstOrCreate(
                ['nama' => $nama],
                [
                    'kategori' => $kategori,
                    'harga_sewa_per_hari' => $harga,
                    'stok' => $stok,
                    'deskripsi' => $deskripsi,
                ]
            );
        }
    }
}
