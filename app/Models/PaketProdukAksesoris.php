<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketProdukAksesoris extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'nama_paket' => 10,
            'minimal_lebar' => 10,
            'maksimal_lebar' => 10,
            'minimal_tinggi' => 5,
            'maksimal_tinggi' => 5,
            'jumlah_daun' => 5,
        ],
    ];

    protected $fillable = [
        'paket_produk_id', 'paket_aksesoris_id', 'nama_paket', 'minimal_lebar', 'maksimal_lebar', 'minimal_tinggi', 'maksimal_tinggi', 'jumlah_daun', 'created_id', 'updated_id',
    ];

    protected $table = 'paket_produk_aksesoris';

    public function paket_produk(){
        return $this->belongsTo(PaketProduk::class, 'paket_produk_id', 'id');
    }
}
