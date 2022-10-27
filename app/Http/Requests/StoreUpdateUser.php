<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateUser extends FormRequest
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
        $uuid = $this->user;

        $rules = [
            'full_name' => ['required', 'string', 'min:3', 'max:100'],
            'short_name' => ['required', 'string', 'min:3', 'max:100'],
            'password' => ['required', 'min:4', 'max:20'],
            'cpf' => ['required', 'min:11', 'max:11', "unique:users,cpf,{$uuid},uuid"],
            'cell_phone' => ['required', 'min:11', 'max:11', "unique:users,cell_phone,{$uuid},uuid"],
            'password' => ['required', 'min:4', 'max:20'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$uuid},uuid"],
        ];

        if($this->method() == 'PUT'){
            $rules['password'] = ['nullable', 'min:4', 'max:20'];
        }

        return $rules;
    }
}
