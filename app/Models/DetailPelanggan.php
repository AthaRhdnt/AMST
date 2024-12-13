<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPelanggan extends Model
{
    use HasFactory;

    protected $table = 'detail_pelanggan';
    protected $primaryKey = 'id_detail_pelanggan';
    protected $fillable = ['id_transaksi', 'nama_pelanggan'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
