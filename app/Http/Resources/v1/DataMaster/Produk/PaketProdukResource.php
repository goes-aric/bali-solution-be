<?php

namespace App\Http\Resources\v1\DataMaster\Produk;

use Illuminate\Http\Resources\Json\JsonResource;

class PaketProdukResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'kategori_produk_id'    => $this->kategori_produk_id,
            'nama_kategori'         => $this->kategori->nama_kategori,
            'nama_paket_produk'     => $this->nama_paket_produk,
            'warna'                 => $this->warna,
            'satuan'                => $this->satuan,
            'gambar'                => $this->gambar ? asset('/media/images') . '/' . $this->gambar : null,
            'material_kaca'         => $this->whenLoaded('materialKaca'),
            'material_upvc'         => $this->whenLoaded('materialUpvc'),
            'aksesoris'             => $this->whenLoaded('aksesoris'),
            'created_user'          => $this->createdUser->name,
            'updated_user'          => $this->updatedUser->name ?? null,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
        ];
    }
}
