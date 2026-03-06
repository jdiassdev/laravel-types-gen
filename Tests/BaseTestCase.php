<?php

namespace Jdiassdev\LaravelTypesGen\Tests;

use Orchestra\Testbench\TestCase; 
use Jdiassdev\LaravelTypesGen\LaravelTypesGenServiceProvider;

class BaseTestCase extends TestCase
{
    /**
     * Registra seu ServiceProvider da biblioteca no ambiente de teste.
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelTypesGenServiceProvider::class,
        ];
    }
}
