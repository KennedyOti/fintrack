<?php

namespace App\Providers;

use App\Helpers\CurrencyHelper;
use Illuminate\Support\ServiceProvider;

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
        // Create class alias so CurrencyHelper can be used without namespace in views
        if (!class_exists('CurrencyHelper')) {
            class_alias(CurrencyHelper::class, 'CurrencyHelper');
        }
    }
}
