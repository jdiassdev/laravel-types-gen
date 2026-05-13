<?php

namespace Jdiassdev\LaravelTypesGen;

use Illuminate\Support\ServiceProvider;
use Jdiassdev\LaravelTypesGen\Commands\GenerateRequestTypesCommand;

class LaravelTypesGenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRequestTypesCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/laravel-types-gen.php' => config_path('laravel-types-gen.php'),
            ], 'laravel-types-gen-config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-types-gen.php',
            'laravel-types-gen'
        );
    }
}
