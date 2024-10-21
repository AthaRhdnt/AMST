<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_kategori' => 'Fruity Series'],
            ['nama_kategori' => 'Tea Series'],
            ['nama_kategori' => 'Berry Jam Series'],
            ['nama_kategori' => 'Yakult Series'],
            ['nama_kategori' => 'Soda Series'],
            ['nama_kategori' => 'Milky Series'],
            ['nama_kategori' => 'Cheezy Series'],
            ['nama_kategori' => 'Latte Series'],
        ];
        foreach ($data as $value) {
            Kategori::insert([
                'nama_kategori' => $value['nama_kategori'],
            ]);
        }
    }
}
