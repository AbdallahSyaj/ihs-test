<?php

namespace App\Providers;

use App\Contracts\Catalog\StorefrontCatalogProvider;
use App\Services\Catalog\DatabaseStorefrontCatalogProvider;
use App\Services\Catalog\JsonStorefrontCatalogProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StorefrontCatalogProvider::class, function ($app): StorefrontCatalogProvider {
            $driver = (string) config('catalog.driver', 'json');

            return match ($driver) {
                'json' => $app->make(JsonStorefrontCatalogProvider::class),
                'database' => $app->make(DatabaseStorefrontCatalogProvider::class),
                default => throw new InvalidArgumentException('Unsupported catalog driver ['.$driver.'].'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
        });
    }

}
