<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StokOutlet;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';
    protected $primaryKey = 'id_barang';
    protected $fillable = ['nama_barang', 'minimum'];

    public function menuStok()
    {
        return $this->hasMany(MenuStok::class, 'id_barang');
    }

    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'menu_stok', 'id_barang', 'id_menu')
                    ->withPivot('jumlah');
    }

    public function stokOutlet()
    {
        return $this->hasMany(StokOutlet::class, 'id_barang');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'id_barang');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'id_barang');
    }
}
