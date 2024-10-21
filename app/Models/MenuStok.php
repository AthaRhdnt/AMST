<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuStok extends Model
{
    use HasFactory;

    protected $table = 'menu_stok';
    protected $fillable = ['id_menu', 'id_barang', 'jumlah'];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    public function stok()
    {
        return $this->belongsTo(Stok::class, 'id_barang');
    }
}
