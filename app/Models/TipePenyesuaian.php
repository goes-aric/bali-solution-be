<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipePenyesuaian extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'deskripsi' => 10,
            'posisi' => 10,
        ],
    ];

    protected $fillable = [
        'deskripsi', 'posisi', 'created_id', 'updated_id',
    ];

    protected $table = 'tipe_penyesuaian';
}
