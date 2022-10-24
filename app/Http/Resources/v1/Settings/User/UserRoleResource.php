<?php
namespace App\Http\Resources\v1\Settings\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'role_name' => $this->whenNotNull($this->roles->role_name),
        ];
    }
}
