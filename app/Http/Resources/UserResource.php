<?php

namespace App\Http\Resources;

use Spatie\Permission\Models\Role;
use App\Http\Resources\NotificationResource;
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
            'name' => $this->user_profile->name,
            'lastname' => $this->user_profile->lastname,
            'email' => $this->email,
            'access_token' => $this->accessToken->access_token,
            'refresh_token' => $this->accessToken->refresh_token,
            'expires_at' => $this->accessToken->expires_at,
            'role' => 'Administrador',
            'notifications' => NotificationResource::collection($this->notifications),
        ];
    }
}
