<?php


namespace Wovosoft\LaravelPermissions\Http\Controllers;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Wovosoft\LaravelPermissions\Builders\QueryBuilder;
use Wovosoft\LaravelPermissions\Models\Roles as ItemModel;
use Wovosoft\LaravelPermissions\Traits\CommonFunctionsTrait;
use Wovosoft\LaravelPermissions\Traits\ExceptionHandlerTrait;

class RoleController extends Controller
{
    use ExceptionHandlerTrait, CommonFunctionsTrait;

    public static function routes()
    {
        Route::post("roles/list", '\\' . __CLASS__ . '@list')->name('Roles.List');
        Route::post("roles/search", '\\' . __CLASS__ . '@search')->name('Roles.Search');
        Route::post("roles/store", '\\' . __CLASS__ . '@store')->name('Roles.Store');
        Route::post("roles/delete", '\\' . __CLASS__ . '@delete')->name('Roles.Delete');
        Route::post("roles/detuch", '\\' . __CLASS__ . '@removeRoleFromUser')->name('Roles.RemoveRoleFromUser');
        Route::post("roles/permissions", '\\' . __CLASS__ . '@permissions')->name('Roles.Permissions');
        Route::post("roles/abilities/check", '\\' . __CLASS__ . '@checkAbilities')->name('Roles.CheckAbilities');
    }

    public function store(Request $request)
    {
        try {
            $item = ItemModel::updateOrCreate(["id" => $request->post("id")], [
                "name" => $request->post("name"),
                "guard_name" => $request->post("guard_name") ?? "web",
                "description" => $request->post("description")
            ]);
            if ($item && $request->has("users_insert")) {
                $assigned = $item->assignUsers($request->post("users_insert"));
            }
            if ($item && $request->has("permissions_sync") && $request->post("permissions_sync")) {
                $item->syncPermissions($request->post("permissions_sync"));
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


    public function permissions(Request $request)
    {
        try {
            $item = ItemModel::find($request->post("id"));
            if ($item) {
                return $item->permissions->pluck("name");
            }
            return response()->json([
                "status" => false,
                "msg" => "Not Found",
                "type" => "danger"
            ], 404);
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function list(Request $request)
    {
        try {
<<<<<<< HEAD
            $roles_table = config("permission.table_names.roles");
=======
            $roles_table = config('permission.table_names.roles');
>>>>>>> 72863d9ffa8fbe754213bb60634512258bc05810
            $items = (new QueryBuilder(ItemModel::class))
                ->select([
                    "$roles_table.*"
                ])
                ->with(["permissions:id,name,description"]);

            //->with(["users"]); //it might have lots of users. so not a good idea to grab all, rather paginate is better
            return response()->json(
                $items->datatable([
                    "filter" => $request->post("filter") ?? null,
                    "per_page" => $request->post("per_page") ?? 10,
                    "orderBy" => $request->post("orderBy") ?? "id",
                    "order" => $request->post("order") ?? "asc",
                    "columns" => [
                        "$roles_table.name",
                        "$roles_table.description",
                        "$roles_table.guard_name",
                        "$roles_table.created_at",
                        "$roles_table.updated_at",
                    ]
                ]),
                200, [],
                JSON_PRETTY_PRINT
            )->header("SQL", $items->toSql());
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
                    ->orWhere("description", "LIKE", "%" . $request->post("search") . "%");
            }
            return response()->json(
                $items->get()
            );
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }
    public function checkAbilities(Request $request)
    {
        $role = ItemModel::find($request->post("role"));
        return collect($request->post("permissions"))
            ->map(function ($permission) use ($role) {
                return [
                    "permission" => $permission,
                    "ability" => $role->hasPermissionTo($permission)
                ];
            });
    }
}
