<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOutlet extends Model
{
    use HasFactory;

    protected $table = 'stok_outlet';
    protected $fillable = ['id_outlet', 'id_barang', 'jumlah'];

    public function outlet()
    {
        return $this->belongsTo(Outlets::class, 'id_outlet');
    }

    public function barang()
    {
        return $this->belongsTo(Stok::class, 'id_barang');
    }
}
