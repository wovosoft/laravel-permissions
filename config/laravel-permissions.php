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
