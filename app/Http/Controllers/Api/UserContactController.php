<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateUser;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserContactController extends Controller
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function updateAddress(Request $request, $identify)
    {
        Log::channel('auth')->info("UpdateAddress: " . print_r($request->all(), true));
        $user = $this->model->where('uuid', $identify)->first();
        
        
        try {
            if ($user) {
                $user->postal_code = addslashes($request->postal_code);
                $user->street = addslashes($request->street);
                $user->number = addslashes($request->number);
                $user->complement = addslashes($request->complement);
                $user->district = addslashes($request->district);
                $user->city = addslashes($request->city);
                $user->state = addslashes($request->state);
                $user->country = addslashes($request->country);
                $user->save();
            } else {
                return ['error' => 99, 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            Log::channel('auth')->error("UpdateAddress ERROR: " . print_r($e->getMessage(), true));
            return ['error' => 1, 'message' => 'UpdateAddress ERROR: ' . $e->getMessage()];
        }
        
        return response()->json(['error' => false, 'message' => 'success',]);
    }
}
