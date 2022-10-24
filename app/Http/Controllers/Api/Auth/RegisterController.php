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
    protected $token;

    public function __construct(User $user)
    {
        $this->model = $user;
        $this->url = config('microservices.micro_notification.url');
        $this->http = Http::acceptJson();
        $this->token = $this->getToken();
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
            Log::channel('auth')->error("SMS SEND: " . $sendSms['message']);
            throw ValidationException::withMessages(['SMS_SEND_ERROR'])->status(406);
        } else {
            Log::channel('auth')->info("SMS SEND: SUCCESS!");
        }

        $email = $user->email;
        $subject = "[Muquirana's Bar] Código de Verificação: " . $data['confirmation_token'];
        $message = "Utilize o código de verificação: " . $data['confirmation_token'] . "<br>para confirmar seu e-mail e celular!";

        $sendEmail = $this->sendEmail($email, $subject, $message);
        if ($sendEmail['error'] == 1) {
            Log::channel('auth')->error("EMAIL SEND: " . $sendEmail['message']);
            throw ValidationException::withMessages(['EMAIL_SEND_ERROR'])->status(406);
        } else {
            Log::channel('auth')->info("EMAIL SEND: SUCCESS!");
        }

        return (new UserResource($user))->additional([
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function sendSMS($cellPhone, $message)
    {   
        Log::channel('notification')->info("SendSMS TOKEN: " . $this->token);
        
        Log::channel('notification')->info("SendSMS: " . $cellPhone . " - Message: " . $message);
        $receiver = '+55' . $cellPhone;
        try {
            $response = $this->http->withToken($this->token)->get($this->url . '/send-sms', [
                'to' => $receiver,
                'message' => $message,
            ]);
            Log::channel('auth')->info("SendSMS body: " . print_r($response->body(), true) . " - status code: " . $response->status());

            if ($response->status() > 299) {
                return ['error' => 1, 'message' => 'SEND SMS STATUS CODE: ' . $response->status()];
            }

            return ['error' => 0, 'message' => 'success'];
        } catch (Exception $e) {
            Log::channel('auth')->error("SMS ERROR: " . print_r($e->getMessage(), true));
            return ['error' => 1, 'message' => 'SEND SMS ERROR: ' . $e->getMessage()];
        }
    }

    public function sendEmail($email, $subject, $message)
    {      
        Log::channel('notification')->info("SendEMAIL TOKEN: " . $this->token);
        Log::channel('notification')->info("SendEMAIL: " . $email . " - Subject: " . $subject . " - Message: " . $message);
        try {
            $response = $this->http->withToken($this->token)->get($this->url . '/send-email', [
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
            ]);
            Log::channel('auth')->info("SendEMAIL body: " . print_r($response->body(), true) . " - status code: " . $response->status());
            
            if ($response->status() > 299) {
                return ['error' => 1, 'message' => 'SEND EMAIL STATUS CODE: ' . $response->status()];
            }

            return ['error' => 0, 'message' => 'success'];
        } catch (Exception $e) {
            Log::channel('auth')->error("EMAIL ERROR: " . print_r($e->getMessage(), true));
            return ['error' => 1, 'message' => 'SEND EMAIL ERROR: ' . $e->getMessage()];
        }
    }

    public function getToken() {
        Log::channel('auth')->info("TOKEN API: ".config('api.notification.email')." - password: " . 
            config('api.notification.password'). " - device: ".config('api.notification.device'));
        $result = $this->http->post($this->url . '/auth', [
            'email' => config('api.notification.email'),
            'password' => config('api.notification.password'),
            'device_name' => config('api.notification.device'),
        ]);
        Log::channel('auth')->info("TOKEN API: " . print_r($result->body(), true) . " - status code: " . $result->status());
        $body = json_decode($result->body());
        Log::channel('auth')->info("Access Token: " . $body->token);
        
        return $body->token;
    }
}
