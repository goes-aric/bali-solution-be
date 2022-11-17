<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketProduk extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'kategori_produk_id' => 10,
            'nama_paket_produk' => 10,
            'warna' => 10,
            'satuan' => 5,
        ],
    ];

    protected $fillable = [
        'kategori_produk_id', 'nama_paket_produk', 'warna', 'satuan', 'gambar', 'created_id', 'updated_id',
    ];

    protected $table = 'paket_produk';

    public function kategori(){
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id', 'id');
    }

    public function materialKaca(){
        return $this->hasMany(PaketProdukMaterialKaca::class, 'paket_produk_id', 'id');
    }

    public function materialUpvc(){
        return $this->hasMany(PaketProdukMaterialUpvc::class, 'paket_produk_id', 'id');
    }

    public function aksesoris(){
        return $this->hasMany(PaketProdukAksesoris::class, 'paket_produk_id', 'id');
    }
}
