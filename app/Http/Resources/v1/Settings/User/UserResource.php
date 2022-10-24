<?php
namespace App\Http\Resources\v1\Settings\User;

use Carbon\Carbon;
use App\Http\Resources\v1\Settings\User\UserRoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'nama_pengguna'     => $this->nama_pengguna,
            'username'          => $this->username,
            'email'             => $this->email,
            'status'            => $this->status,
            'status_text'       => $this->status == 1 ? 'AKTIF' : 'TIDAK AKTIF',
            'tanggal_berhenti'  => $this->tanggal_berhenti,
            'is_admin'          => $this->is_admin,
            'last_visit'        => $this->last_visit ? Carbon::createFromDate($this->last_visit)->diffForHumans() : '',
            'roles'             => UserRoleResource::collection($this->whenLoaded('roles')),
        ];
    }
}
