<?php
namespace App\Http\Resources\v1\DataMaster\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;

class KontakSupplierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'supplier_id'       => $this->supplier_id,
            'kontak_person'     => $this->kontak_person,
            'no_telp'           => $this->no_telp,
            'email'             => $this->email
        ];
    }
}
