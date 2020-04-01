<?php


namespace Wovosoft\LaravelPermissions\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Wovosoft\LaravelPermissions\Builders\QueryBuilder;
use App\User as ItemModel;
use Wovosoft\LaravelPermissions\Models\Permissions;
use Wovosoft\LaravelPermissions\Models\Roles;
use Wovosoft\LaravelPermissions\Traits\ExceptionHandlerTrait;

class UserController extends Controller
{
    use ExceptionHandlerTrait;

    public static function routes()
    {
        Route::post("users/list", '\\' . __CLASS__ . '@list')->name('Users.List');
        Route::post("users/search", '\\' . __CLASS__ . '@search')->name('Users.Search');
        Route::post("users/store", '\\' . __CLASS__ . '@store')->name('Users.Store');
        Route::post("users/delete", '\\' . __CLASS__ . '@delete')->name('Users.Delete');
        Route::post("users/listByRole", '\\' . __CLASS__ . '@listByRole')->name('Users.ListByRole');
        Route::post("users/data", '\\' . __CLASS__ . '@data')->name('Users.Data');
        Route::post("users/directPermissions", '\\' . __CLASS__ . '@directPermissions')->name('Users.directPermissions');
        Route::post("users/abilities/check", '\\' . __CLASS__ . '@checkAbilities')->name('Users.Abilities.Check');
    }

    public function store(Request $request)
    {
        try {
            $item = ItemModel::findOrNew($request->post("id"));
            $item->email = $request->post("email");
            $item->name = $request->post("name");
            if ($request->post("password")) {
                $item->password = Hash::make($request->post("password"));
            }
            if ($item->save()) {
                if ($request->has("roles") && $request->post("roles")) {
                    $item->assignRole($request->post("roles"));
                }
                if ($request->has("direct_permissions") && $request->post("direct_permissions")) {
                    $item->syncPermissions($request->post("direct_permissions"));
                }
            }
            return response()->json([
                "status" => true,
                "title" => 'SUCCESS!',
                "type" => "success",
                "msg" => ($request->post('id') ? 'Edited' : 'Added') . ' Successfully'
            ]);
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function data(Request $request)
    {
        $output = [];
        if ($request->has("roles") && $request->post("roles")) {
            $output["roles"] = Roles::select($request->post("roles"))->get();
        }
        if ($request->has("permissions") && $request->post("permissions")) {
            $output["permissions"] = Permissions::select($request->post("permissions"))->get();
        }
        if ($request->has("direct_permissions") && $request->post("direct_permissions")) {
            $item = ItemModel::findOrFail($request->post("direct_permissions"));
            $output["direct_permissions"] = $item ? $item->getDirectPermissions()->pluck("name") : [];
        }
        return response()->json($output);
    }

    public function directPermissions(Request $request)
    {
        try {
            $item = ItemModel::findOrFail($request->post("id"));

            if ($item) {
                return $item->getDirectPermissions();
            }
            return response()->json([
                "status" => false,
                "title" => 'Failed!',
                "type" => "warning",
                "msg" => "Role Note Found"
            ], 404);
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function listByRole(Request $request)
    {
        try {
            $role = Roles::findOrFail($request->post("role_id"));

            if ($role) {
                return $role->users()->paginate();
            }
            return response()->json([
                "status" => false,
                "title" => 'Failed!',
                "type" => "warning",
                "msg" => "Role Note Found"
            ], 404);
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function list(Request $request)
    {
        try {
            $table = config("laravel-permissions.users_table");
            $items = (new QueryBuilder(ItemModel::class))
                ->select(["*"])
                ->with(["roles:id,name", "permissions:id,name"]);
            return response()->json(
                $items->datatable([
                    "filter" => $request->post("filter") ?? null,
                    "per_page" => $request->post("per_page") ?? 10,
                    "orderBy" => $request->post("orderBy") ?? "id",
                    "order" => $request->post("order") ?? "asc",
                    "columns" => ($request->has("columns") && $request->post("columns")) ?
                        $request->post("columns") : [
                            "$table.name",
                            "$table.email",
                            "$table.created_at",
                            "$table.updated_at",
                        ]
                ])
            );
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function search(Request $request)
    {
        try {
            $items = ItemModel::query()
                ->select([
                    "*"
                ]);
            if (!$request->post('all')) {
                $items->where("id", $request->post("search"))
                    ->orWhere("name", "LIKE", "%" . $request->post("search") . "%")
                    ->orWhere("email", "LIKE", "%" . $request->post("search") . "%");
            }
            return response()->json(
                $items->get()
            );
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function delete(Request $request)
    {
        if ($request->has('id') && $request->post('id')) {
            try {
                $item = ItemModel::find($request->post('id'));
                if ($item->delete()) {
                    return response()->json([
                        "status" => true,
                        "title" => "Success!",
                        "msg" => "Deleted Successfully",
                        "type" => "success"
                    ]);
                }
            } catch (\Exception $exception) {
                return $this->handleExceptions($exception);
            }
        }
        return response()->json([
            "status" => false,
            "title" => "Failed!",
            "msg" => "Item ID is not valid",
            "type" => "danger"
        ], 404);
    }

    public function checkAbilities(Request $request)
    {
        $user = ItemModel::find($request->post("user"));
        return collect($request->post("permissions"))
            ->map(function ($permission) use ($user) {
                return [
                    "permission" => $permission,
                    "ability" => $user->can($permission)
                ];
            });
    }
}
