<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'nama_supplier' => 10,
            'alamat' => 10,
            'kota' => 10,
            'kode_pos' => 5,
        ],
    ];

    protected $fillable = [
        'nama_supplier', 'alamat', 'kota', 'kode_pos', 'created_id', 'updated_id',
    ];

    protected $table = 'supplier';

    public function kontak(){
        return $this->hasMany(KontakSupplier::class, 'supplier_id', 'id');
    }
}
