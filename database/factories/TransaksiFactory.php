<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Support\Str;
use App\Models\DetailTransaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        // Generate a random transaction date within the last month
        $tanggalTransaksi = $this->faker->dateTimeBetween('-1 month', 'yesterday');

        // Use make() for transaction details and link them later
        $details = DetailTransaksi::factory(rand(1, 5))->make();

        // Calculate total_transaksi based on the details' subtotal
        $totalTransaksi = $details->sum('subtotal');

        return [
            'id_outlet' => 1, // Use a dynamic outlet if needed
            'kode_transaksi' => 'TRX-' . strtoupper(uniqid()),
            'tanggal_transaksi' => $tanggalTransaksi,
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
