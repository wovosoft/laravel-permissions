<?php


namespace Wovosoft\LaravelPermissions\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Wovosoft\LaravelPermissions\Builders\QueryBuilder;
use Wovosoft\LaravelPermissions\Models\Permissions as ItemModel;
use Wovosoft\LaravelPermissions\Traits\CommonFunctionsTrait;
use Wovosoft\LaravelPermissions\Traits\ExceptionHandlerTrait;

class PermissionController extends Controller
{
    use ExceptionHandlerTrait, CommonFunctionsTrait;

    public static function routes()
    {
        Route::post("permissions/list", '\\' . __CLASS__ . '@list')->name('Permissions.List');
        Route::post("permissions/search", '\\' . __CLASS__ . '@search')->name('Permissions.Search');
        Route::post("permissions/store", '\\' . __CLASS__ . '@store')->name('Permissions.Store');
        Route::post("permissions/delete", '\\' . __CLASS__ . '@delete')->name('Permissions.Delete');
        Route::post("permissions/users", '\\' . __CLASS__ . '@users')->name('Permissions.Users');
        Route::post("permissions/roles", '\\' . __CLASS__ . '@roles')->name('Permissions.Roles');
        Route::post("permissions/revokePermissionToUser", '\\' . __CLASS__ . '@revokePermissionToUser')->name('Permissions.RevokePermissionToUser');
        Route::post("permissions/revokePermissionToRole", '\\' . __CLASS__ . '@revokePermissionToRole')->name('Permissions.revokePermissionToRole');
        Route::post("permissions/assignRoleToPermission", '\\' . __CLASS__ . '@assignRoleToPermission')->name('Permissions.assignRoleToPermission');
        Route::post("permissions/assignUserToPermission", '\\' . __CLASS__ . '@assignUserToPermission')->name('Permissions.assignUserToPermission');
    }

    public function store(Request $request)
    {
        try {
            $item = ItemModel::updateOrCreate(["id" => $request->post("id")], [
                "name" => $request->post("name"),
                "guard_name" => $request->post("guard_name") ?? "web",
                "description" => $request->post("description")
            ]);
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


    public function users(Request $request)
    {
        try {
            $item = ItemModel::findById($request->post("permission_id"));
            if ($item) {
                return $item->users()->paginate();
            }
            return response()->json([
                "msg" => "Not Found",
                "title" => "Failed",
                "type" => "danger"
            ], 404);
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function roles(Request $request)
    {
        try {
            $item = ItemModel::findById($request->post("permission_id"));
            if ($item) {
                return $item->roles()->paginate();
            }
            return response()->json([
                "msg" => "Not Found",
                "title" => "Failed",
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
            $table = config("permission.table_names.permissions");
=======
            $table = config('permission.table_names.permissions');
>>>>>>> 72863d9ffa8fbe754213bb60634512258bc05810
            $items = (new QueryBuilder(ItemModel::class))
                ->select([
                    "$table.*"
                ]);
            return response()->json(
                $items->datatable([
                    "filter" => $request->post("filter") ?? null,
                    "per_page" => $request->post("per_page") ?? 10,
                    "orderBy" => $request->post("orderBy") ?? "id",
                    "order" => $request->post("order") ?? "asc",
                    "columns" => [
                        "$table.name",
                        "$table.guard_name",
                        "$table.created_at",
                        "$table.updated_at",
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
                ->select(["*"]);
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
}
