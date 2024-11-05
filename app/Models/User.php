<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = ['nama_user', 'id_role', 'username', 'password'];
    protected $hidden = ['password', 'remember_token'];
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Check for the lowest available id_user to reuse
            $nextId = DB::table('users')
                        ->whereNotIn('id_user', function($query) {
                            $query->select('id_user')->from('users');
                        })
                        ->select(DB::raw('MIN(id_user + 1) as next_id'))
                        ->value('next_id');

            // If a gap is found, use it; otherwise, auto-increment
            $user->id_user = $nextId ?? (DB::table('users')->max('id_user') + 1);
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function outlets()
    {
        return $this->hasMany(Outlets::class, 'id_user');
    }
}
