<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id_kategori' => 1, 'nama_menu' => 'Teh Leci', 'harga_menu' => 6000],
            ['id_kategori' => 1, 'nama_menu' => 'Teh Lemon', 'harga_menu' => 6000],
            ['id_kategori' => 1, 'nama_menu' => 'Teh Blackcurrant', 'harga_menu' => 6000],
            ['id_kategori' => 2, 'nama_menu' => 'Es Teh Manis', 'harga_menu' => 3000],
            ['id_kategori' => 2, 'nama_menu' => 'Thai Tea', 'harga_menu' => 10000],
            ['id_kategori' => 2, 'nama_menu' => 'Milk Tea', 'harga_menu' => 10000],
            ['id_kategori' => 3, 'nama_menu' => 'Leci Berry', 'harga_menu' => 8000],
            ['id_kategori' => 3, 'nama_menu' => 'Lemon Berry', 'harga_menu' => 8000],
            ['id_kategori' => 3, 'nama_menu' => 'Blackcurrant Berry', 'harga_menu' => 8000],
            ['id_kategori' => 4, 'nama_menu' => 'Leci Yakult', 'harga_menu' => 11000],
            ['id_kategori' => 4, 'nama_menu' => 'Lemon Yakult', 'harga_menu' => 11000],
            ['id_kategori' => 4, 'nama_menu' => 'Blackcurrant Yakult', 'harga_menu' => 11000],
            ['id_kategori' => 5, 'nama_menu' => 'Leci Soda', 'harga_menu' => 10000],
            ['id_kategori' => 5, 'nama_menu' => 'Lemon Soda', 'harga_menu' => 10000],
            ['id_kategori' => 5, 'nama_menu' => 'Blackcurrant Soda', 'harga_menu' => 10000],
            ['id_kategori' => 6, 'nama_menu' => 'Milky Choco', 'harga_menu' => 10000],
            ['id_kategori' => 6, 'nama_menu' => 'Milky Taro', 'harga_menu' => 10000],
            ['id_kategori' => 6, 'nama_menu' => 'Milky Red Velvet', 'harga_menu' => 10000],
            ['id_kategori' => 7, 'nama_menu' => 'Cheezy Choco', 'harga_menu' => 13000],
            ['id_kategori' => 7, 'nama_menu' => 'Cheezy Taro', 'harga_menu' => 13000],
            ['id_kategori' => 7, 'nama_menu' => 'Cheezy Red Velvet', 'harga_menu' => 13000],
            ['id_kategori' => 8, 'nama_menu' => 'Greentea Latte', 'harga_menu' => 13000],
            ['id_kategori' => 8, 'nama_menu' => 'Cappucino Latte', 'harga_menu' => 13000],
        ];
        foreach ($data as $value) {
            Menu::insert([
                'id_kategori' => $value['id_kategori'],
                'nama_menu' => $value['nama_menu'],
                'harga_menu' => $value['harga_menu'],
            ]);
        }

        Menu::insert([
            [
                'id_menu' => 99,
                'id_kategori' => 99,
                'nama_menu' => 'Transaksi',
                'harga_menu' => 1,
            ],
            [
                'id_menu' => 98,
                'id_kategori' => 99,
                'nama_menu' => 'Update',
                'harga_menu' => 1,
            ],
            [
                'id_menu' => 97,
                'id_kategori' => 99,
                'nama_menu' => 'Update Sistem',
                'harga_menu' => 1,
            ],
        ]);
    }
}
