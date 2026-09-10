<?php

namespace App\Providers;

use App\Models\Producto;
use App\Policies\InventarioPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Producto::class, InventarioPolicy::class);
    }
}
