<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlets extends Model
{
    use HasFactory;

    protected $table = 'outlet';
    protected $primaryKey = 'id_outlet';
    protected $fillable = ['id_user', 'alamat_outlet', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function stokOutlet()
    {
        return $this->hasMany(StokOutlet::class, 'id_outlet');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_outlet');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'id_outlet');
    }
}
