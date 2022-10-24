<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriProduk extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'nama_kategori' => 10,
            'keterangan' => 10,
            'status' => 5,
        ],
    ];

    protected $fillable = [
        'nama_kategori', 'keterangan', 'status', 'used_status', 'created_id', 'updated_id',
    ];

    protected $table = 'kategori_produk';
}
