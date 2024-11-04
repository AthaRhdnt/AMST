<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailTransaksi>
 */
class DetailTransaksiFactory extends Factory
{
    protected $model = DetailTransaksi::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         // Randomly select a menu item
        $menuItem = Menu::inRandomOrder()->first();
        $jumlah = rand(1, 3); // Random quantity between 1 and 3
        $subtotal = $menuItem->harga_menu * $jumlah;

        return [
            'id_transaksi' => Transaksi::factory(), // Link to a new Transaksi
            'id_menu' => $menuItem->id_menu,
            'jumlah' => $jumlah,
            'subtotal' => $subtotal,
        ];
    }
}
