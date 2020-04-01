<?php

namespace Wovosoft\LaravelPermissions\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelPermissions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-permissions';
    }
}
