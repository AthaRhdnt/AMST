<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailTransaksi;

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

    public function getUsedTodayAttribute()
    {
        return DetailTransaksi::whereHas('menu', function ($query) {
            $query->whereHas('menuStok', function($q) {
                $q->where('id_barang', $this->id_barang);
            });
        })
        ->whereHas('transaksi', function ($query) {
            $query->whereDate('tanggal_transaksi', today());
        })
        ->sum('jumlah'); 
    }

    public function getRemainingStockAttribute()
    {
        return $this->jumlah_barang - $this->used_today;
    }
}
