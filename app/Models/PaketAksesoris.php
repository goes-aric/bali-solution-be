<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketAksesoris extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'nama_paket' => 10,
            'keterangan' => 10,
            'minimal_lebar' => 10,
            'maksimal_lebar' => 5,
            'minimal_tinggi' => 10,
            'maksimal_tinggi' => 5,
            'jumlah_daun' => 5,
        ],
    ];

    protected $fillable = [
        'nama_paket', 'keterangan', 'minimal_lebar', 'maksimal_lebar', 'minimal_tinggi', 'maksimal_tinggi', 'jumlah_daun', 'created_id', 'updated_id',
    ];

    protected $table = 'paket_aksesoris';

    public function materials(){
        return $this->hasMany(PaketAksesorisMaterial::class, 'paket_aksesoris_id', 'id');
    }
}
