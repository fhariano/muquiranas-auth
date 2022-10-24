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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Twilio\Rest\Client;

class RegisterController extends Controller
{
    protected $model;
    protected $url;
    protected $http;

    public function __construct(User $user)
    {
        $this->model = $user;
        $this->url = config('microservices.micro_notification.url');
        $this->http = Http::acceptJson();
    }

    public function store(StoreUser $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $data['confirmation_token'] = (string) random_int(1000, 9999);

        Log::channel('auth')->info("request: " . print_r($data, true));

        $user = $this->model->create($data);

        if ($data['device_name'] == 'mobile') {
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

        Log::channel('auth')->info("User: " . $user->email . " - CONFIRMATION_TOKEM: " . $user->confirmation_token);
        $message = "Código: " . $data['confirmation_token'] . " - Muquiranas Bar";
        $sendSms = $this->sendSMS($data['cell_phone'], $message);
        
        if ($sendSms['error'] == 1) {
            Log::channel('auth')->error("SMS SEND: ERROR!");
            throw ValidationException::withMessages(['SMS_SEND_ERROR'])->status(406);
        } else {
            Log::channel('auth')->info("SMS SEND: SUCCESS!");
        }
        
        return (new UserResource($user))->additional([
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function sendSMS($cellPhone, $message)
    {
        Log::channel('auth')->info("SendSMS: " . $cellPhone . " - Message: " . $message);
        $receiver = '+55' . $cellPhone;
        try {

            $response = $this->http->get($this->url . '/send-sms', [
                'to' => $receiver,
                'message' => $message,
            ]);
            // return response()->json(json_decode($response->body()), $response->status());

            return ['error' => 0, 'message' => 'success'];
        } catch (Exception $e) {
            Log::channel('auth')->error("SMS ERROR: " . print_r($e->getMessage(), true));
            return ['error' => 1, 'message' => 'SEND SMS ERROR: ' . $e->getMessage()];
        }
    }
}
