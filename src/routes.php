<?php

use Illuminate\Support\Facades\Route;

//MAPPING_AREA_FOR_CRUD_DO_NOT_REMOVE_OR_EDIT_THIS_LINE_USE_AREA//


Route::name(config("laravel-permissions.route_name_prefix") . '.')
    ->prefix(config("laravel-permissions.route_url_prefix"))
    ->middleware(config("laravel-permissions.middleware"))
    ->group(function () {

        Wovosoft\LaravelPermissions\Http\Controllers\UserController::routes();
        Wovosoft\LaravelPermissions\Http\Controllers\RoleController::routes();
        Wovosoft\LaravelPermissions\Http\Controllers\PermissionController::routes();
        //MAPPING_AREA_FOR_CRUD_DO_NOT_REMOVE_OR_EDIT_THIS_LINE//
    });
