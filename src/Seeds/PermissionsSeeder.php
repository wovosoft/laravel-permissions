<?php

namespace Wovosoft\LaravelPermissions\Seeds;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Wovosoft\LaravelPermissions\Models\Permissions;
use Wovosoft\LaravelPermissions\Models\Roles;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {
//            // Call the php artisan migrate:refresh
//            $this->command->call('migrate:refresh');
//            $this->command->warn("Data cleared, starting from blank database.");
//        }

        // Seed the default permissions
        $permissions = config("laravel-permissions.default_permissions");
        $roles = config("laravel-permissions.default_roles");


        $demo_permissions = [];
        foreach (["user", "post", "send sms", "articles", "boost", "advertise"] as $p) {
            foreach (["add", "edit", "delete", "view"] as $s) {
                $demo_permissions[] = [
                    "name" => "$s $p",
                    "description" => "$s $p",
                ];
            }
        }

        foreach ($demo_permissions as $permission) {
            Permissions::firstOrCreate($permission);
        }

        $this->command->info('Default Permissions added.');

        foreach ($roles as $role) {
            $role = Roles::firstOrCreate($role);

            if ($role->name == 'Super Admin') {
                // assign all permissions
                $role->syncPermissions(Permissions::all());
                $this->command->info('Super Admin granted all the permissions');
            } else {
                // for others by default only read access
                $role->syncPermissions(Permissions::where('name', 'LIKE', 'view_%')->get());
            }

            // create one user for each role
            $this->createUser($role);
        }

        // Reset cached roles and permissions
        $this->command->info("\nWe are flushing the new records from database to the cache.\n");
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info("\nCreating 50 demo users without roles and permissions.\n");
        factory(User::class,50)->create();
        $this->command->warn('All done :)');
    }

    /**
     * Create a user with given role
     *
     * @param $role
     */
    private function createUser($role)
    {
        if ($role->name == 'Super Admin') {
            $user = factory(User::class)->create([
                "name" => "Super Admin",
                "email" => "superadmin@gmail.com",
                "password" => Hash::make("superadmin123456789")
            ]);
            $user->assignRole($role->name);
        } else {
            $user = factory(User::class)->create();
            $user->assignRole($role->name);
        }
    }
}
