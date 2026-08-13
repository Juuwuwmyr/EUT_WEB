<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix MySQL key length error for older MySQL versions
        Schema::defaultStringLength(191);

        // ── Audit logging via the Auditable trait ──────────────────────────
        // The trait hooks (bootAuditable) are automatically called by Laravel
        // when the model boots, so no explicit Observer registration is needed.
        // Models using the trait: Order, User, MenuItem, Category, Rider
    }
}
