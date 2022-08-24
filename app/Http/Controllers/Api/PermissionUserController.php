<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPermissionsUser;
use App\Http\Resources\PermissionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionUserController extends Controller
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function permissonsUser($identify)
    {
        $user = $this->model->where('uuid', $identify)
            ->with('permissions')
            ->firstOrFail();

        return  PermissionResource::collection($user->permissions);
    }

    public function addPermissonsUser(AddPermissionsUser $request)
    {
        if (Gate::denies('add_permissions_user')) {
            return abort(403, 'Not Authrorized');
        }

        $user = $this->model->where('uuid', $request->user)->firstOrFail();

        // if ($user) {
        //     return response()->json(['message' => 'updated']);
        //     $user->permissions()->syncWithoutDetaching([
        //         $request->user()->id => ['updated_at' => now()]
        //     ]);
        // } else {
        //     return response()->json(['message' => 'created']);
        //     $user->permissions()->syncWithoutDetaching([
        //         $request->user()->id => ['created_at' => now()]
        //     ]);
        // }

        $user = $user->permissions()->sync($request->permissions);

        return response()->json(['message' => 'success']);
    }

    public function removePermissonsUser(Request $request)
    {
        $user = $this->model->where('uuid', $request->user)->firstOrFail();

        $user->permissions()->detach($request->permissions);

        return response()->json(['message' => 'success']);
    }

    public function userHasPermission(Request $request, $permission)
    {
        $user = $request->user();

        if (!$user->isSuperAdmin() && !$user->hasPermisson($permission)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['message' => 'success']);
    }
}
