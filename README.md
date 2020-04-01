<p align="center">
  <a href="https://wovosoft.com/">
    <img src="https://github.com/wovosoft/bootstrap-vue-font-awesome-picker/blob/master/wovosoft.png" alt="Wovosoft Software Development Compnay"  height="72">
  </a>
</p>

# Laravel Role & Permissions Front-End Implementation using [spatie/laravel-permission](https://github.com/spatie/laravel-permission)


[![Latest Stable Version](https://poser.pugx.org/wovosoft/laravel-permissions/v/stable)](https://packagist.org/packages/wovosoft/laravel-permissions) [![Total Downloads](https://poser.pugx.org/wovosoft/laravel-permissions/downloads)](https://packagist.org/packages/wovosoft/laravel-permissions) [![Latest Unstable Version](https://poser.pugx.org/wovosoft/laravel-permissions/v/unstable)](https://packagist.org/packages/wovosoft/laravel-permissions) [![License](https://poser.pugx.org/wovosoft/laravel-permissions/license)](https://packagist.org/packages/wovosoft/laravel-permissions)

## Package description
The package is a Front-End Implementationf of the [spatie/laravel-permission](https://github.com/spatie/laravel-permission) package. The [spatie/laravel-permission](https://github.com/spatie/laravel-permission)
is an awesome package for managing Roles & Permissionf for Laravel applications out of the box. But currently it doesn't have the front-end to easily deploy in your application.

> This package comes to solve this problem. The package implements almost every features provided by [spatie/laravel-permission](https://github.com/spatie/laravel-permission).

## Features

- Vue Components for each possible features .
- Components are reusable. So, the default layout can be modified according to the needs of your application.
- Currently the front-end uses Bootstrap-Vue. But you can easily change it's layout.
- The front-end vue components are packaged as an npm package. You can use it as a module for you bundlers eg. Webpack,
- Check the main [spatie/laravel-permission](https://github.com/spatie/laravel-permission) for more details.

## Installation

Install via composer

```bash
composer require wovosoft/laravel-permissions
```

### Publish Configuration File

1. Publish the configuration file.

```bash
php artisan vendor:publish --provider="Wovosoft\LaravelPermissions\ServiceProvider" --tag="config"
```

2. Publish the Vue Components. The Published components will be copied to `resources/laravel-permissions/permissions` folder. You need to add the `Main.vue` component to your `app.js`

```bash
php artisan vendor:publish --provider="Wovosoft\LaravelPermissions\ServiceProvider" --tag="resources"
```

3. Publish the Migrations

```bash
php artisan vendor:publish --provider="Wovosoft\LaravelPermissions\ServiceProvider" --tag="migrations"
```

3. Publish the Seeds

```bash
php artisan vendor:publish --provider="Wovosoft\LaravelPermissions\ServiceProvider" --tag="seeds"
```

## Configuration

1. In `App\User.php` model add the `HasRoles.php` Trait.

```php
//other imports goes here
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // other codes goes here
}
```

2. Now Run

```bash
php artisan migrate
```

3. Go to `config/laravel-permissions.php` . Add Default Permissions and Roles.

```php
<?php

return [
    "route_name_prefix" => "Wovosoft",
    "route_url_prefix" => "backend",
    "middleware" => ["web", "auth"],
    "users_table" => "users",                //Default Laravel Generated Name
    "roles_table" => config("permission.table_names.roles"),                //comes from spatie config file.
    "permissions_table" => config("permission.table_names.permissions"),    //comes from spatie config file
    "default_permissions" => [
        [
            "name" => "add user",
            "description" => "Can Add User"
        ],
        [
            "name" => "edit user",
            "description" => "Can Edit User"
        ],
        [
            "name" => "delete user",
            "description" => "Can Delete User"
        ]
    ],
    "default_roles" => [
        [
            "name" => "Super Admin",
            "description" => "Super Admin Manages Everything"
        ],
        [
            "name" => "User",
            "description" => "User Role"
        ],
        [
            "name" => "Customer",
            "description" => "Customer Role"
        ]
    ]
];

```

4. The package adds the routes automatically prefixed by `backend`, so your other routes should't be prefixed by `backend`. But you can change it in `config/laravel-permissions.php` config file. To check the registered routes, run in your terminal from project the root,

```bash
php artisan route:list
```

5. The gates are automatically registered during boot-up by [spatie/laravel-permission](https://github.com/spatie/laravel-permission)

## Usage
1. So, according to `config/laravel-permissions.php` (#3) you can perform user abilities as follows:

```php
if(auth()->can('permission')){
  echo "Auth user is allowed to perform this operation";
}
```

```php
if(App\User::find(1)->can('permission')){
  echo "Auth user is allowed to perform this operation";
}
```

```php
$role = Wovosoft\LaravelPermissions\Models\Roles::find(1);
if($role->hasAbility('permission')){
  echo "The Role with ID 1 is allowed to perform this operation";
}
```

2. Check the main [spatie/laravel-permission](https://github.com/spatie/laravel-permission) for more details.

### Note

> Please keep in mind, the default Role and Permission models provided by [spatie/laravel-permission](https://github.com/spatie/laravel-permission) is extended in the package. That's why rather than using `Spatie\Permission\Models\Role` for Role and `Spatie\Permission\Models\Permission` please use `Wovosoft\LaravelPermissions\Models\Roles` for Role and `Wovosoft\LaravelPermissions\Models\Permissions` for Permission respectively.

## Security

If you discover any security related issues, please email narayanadhikary24@gmail.com
ore create issue in the Github Repository.

## Credits

- [Narayan Adhikary](https://github.com/wovosoft/laravel-permissions)
- [All contributors](https://github.com/wovosoft/laravel-permissions/graphs/contributors)

This package is bootstrapped with the help of
[wovosoft/crud](https://github.com/wovosoft/crud).
