<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPermissionsUser;
use App\Http\Requests\Auth\StoreUser;
use App\Http\Resources\UserResource;
use App\Models\Permission;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Twilio\Rest\Client;

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
        $data['email_token'] = (string) random_int(1000, 9999);

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
              
        $resSMS = $this->sendSMS($data['sms_token']);

        if($resSMS['error'] == 1){
            throw ValidationException::withMessages(['SMS_SEND_ERROR'])->status(406);
        }

        return (new UserResource($user))->additional([
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function sendSMS($code)
    {
        $receiver = '+5511996204924'; // EU
        // $receiver = '+5511997465440'; // Paulinho
        // $receiver = '+5511991175420'; // Bassi
        $code = random_int(1000, 9999);
        $message = 'Codigo: ' . $code. ' - Muquiranas Bar' ;

        try {
            $accound_id = getenv('TWILIO_ACCOUNT_SID');
            $auth_token = getenv('TWILIO_AUTH_TOKEN');
            $twilio_number = getenv('TWILIO_FROM');

            $client = new Client($accound_id, $auth_token);
            $client->messages->create(
                $receiver,
                array(
                    'from' => $twilio_number,
                    'body' => $message
                )
            );

            return ['error' => 0, 'message' => 'success'];
        } catch (Exception $e) {
            return ['error' => 1, 'message' => 'SEND SMS ERROR: '. $e->getMessage()];
        }
    }
}
