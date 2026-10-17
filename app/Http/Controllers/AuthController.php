<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ---------- LOGIN ----------
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        try {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                return redirect()->intended(route('equipments.index'))
                    ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput($request->only('email'))
                ->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi nanti.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    // ---------- REGISTER (selalu menjadi role "penyewa") ----------
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => ['nullable', 'regex:/^[0-9+]{9,15}$/'],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain.',
            'phone.regex' => 'No. HP harus berupa 9-15 digit angka.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => 'penyewa', // role TIDAK diambil dari input agar tidak bisa dimanipulasi
            ]);

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('equipments.index')
                ->with('success', 'Registrasi berhasil. Selamat datang di RentVentures!');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Registrasi gagal diproses. Silakan coba lagi.');
        }
    }

    // ---------- LOGOUT ----------
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
