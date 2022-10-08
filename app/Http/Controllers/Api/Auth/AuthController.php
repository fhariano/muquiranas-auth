<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthUser;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function auth(AuthUser $request)
    {
        $user = $this->model::where('email', $request->email)->firstOrFail();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $msg = ['EMAIL_NOT_FOUND'];
            if (!Hash::check($request->password, $user->password)) {
                $msg = ['INVALID_PASSWORD'];
            }
            // if(!$user->cell_confirmed){
            //     $msg = ['CELL_NOT_CONFIRMED'];
            // }
            throw ValidationException::withMessages($msg)->status(406);
        }

        // return $user->createToken($request->device_name)->plainTextToken;
        return (new UserResource($user))->additional([
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['logout' => 'success']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }
}
