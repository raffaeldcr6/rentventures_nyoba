<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Agar tampilan pagination ($items->links()) memakai gaya Bootstrap 5
        Paginator::useBootstrapFive();
    }
}
