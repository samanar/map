<?php

namespace Samanar\Map;

use Illuminate\Support\ServiceProvider;

class MapServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadDependencies()
            ->publishDependencies();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Samanar\Map\Controllers\MapController');
        $this->app->make('Samanar\Map\Controllers\UserMapController');

        // Register the service the package provides.
        $this->app->bind('map', function ($app) {
            return new Map;
        });
    }

    private function loadDependencies()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'map');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        return $this;
    }

    private function publishDependencies()
    {
        $this->publishes([
            __DIR__ . '/public/' => public_path(''),
        ]);
        $this->publishes([
            __DIR__ . '/config/map.php' => config_path('map.php'),
        ]);
    }
}
