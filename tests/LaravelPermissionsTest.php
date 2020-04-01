<?php

namespace Wovosoft\LaravelPermissions\Tests;

use Wovosoft\LaravelPermissions\Facades\LaravelPermissions;
use Wovosoft\LaravelPermissions\ServiceProvider;
use Orchestra\Testbench\TestCase;

class LaravelPermissionsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'laravel-permissions' => LaravelPermissions::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
