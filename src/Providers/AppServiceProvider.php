<?php

namespace Sas\ShopwareAppLaravelSdk\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/sw-app.php', 'sw-app');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishConfigs();
        $this->loadRoutes();
        $this->loadDatabase();
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    private function loadDatabase(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    private function publishConfigs(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sw-app.php' => App::configPath('sw-app.php'),
            ], 'sw-app');
        }
    }
}
