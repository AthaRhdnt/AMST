<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create 10 transactions, each with associated detail records
        Transaksi::factory()
            ->count(50)
            ->create()
            ->each(function ($transaksi) {
                // Create between 1 and 5 detail records for each transaction
                DetailTransaksi::factory()
                    ->count(rand(1, 5))
                    ->create(['id_transaksi' => $transaksi->id_transaksi]);
            });
    }
}
