<?php
namespace App\Http\Resources\v1\Settings\Perusahaan;

use Illuminate\Http\Resources\Json\JsonResource;

class PerusahaanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'nama_perusahaan'   => $this->nama_perusahaan,
            'alamat'            => $this->alamat,
            'nomor_legalitas'   => $this->nomor_legalitas,
            'no_telp'           => $this->no_telp,
            'website'           => $this->website,
            'email'             => $this->email,
            'logo'              => $this->logo ? asset('/media/images') . '/' . $this->logo : null,
            'created_user'      => $this->createdUser->nama_pengguna,
            'updated_user'      => $this->updatedUser->nama_pengguna ?? null,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
