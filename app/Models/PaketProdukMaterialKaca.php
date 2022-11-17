<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketProdukMaterialKaca extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'kode' => 10,
            'nama_material' => 10,
            'panjang' => 10,
            'lebar' => 10,
            'tebal' => 10,
            'satuan' => 10,
            'tipe' => 5,
        ],
    ];

    protected $fillable = [
        'paket_produk_id', 'aksesoris_id', 'kode', 'nama_material', 'panjang', 'lebar', 'tebal', 'satuan', 'tipe', 'created_id', 'updated_id',
    ];

    protected $table = 'paket_produk_material_kaca';

    public function paket_produk(){
        return $this->belongsTo(PaketProduk::class, 'paket_produk_id', 'id');
    }
}
