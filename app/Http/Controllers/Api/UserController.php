<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateUser;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->model->with('permissions')->paginate();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateUser $request)
    {
        $data = $request->validated();

        // Encrypting password
        $data['password'] = bcrypt($data['password']);

        $user = $this->model->create($data);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $identify
     * @return \Illuminate\Http\Response
     */
    public function show($identify)
    {
        $user = $this->model->with('permissions')->where('uuid', $identify)->firstOrFail();

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $identify
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateUser $request, $identify)
    {
        Log::channel('auth')->info("request: " . print_r($request, true));
        
        $identify = $this->model->where('uuid', $identify)->firstOrFail();

        $data = $request->validated();

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $identify->update($data);

        return response()->json(['updated' => 'success',]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $identify
     * @return \Illuminate\Http\Response
     */
    public function destroy($identify)
    {
        $user = $this->model->where('uuid', $identify)->firstOrFail();

        $user->delete();

        return response()->json(['deleted' => 'success',]);
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

    public function updateAddress(Request $request, $identify)
    {
        Log::channel('auth')->info("UpdateAddress: " . print_r($request->all(), true));
        $user = $this->model->where('uuid', $identify)->first();
        
      
        try {
            if ($user) {
                $user->street = '{$request->street}';
                $user->number = '{$request->number}';
                $user->complement = '{$request->complement}';
                $user->district = '{$request->district}';
                $user->city = '{$request->city}';
                $user->state = '{$request->state}';
                $user->country = '{$request->country}';
            } else {
                return ['error' => 99, 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            Log::channel('auth')->error("UpdateAddress ERROR: " . print_r($e->getMessage(), true));
            return ['error' => 1, 'message' => 'UpdateAddress ERROR: ' . $e->getMessage()];
        }
    }
}
