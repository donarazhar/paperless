<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Import model & policy
use App\Models\Letter;
use App\Policies\LetterPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan mapping model → policy
        Gate::policy(Letter::class, LetterPolicy::class);
    }
}
