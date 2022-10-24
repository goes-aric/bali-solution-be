<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialKaca extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'kode' => 10,
            'nama_material' => 10,
            'panjang' => 10,
            'lebar' => 5,
            'tebal' => 5,
            'satuan' => 5,
            'warna' => 5,
            'gambar' => 5,
            'harga_beli' => 5,
            'harga_beli_sebelumnya' => 5,
            'harga_jual' => 5,
            'status' => 5,
        ],
    ];

    protected $fillable = [
        'kode', 'nama_material', 'panjang', 'lebar', 'tebal', 'satuan', 'warna', 'gambar', 'harga_beli', 'harga_beli_sebelumnya', 'harga_jual', 'status', 'used_status', 'created_id', 'updated_id',
    ];

    protected $table = 'material_kaca';
}
