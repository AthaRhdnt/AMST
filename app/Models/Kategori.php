<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    protected $fillable = ['nama_kategori'];

    public function menu()
    {
        return $this->hasMany(Menu::class, 'id_kategori');
    }
}
