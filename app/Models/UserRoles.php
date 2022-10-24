<?php
namespace App\Models;

use App\Models\User;
use App\Models\Roles;
use App\Models\BaseModel;

class UserRoles extends BaseModel
{
    protected $with = [
        'user', 'roles'
    ];

    protected $fillable = [
        'user_id', 'role_id',
    ];

    protected $table = 'user_roles';

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function roles(){
        return $this->belongsTo(Roles::class, 'role_id', 'id');
    }
}
