<?php

namespace Wovosoft\LaravelPermissions\Models;

use App\User;

use Spatie\Permission\Models\Role;
use Wovosoft\LaravelPermissions\Traits\ExceptionHandlerTrait;

class Roles extends Role
{
    use ExceptionHandlerTrait;
    protected $fillable = ["name", "description", "guard_name", "created_at", "updated_at"];

    /**
     * @param $users
     * @return bool
     * @throws \Exception
     */
    public function assignUsers($users)
    {
        try {
            if ($users && is_array($users)) {
                foreach ($users as $user) {
                    $item = User::findOrFail($user);
                    if ($item) {
                        if (!$item->assignRole($this)) {
                            return false;
                        }
                    }
                }
                return true;
            } elseif ($users) {
                $item = User::findOrFail($users);
                return $item->assignRole($this);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
