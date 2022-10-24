<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketAksesorisMaterial extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'kode' => 10,
            'nama_material' => 10,
            'tipe' => 10,
            'warna' => 5,
            'satuan' => 5,
            'qty' => 5,
        ],
    ];

    protected $fillable = [
        'paket_aksesoris_id', 'aksesoris_id', 'kode', 'nama_material', 'tipe', 'warna', 'satuan', 'qty', 'created_id', 'updated_id',
    ];

    protected $table = 'paket_aksesoris_material';

    protected $casts = [
        'tipe' => 'array',
    ];

    public function paket_aksesoris(){
        return $this->belongsTo(PaketAksesoris::class, 'paket_aksesoris_id', 'id');
    }
}
