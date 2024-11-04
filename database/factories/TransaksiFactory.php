<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaksi>
 */
class TransaksiFactory extends Factory
{
    protected $model = Transaksi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalTransaksi = $this->faker->dateTimeBetween('-1 month', 'tomorrow'); // Random date within the last month
        // Get random menu items and calculate total
        $menuItems = Menu::inRandomOrder()->take(rand(1, 5))->get();
        $totalTransaksi = $menuItems->sum(function ($item) {
            return $item->harga_menu * rand(1, 3); // Random quantity between 1 and 3
        });

        return [
            'id_outlet' => 1,
            'kode_transaksi' => 'TRX-' . strtoupper(uniqid()),
            'tanggal_transaksi' => $this->faker->dateTimeBetween('-1 month', 'tomorrow'),
            'total_transaksi' => $totalTransaksi,
            'created_at' => $this->getRandomTimestamp($tanggalTransaksi),
        ];
    }

    private function getRandomTimestamp($date)
    {
        // Generate a random time within the same day as tanggal_transaksi
        return Carbon::parse($date)->setTime(
            $this->faker->numberBetween(0, 23),
            $this->faker->numberBetween(0, 59),
            $this->faker->numberBetween(0, 59)
        );
    }
}
