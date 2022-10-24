<?php

namespace App\Http\Resources\v1\DataMaster\Pelanggan;

use Illuminate\Http\Resources\Json\JsonResource;

class PelangganResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'nama_pelanggan'    => $this->nama_pelanggan,
            'alamat'            => $this->alamat,
            'no_telp'           => $this->no_telp,
            'email'             => $this->email,
            'status'            => $this->status == 1 ? 'AKTIF' : 'TIDAK AKTIF',
            'created_user'      => $this->createdUser->name,
            'updated_user'      => $this->updatedUser->name ?? null,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
