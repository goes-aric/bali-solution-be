<?php
namespace App\Models;

use App\Models\User;
use Nicolaslopezj\Searchable\SearchableTrait;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BaseModel extends Model
{
    use HasApiTokens, SearchableTrait, Notifiable;

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_id', 'id');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_id', 'id');
    }
}
