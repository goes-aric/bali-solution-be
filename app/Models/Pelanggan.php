<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'nama_pelanggan' => 10,
            'alamat' => 10,
            'no_telp' => 10,
            'email' => 5,
            'status' => 5,
        ],
    ];

    protected $fillable = [
        'nama_pelanggan', 'alamat', 'no_telp', 'email', 'status', 'created_id', 'updated_id',
    ];

    protected $table = 'pelanggan';
}
