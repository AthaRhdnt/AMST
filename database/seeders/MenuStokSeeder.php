<?php

namespace Database\Seeders;

use App\Models\MenuStok;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuStokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Teh Leci
            ['id_menu' => 1, 'id_barang' => 1, 'jumlah' => 200], // Teh Hitam
            ['id_menu' => 1, 'id_barang' => 5, 'jumlah' => 20], // Powder
            ['id_menu' => 1, 'id_barang' => 25, 'jumlah' => 25], // Gula Cair
            ['id_menu' => 1, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Teh Lemon
            ['id_menu' => 2, 'id_barang' => 1, 'jumlah' => 200], // Teh Hitam
            ['id_menu' => 2, 'id_barang' => 4, 'jumlah' => 20], // Powder
            ['id_menu' => 2, 'id_barang' => 25, 'jumlah' => 25], // Gula Cair
            ['id_menu' => 2, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Teh Blackcurrant
            ['id_menu' => 3, 'id_barang' => 1, 'jumlah' => 200], // Teh Hitam
            ['id_menu' => 3, 'id_barang' => 9, 'jumlah' => 20], // Powder
            ['id_menu' => 3, 'id_barang' => 25, 'jumlah' => 25], // Gula Cair
            ['id_menu' => 3, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Es Teh Manis
            ['id_menu' => 4, 'id_barang' => 2, 'jumlah' => 250], // Teh Poci STM
            ['id_menu' => 4, 'id_barang' => 25, 'jumlah' => 10], // Gula Cair
            ['id_menu' => 4, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Thai Tea
            ['id_menu' => 5, 'id_barang' => 18, 'jumlah' => 30], // Krimer Powder
            ['id_menu' => 5, 'id_barang' => 2, 'jumlah' => 200], // Teh Poci STM
            ['id_menu' => 5, 'id_barang' => 25, 'jumlah' => 30], // Gula Cair
            ['id_menu' => 5, 'id_barang' => 23, 'jumlah' => 40], // Susu Kental Manis
            ['id_menu' => 5, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Milk Tea
            ['id_menu' => 6, 'id_barang' => 18, 'jumlah' => 30], // Krimer Powder
            ['id_menu' => 6, 'id_barang' => 2, 'jumlah' => 200], // Teh Poci STM
            ['id_menu' => 6, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 6, 'id_barang' => 29, 'jumlah' => 20], // Brown Sugar Liquid
            ['id_menu' => 6, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Leci Berry
            ['id_menu' => 7, 'id_barang' => 5, 'jumlah' => 20], // Powder
            ['id_menu' => 7, 'id_barang' => 25, 'jumlah' => 10], // Gula Cair
            ['id_menu' => 7, 'id_barang' => 27, 'jumlah' => 30], // Selai Strawberry
            ['id_menu' => 7, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Lemon Berry
            ['id_menu' => 8, 'id_barang' => 4, 'jumlah' => 20], // Powder
            ['id_menu' => 8, 'id_barang' => 25, 'jumlah' => 10], // Gula Cair
            ['id_menu' => 8, 'id_barang' => 27, 'jumlah' => 30], // Selai Strawberry
            ['id_menu' => 8, 'id_barang' => 37, 'jumlah' => 300], // Es Batu

            // Blackcurrant Berry
            ['id_menu' => 9, 'id_barang' => 9, 'jumlah' => 20], // Powder
            ['id_menu' => 9, 'id_barang' => 25, 'jumlah' => 10], // Gula Cair
            ['id_menu' => 9, 'id_barang' => 27, 'jumlah' => 30], // Selai Strawberry
            ['id_menu' => 9, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
        
            // Leci Yakult
            ['id_menu' => 10, 'id_barang' => 5, 'jumlah' => 20], // Powder
            ['id_menu' => 10, 'id_barang' => 22, 'jumlah' => 2],  // Yakult
            ['id_menu' => 10, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Lemon Yakult
            ['id_menu' => 11, 'id_barang' => 4, 'jumlah' => 20], // Powder
            ['id_menu' => 11, 'id_barang' => 22, 'jumlah' => 2],  // Yakult
            ['id_menu' => 11, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            
            // Blackcurrant Yakult
            ['id_menu' => 12, 'id_barang' => 9, 'jumlah' => 20], // Powder
            ['id_menu' => 12, 'id_barang' => 22, 'jumlah' => 2],  // Yakult
            ['id_menu' => 12, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
        
            // Leci Soda
            ['id_menu' => 13, 'id_barang' => 5, 'jumlah' => 20], // Powder
            ['id_menu' => 13, 'id_barang' => 21, 'jumlah' => 200], // Sprite
            ['id_menu' => 13, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            ['id_menu' => 13, 'id_barang' => 32, 'jumlah' => 40], // Rainbow Jelly

            // Lemon Soda
            ['id_menu' => 14, 'id_barang' => 4, 'jumlah' => 20], // Powder
            ['id_menu' => 14, 'id_barang' => 21, 'jumlah' => 200], // Sprite
            ['id_menu' => 14, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            ['id_menu' => 14, 'id_barang' => 32, 'jumlah' => 40], // Rainbow Jelly

            // Blackcurrant Soda
            ['id_menu' => 15, 'id_barang' => 9, 'jumlah' => 20], // Powder
            ['id_menu' => 15, 'id_barang' => 21, 'jumlah' => 200], // Sprite
            ['id_menu' => 15, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            ['id_menu' => 15, 'id_barang' => 32, 'jumlah' => 40], // Rainbow Jelly
        
            // Milky Choco
            ['id_menu' => 16, 'id_barang' => 12, 'jumlah' => 25], // Powder
            ['id_menu' => 16, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 16, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 16, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 16, 'id_barang' => 37, 'jumlah' => 300], // Es Batu

            // Milky Taro
            ['id_menu' => 17, 'id_barang' => 11, 'jumlah' => 25], // Powder
            ['id_menu' => 17, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 17, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 17, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 17, 'id_barang' => 37, 'jumlah' => 300], // Es Batu

            // Milky Red Velvet
            ['id_menu' => 18, 'id_barang' => 10, 'jumlah' => 25], // Powder
            ['id_menu' => 18, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 18, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 18, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 18, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
        
            // Cheezy Choco
            ['id_menu' => 19, 'id_barang' => 12, 'jumlah' => 25], // Powder
            ['id_menu' => 19, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 19, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 19, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 19, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            ['id_menu' => 19, 'id_barang' => 32, 'jumlah' => 40], // Cheese Powder

            // Cheezy Taro
            ['id_menu' => 20, 'id_barang' => 11, 'jumlah' => 25], // Powder
            ['id_menu' => 20, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 20, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 20, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 20, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            ['id_menu' => 20, 'id_barang' => 32, 'jumlah' => 40], // Cheese Powder

            // Cheezy Red Velvet
            ['id_menu' => 21, 'id_barang' => 10, 'jumlah' => 25], // Powder
            ['id_menu' => 21, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 21, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 21, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 21, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
            ['id_menu' => 21, 'id_barang' => 32, 'jumlah' => 40], // Cheese Powder

            // Greentea Latte
            ['id_menu' => 22, 'id_barang' => 13, 'jumlah' => 25], // Powder
            ['id_menu' => 22, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 22, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 22, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 22, 'id_barang' => 37, 'jumlah' => 300], // Es Batu

            // Cappucino Latte
            ['id_menu' => 23, 'id_barang' => 14, 'jumlah' => 25], // Powder
            ['id_menu' => 23, 'id_barang' => 23, 'jumlah' => 20], // Susu Kental Manis
            ['id_menu' => 23, 'id_barang' => 18, 'jumlah' => 10], // Krimer Powder
            ['id_menu' => 23, 'id_barang' => 25, 'jumlah' => 20], // Gula Cair
            ['id_menu' => 23, 'id_barang' => 37, 'jumlah' => 300], // Es Batu
        ];
        foreach ($data as $value) {
            MenuStok::insert([
                'id_menu' => $value['id_menu'],
                'id_barang' => $value['id_barang'],
                'jumlah' => $value['jumlah'],
            ]);
        }
    }
}
