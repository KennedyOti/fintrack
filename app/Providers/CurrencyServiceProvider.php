<?php

namespace App\Providers;

use App\Helpers\CurrencyHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class CurrencyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register class alias for global access without namespace
        $this->app->alias(CurrencyHelper::class, 'CurrencyHelper');
        
        $this->app->singleton(CurrencyHelper::class, function () {
            return new CurrencyHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directive for currency formatting
        Blade::directive('currency', function ($expression) {
            return "<?php echo \\App\\Helpers\\CurrencyHelper::format{$expression}; ?>";
        });

        Blade::directive('currencysymbol', function ($expression) {
            return "<?php echo \\App\\Helpers\\CurrencyHelper::getSymbol{$expression}; ?>";
        });

        Blade::directive('currencycode', function () {
            return "<?php echo \\App\\Helpers\\CurrencyHelper::getUserCurrency(); ?>";
        });
    }
}
