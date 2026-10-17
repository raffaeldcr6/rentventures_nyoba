<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware agar bisa dipanggil ->middleware('role:admin') di routes/web.php
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /*
         * Jaring pengaman terakhir: semua error (404, 500, dll.) ditampilkan lewat
         * halaman kustom di resources/views/errors/, BUKAN halaman debug Laravel
         * (Whoops/Ignition), walaupun APP_DEBUG=true.
         * Detail error asli tetap tercatat di storage/logs/laravel.log
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            // Biarkan Laravel menangani sendiri validasi (redirect back + pesan error) dan redirect login
            if ($e instanceof ValidationException
                || $e instanceof AuthenticationException
                || $e instanceof HttpResponseException) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {
                switch ($e->getStatusCode()) {
                    case 404: // termasuk ModelNotFoundException (findOrFail)
                    case 405: // misal membuka URL POST lewat browser (GET)
                        return response()->view('errors.404', [], 404);

                    case 419: // token CSRF kedaluwarsa
                        return redirect()->back()
                            ->with('error', 'Sesi Anda telah habis. Silakan coba lagi.');

                    case 403:
                        return redirect('/')
                            ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                }
            }

            // Error lainnya (query gagal, bug kode, dsb.)
            return response()->view('errors.500', [], 500);
        });
    })
    ->create();
