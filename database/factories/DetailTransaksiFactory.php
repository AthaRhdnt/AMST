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
        // Pre-fetch menus and pick one at random
        $menuItem = Menu::all()->random(); // Fetch all menus, then pick one randomly

        // Generate a random quantity between 1 and 3
        $jumlah = rand(1, 3);

        // Calculate subtotal based on quantity and menu item price
        $subtotal = $menuItem->harga_menu * $jumlah;

        return [
            'id_menu' => $menuItem->id_menu,
            'jumlah' => $jumlah,
            'subtotal' => $subtotal,
        ];
    }
}
