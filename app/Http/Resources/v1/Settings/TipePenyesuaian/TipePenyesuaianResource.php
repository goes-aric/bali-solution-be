<?php

namespace App\Http\Resources\v1\Settings\TipePenyesuaian;

use Illuminate\Http\Resources\Json\JsonResource;

class TipePenyesuaianResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'deskripsi'     => $this->deskripsi,
            'posisi'        => $this->posisi,
            'created_user'  => $this->createdUser->name,
            'updated_user'  => $this->updatedUser->name ?? null,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
