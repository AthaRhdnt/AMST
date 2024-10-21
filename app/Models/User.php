<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = ['nama_user', 'id_role', 'username', 'password'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function outlets()
    {
        return $this->hasMany(Outlets::class, 'id_user');
    }
}
