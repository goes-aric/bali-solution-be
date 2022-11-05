<?php

namespace App\Http\Resources\v1\DataMaster\Material;

use Illuminate\Http\Resources\Json\JsonResource;

class AksesorisResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'kode'                  => $this->kode,
            'nama_material'         => $this->nama_material,
            'tipe'                  => json_decode($this->tipe),
            'satuan'                => $this->satuan,
            'warna'                 => $this->warna,
            'gambar'                => $this->gambar ? asset('/media/images') . '/' . $this->gambar : null,
            'harga_beli_terakhir'   => $this->harga_beli_terakhir,
            'harga_beli_sebelumnya' => $this->harga_beli_sebelumnya,
            'harga_jual'            => $this->harga_jual,
            'status'                => $this->status,
            'status_text'           => $this->status == 1 ? 'AKTIF' : 'TIDAK AKTIF',
            'created_user'          => $this->createdUser->name,
            'updated_user'          => $this->updatedUser->name ?? null,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
        ];
    }
}
