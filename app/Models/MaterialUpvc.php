<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialUpvc extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'tipe' => 10,
            'kode' => 10,
            'nama_material' => 10,
            'panjang' => 10,
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
        'tipe', 'kode', 'nama_material', 'panjang', 'satuan', 'warna', 'gambar', 'harga_beli', 'harga_beli_sebelumnya', 'harga_jual', 'status', 'used_status', 'created_id', 'updated_id',
    ];

    protected $table = 'material_upvc';
}
