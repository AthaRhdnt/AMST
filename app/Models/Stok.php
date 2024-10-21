<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';
    protected $primaryKey = 'id_barang';
    protected $fillable = ['nama_barang', 'jumlah_barang'];

    public function menuStok()
    {
        return $this->hasMany(MenuStok::class, 'id_barang');
    }

    public function stokOutlet()
    {
        return $this->hasMany(StokOutlet::class, 'id_barang');
    }
}
