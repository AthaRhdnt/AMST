<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

    protected $table = 'detail_pembelian';
    protected $primaryKey = 'id_detail_pembelian';
    protected $fillable = ['id_pembelian', 'id_barang', 'jumlah', 'subtotal'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian');
    }

    public function stok()
    {
        return $this->belongsTo(Stok::class, 'id_barang');
    }
}
