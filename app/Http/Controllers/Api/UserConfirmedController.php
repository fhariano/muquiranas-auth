<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateUser;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserConfirmedController extends Controller
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $identify
     * @return \Illuminate\Http\Response
     */
    public function updateCellConfirmed(Request $request, $identify)
    {
        $user = $this->model->where('uuid', $identify)->first();

        try {
            if ($user) {
                $user->update($request->all());
            } else {
                return ['error' => 99, 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            Log::channel('auth')->error("CELL-CONFIRMED ERROR: " . print_r($e->getMessage(), true));
            return ['error' => 1, 'message' => 'CELL-CONFIRMED ERROR: ' . $e->getMessage()];
        }


        return response()->json(['error' => false, 'message' => 'success',]);
    }
}
