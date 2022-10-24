<?php
namespace App\Models;

use App\Models\BaseModel;

class Perusahaan extends BaseModel
{
    protected $searchable = [
        'columns' => [
            'nama_perusahaan' => 10,
            'alamat' => 10,
            'nomor_legalitas' => 10,
            'no_telp' => 5,
            'website' => 5,
            'email' => 5,
        ],
    ];

    protected $fillable = [
        'nama_perusahaan', 'alamat', 'nomor_legalitas', 'no_telp', 'website', 'email', 'logo', 'created_id', 'updated_id',
    ];

    protected $table = 'perusahaan';
}
