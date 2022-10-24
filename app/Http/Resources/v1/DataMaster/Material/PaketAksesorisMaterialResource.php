<?php
namespace App\Http\Resources\v1\DataMaster\Material;

use Illuminate\Http\Resources\Json\JsonResource;

class PaketAksesorisMaterialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'paket_aksesoris_id'    => $this->paket_aksesoris_id,
            'aksesoris_id'          => $this->aksesoris_id,
            'kode'                  => $this->kode,
            'nama_material'         => $this->nama_material,
            'tipe'                  => json_decode($this->tipe),
            'warna'                 => $this->warna,
            'satuan'                => $this->satuan,
            'qty'                   => $this->qty,
            'created_user'          => $this->createdUser->name,
            'updated_user'          => $this->updatedUser->name ?? null,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
        ];
    }
}
