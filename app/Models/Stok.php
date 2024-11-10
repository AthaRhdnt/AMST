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
    protected $fillable = ['nama_barang', 'jumlah_barang'];

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

    protected function updateJumlahBarang($id_barang)
    {
        // Calculate the total jumlah from all outlets
        $totalJumlah = StokOutlet::where('id_barang', $id_barang)
            ->sum('jumlah');
        
        // Update the jumlah_barang in the Stok table
        $stok = Stok::find($id_barang);
        $stok->update(['jumlah_barang' => $totalJumlah]);
    }
}
