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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'cell_phone' => $this->cell_phone,
            'email' => $this->email,
            'sms_confirmed' => $this->sms_confirmed,
            'email_confirmed' => $this->email_confirmed,
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
