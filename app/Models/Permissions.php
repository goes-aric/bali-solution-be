<?php
namespace App\Models;

use App\Models\BaseModel;

class Permissions extends BaseModel
{
    protected $searchable = [
        'columns' => [
            'permission_name' => 10,
            'key' => 10,
            'group_key' => 2,
            'master_group' => 5,
            'role_id' => 2,
        ],
    ];

    protected $fillable = [
        'permission_name', 'key', 'group_key', 'master_group', 'role_id',
    ];

    protected $table = 'permissions';
}
