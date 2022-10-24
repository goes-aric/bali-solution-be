<?php
namespace App\Http\Resources\v1\Settings\User;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'role_name'     => $this->role_name,
            'description'   => $this->description,
            'permissions'   => $this->permissions,
            'created_user'  => $this->createdUser->nama_pengguna,
            'updated_user'  => $this->updatedUser->nama_pengguna ?? null,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'deleted_at'    => $this->deleted_at
        ];
    }
}
