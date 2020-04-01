<?php

namespace Wovosoft\LaravelPermissions\Traits;

use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Wovosoft\LaravelPermissions\Models\Permissions;
use Wovosoft\LaravelPermissions\Models\Roles;

trait CommonFunctionsTrait
{
    public function initializeCommonFunctionsTrait()
    {

    }

    /**
     * Assign a permission to a Role
     * @param Request $request [permission_id, role_id]
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRoleToPermission(Request $request)
    {
        if ($request->has('permission_id') && $request->has('role_id')) {
            try {
                $permission = Permissions::find($request->post('permission_id'));
                $role = Roles::find($request->post('role_id'));
                if ($permission && $role) {
                    if ($role->givePermissionTo($permission)) {
                        return response()->json([
                            "status" => true,
                            "title" => "Success!",
                            "msg" => "Successfully Done",
                            "type" => "success"
                        ]);
                    }
                }
                return response()->json([
                    "status" => false,
                    "title" => "Failed!",
                    "msg" => "Operation Failed",
                    "type" => "danger"
                ], 404);
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

    /**
     * Assign a Permission to a User
     * @param Request $request [permission_id, user_id]
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignUserToPermission(Request $request)
    {
        if ($request->has('permission_id') && $request->has('user_id')) {
            try {
                $permission = ItemModel::find($request->post('permission_id'));
                $user = User::find($request->post('user_id'));
                if ($permission && $user) {
                    if ($user->givePermissionTo($permission)) {
                        return response()->json([
                            "status" => true,
                            "title" => "Success!",
                            "msg" => "Successfully Done",
                            "type" => "success"
                        ]);
                    }
                }
                return response()->json([
                    "status" => false,
                    "title" => "Failed!",
                    "msg" => "Operation Failed",
                    "type" => "danger"
                ], 404);
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

    /**
     * Revoke A Permission from A Role
     * @param Request $request [permission_id, role_id]
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokePermissionToRole(Request $request)
    {
        try {
            $permission = Permissions::findById($request->post("permission_id"));
            $role = Roles::find($request->post("role_id"));

            if ($permission && $role) {
                $role->revokePermissionTo($permission);
                return response()->json([
                    "msg" => "Success",
                    "title" => "Successfully Done",
                    "type" => "primary"
                ]);
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

    /**
     *  Revoke A Permission From a User
     * @param Request $request [permission_id, user_id]
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokePermissionToUser(Request $request)
    {
        try {
            $permission = Permissions::findById($request->post("permission_id"));
            $user = User::find($request->post("user_id"));

            if ($permission && $user) {
                $user->revokePermissionTo($permission);
                return response()->json([
                    "msg" => "Success",
                    "title" => "Successfully Done",
                    "type" => "primary"
                ]);
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

    /**
     * @param Request $request [user_id, role_id]
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeRoleFromUser(Request $request)
    {
        try {
            $user = User::findOrFail($request->post("user_id"));
            if (!$user) {
                return response()->json([
                    "status" => false,
                    "title" => 'Failed!',
                    "type" => "warning",
                    "msg" => "User Not Found",
                    "line" => __LINE__,
                    "file" => __FILE__
                ], 404);
            }
            $role = Roles::findOrFail($request->post("role_id"));
            if (!$role) {
                return response()->json([
                    "status" => false,
                    "title" => 'Failed!',
                    "type" => "warning",
                    "msg" => "Role Not Found",
                    "line" => __LINE__,
                    "file" => __FILE__
                ], 404);
            }
            if (!$user->hasRole($role)) {
                return response()->json([
                    "status" => false,
                    "title" => 'Failed!',
                    "type" => "warning",
                    "msg" => "The Role is not assigned to this User",
                    "line" => __LINE__,
                    "file" => __FILE__
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            $user->removeRole($role);
            return response()->json([
                "status" => true,
                "title" => 'Success',
                "type" => "primary",
                "msg" => "Successfully Detached"
            ]);
        } catch (\Exception $exception) {
            return $this->handleExceptions($exception);
        }
    }

    public function delete(Request $request)
    {
        if ($request->has('id') && $request->post('id')) {
            try {
                $item = Permissions::find($request->post('id'));
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
}
