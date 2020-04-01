<?php

namespace Wovosoft\LaravelPermissions\Models;

use Spatie\Permission\Models\Permission;

class Permissions extends Permission
{
    protected $fillable = ["name", "guard_name", "created_at", "updated_at"];
}
