<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    protected $fillable = ['nama_kategori'];
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            $maxId = DB::table('kategori')
                        ->where('id_kategori', '!=', 99) 
                        ->max('id_kategori'); 

            if ($maxId >= 99) {
                $kategori->id_kategori = $maxId + 1;

                if ($kategori->id_kategori == 99) {
                    $kategori->id_kategori = $maxId + 2;
                }
            } else {
                $kategori->id_kategori = $maxId + 1;
            }
        });
    }

    public function menu()
    {
        return $this->hasMany(Menu::class, 'id_kategori');
    }

}
