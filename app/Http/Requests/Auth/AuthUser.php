<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $apikeys = config('acl.apiKeys');
        $rules = [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:4', 'max:20'],
            'device_name' => ['required', 'string', 'max:200'],
            'apikey' => ['required', 'string', 'max:50', Rule::in($apikeys['web'], $apikeys['mobile'])],
        ];

        return $rules;
    }
}
