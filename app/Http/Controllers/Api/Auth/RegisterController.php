<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPermissionsUser;
use App\Http\Requests\Auth\StoreUser;
use App\Http\Resources\UserResource;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function store(StoreUser $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $data['sms_token'] = (string) random_int(1000, 9999);

        Log::channel('auth')->info("request: " . print_r($data, true));
        
        $user = $this->model->create($data);
        
        if($data['device_name'] == 'mobile'){
            Log::channel('auth')->info("Permissons User!");
            $permissionsCustomer = [
                'visualizar_bares',
                'visualizar_bar',
                'visualizar_categorias',
                'visualizar_categoria',
                'visualizar_produtos',
                'visualizar_produto',
                'visualizar_favoritos',
                'visualizar_favorito',
                'editar_favorito',
                'apagar_favorito',
                'visualizar_ordens',
                'visualizar_ordem',
                'editar_ordem',
                'listar_bandeiras',
                'processar_pagamento',
                'salvar_cartao',
                'recuperar_cartao',
            ];

            $permissions = Permission::select('id')->whereIn('name', $permissionsCustomer)->orderBy('id', 'asc')->get();
            // Log::channel('auth')->info("Permissons Ids: " . print_r($permissions->toArray(), true));

            $user->permissions()->sync($permissions);
        }

        return (new UserResource($user))->additional([
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }
}
