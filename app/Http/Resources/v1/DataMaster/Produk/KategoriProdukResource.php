<?php

namespace App\Http\Resources\v1\DataMaster\Produk;

use Illuminate\Http\Resources\Json\JsonResource;

class KategoriProdukResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'nama_kategori' => $this->nama_kategori,
            'keterangan'    => $this->keterangan,
            'status'        => $this->status,
            'status_text'   => $this->status == 1 ? 'AKTIF' : 'TIDAK AKTIF',
            'created_user'  => $this->createdUser->name,
            'updated_user'  => $this->updatedUser->name ?? null,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
