<?php

namespace Wovosoft\LaravelPermissions;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/laravel-permissions.php';


    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => config_path('laravel-permissions.php'),
            ], 'config');
            $this->publishes([
                __DIR__ . "/../resources" => resource_path('wovosoft/laravel-permissions'),
            ], 'resources');

            $this->publishes([
                __DIR__ . "/../database/migrations" => database_path('migrations'),
            ], 'migrations');

            $this->publishes([
                __DIR__ . "/Seeds" => database_path('migrations/seeds'),
            ], 'seeds');
        }


        $this->loadMigrationsFrom(__DIR__ . "/../database/migrations");
        $this->loadRoutesFrom(__DIR__ . "/routes.php");
//        $this->loadFactoriesFrom(__DIR__ . "/../database/factories");
//        $this->loadViewsFrom(__DIR__ . "/../resources/views", 'LaravelPermissions');
//        $this->loadTranslationsFrom(__DIR__ . "/../resources/lang", 'LaravelPermissions');
        //$this->loadJsonTranslationsFrom(__DIR__."/../resources/lang");
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'laravel-permissions'
        );

        $this->app->bind('laravel-permissions', function () {
            return new LaravelPermissions();
        });
    }
}
