<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    protected $fillable = ['id_kategori', 'nama_menu', 'harga_menu'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_menu');
    }

    public function menuStok()
    {
        return $this->hasMany(MenuStok::class, 'id_menu');
    }
}
