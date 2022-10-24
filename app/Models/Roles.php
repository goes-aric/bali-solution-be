<?php
namespace App\Models;

use App\Models\BaseModel;
use App\Models\Permissions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles extends BaseModel
{
    use SoftDeletes;

    protected $searchable = [
        'columns' => [
            'role_name' => 10,
            'description' => 10,
        ],
    ];

    protected $fillable = [
        'role_name', 'description', 'created_id', 'updated_id',
    ];

    protected $table = 'roles';

    public function permissions(){
        return $this->hasMany(Permissions::class, 'role_id', 'id');
    }
}
