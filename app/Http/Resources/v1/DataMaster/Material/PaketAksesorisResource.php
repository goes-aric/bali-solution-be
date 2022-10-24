<?php

namespace App\Http\Resources\v1\DataMaster\Material;

use Illuminate\Http\Resources\Json\JsonResource;

class PaketAksesorisResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'nama_paket'        => $this->nama_paket,
            'keterangan'        => $this->keterangan,
            'minimal_lebar'     => $this->minimal_lebar,
            'maksimal_lebar'    => $this->maksimal_lebar,
            'minimal_tinggi'    => $this->minimal_tinggi,
            'maksimal_tinggi'   => $this->maksimal_tinggi,
            'jumlah_daun'       => $this->jumlah_daun,
            'created_user'      => $this->createdUser->name,
            'updated_user'      => $this->updatedUser->name ?? null,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
