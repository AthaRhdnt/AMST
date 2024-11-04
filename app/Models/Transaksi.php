<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $fillable = ['id_outlet', 'kode_transaksi', 'tanggal_transaksi', 'total_transaksi'];
    protected $casts = [
        'tanggal_transaksi' => 'date',
    ];

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'id_transaksi');
    }

    public function laporan()
    {
        return $this->hasOne(Laporan::class, 'id_transaksi');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlets::class, 'id_outlet');
    }
}
