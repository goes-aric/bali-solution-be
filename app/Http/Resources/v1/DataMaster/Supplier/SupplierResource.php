<?php

namespace App\Http\Resources\v1\DataMaster\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'nama_supplier' => $this->nama_supplier,
            'alamat'        => $this->alamat,
            'kota'          => $this->kota,
            'kode_pos'      => $this->kode_pos,
            'created_user'  => $this->createdUser->name,
            'updated_user'  => $this->updatedUser->name ?? null,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
