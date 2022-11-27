<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketProdukMaterial extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'kode' => 10,
            'nama_material' => 10,
            'panjang' => 10,
            'warna' => 5,
            'satuan' => 5,
            'tipe' => 10,
        ],
    ];

    protected $fillable = [
        'paket_produk_id', 'material_id', 'kode', 'nama_material', 'warna', 'panjang', 'satuan', 'tipe', 'created_id', 'updated_id',
    ];

    protected $table = 'paket_produk_material';

    public function paket_produk(){
        return $this->belongsTo(PaketProduk::class, 'paket_produk_id', 'id');
    }
}
