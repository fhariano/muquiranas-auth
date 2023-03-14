<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'identify' => $this->uuid,
            'full_name' => $this->full_name,
            'short_name' => $this->short_name,
            'cpf' => $this->cpf,
            'cell_phone' => $this->cell_phone,
            'email' => $this->email,
            'confirmation_token' => $this->confirmation_token,
            'cell_confirmed' => $this->cell_confirmed,
            'postal_code' => $this->postal_code,
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'district' => $this->district,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
