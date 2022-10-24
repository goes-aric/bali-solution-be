<?php
namespace App\Models;

use App\Models\BaseModel;

class KontakSupplier extends BaseModel
{
    protected $searchable = [
        'columns' => [
            'kontak_person' => 10,
            'no_telp' => 10,
            'email' => 10,
        ],
    ];

    protected $fillable = [
        'kontak_person', 'no_telp', 'email', 'created_id', 'updated_id',
    ];

    protected $table = 'kontak_supplier';

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
