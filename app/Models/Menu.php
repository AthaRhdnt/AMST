<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    protected $fillable = ['id_kategori', 'nama_menu', 'harga_menu', 'image'];
    public $incrementing = false;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($menu) {
            // Step 1: Get the highest existing id_menu excluding 97, 98, and 99
            $maxId = DB::table('menu')
                        ->whereNotIn('id_menu', [97, 98, 99]) // Exclude reserved ids 97, 98, and 99
                        ->max('id_menu'); // Find the highest id_menu

            // Step 2: If maxId is >= 97, skip 97, 98, and 99
            if ($maxId >= 99) {
                // Get the next available id_menu
                $nextId = $maxId + 1;
                if ($nextId == 97) {
                    $nextId = $maxId + 2; // Skip 97
                } elseif ($nextId == 98) {
                    $nextId = $maxId + 3; // Skip 98
                } elseif ($nextId == 99) {
                    $nextId = $maxId + 4; // Skip 99
                }
                $menu->id_menu = $nextId;
            } else {
                // Continue normally if maxId < 97
                $menu->id_menu = $maxId + 1;
            }
        });
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_menu');
    }

    public function menuStok()
    {
        return $this->hasMany(MenuStok::class, 'id_menu');
    }

    public function stok()
    {
        return $this->belongsToMany(Stok::class, 'menu_stok', 'id_menu', 'id_barang')
                ->withPivot('jumlah');
    }
}
