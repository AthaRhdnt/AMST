<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStok extends Model
{
    use HasFactory;

    protected $table = 'riwayat_stok';
    protected $primaryKey = 'id_riwayat_stok';
    protected $fillable = ['id_transaksi', 'id_menu', 'id_barang', 'stok_awal','jumlah_pakai', 'keterangan', 'stok_akhir', 'created_at', 'updated_at'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    public function stok()
    {
        return $this->belongsTo(Stok::class, 'id_barang');
    }
}
