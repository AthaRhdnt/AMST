<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';
    protected $fillable = ['id_transaksi', 'total_penjualan'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
