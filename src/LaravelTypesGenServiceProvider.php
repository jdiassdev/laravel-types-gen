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
        }
    }

    public function register()
    {
        //
    }
}
