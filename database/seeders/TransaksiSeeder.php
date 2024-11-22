<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\StokOutlet;
use App\Models\RiwayatStok;
use App\Models\DetailTransaksi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        $numberOfTransactions = 50; // Generate 50 transactions

        for ($i = 0; $i < $numberOfTransactions; $i++) {
            // Create a transaction
            $transaksi = Transaksi::factory()->create();

            // Get the random transaction date (tanggal_transaksi)
            $transactionDate = $transaksi->tanggal_transaksi;

            // Number of menu items in this transaction
            $menuCount = fake()->numberBetween(1, 5); 

            $totalTransaksi = 0;

            for ($j = 0; $j < $menuCount; $j++) {
                // Get random menu
                $menu = Menu::inRandomOrder()->first();
                $quantity = fake()->numberBetween(1, 5); // Random quantity
                $subtotal = $menu->harga_menu * $quantity;

                // Create transaction detail
                $detailTransaksi = DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_menu' => $menu->id_menu,
                    'jumlah' => $quantity,
                    'subtotal' => $subtotal,
                    'created_at' => $transactionDate,  // Set created_at to the transaction's date
                    'updated_at' => $transactionDate,  //
                ]);

                $totalTransaksi += $subtotal;

                // Log stock usage in RiwayatStok
                foreach ($menu->stok as $stok) {
                    $pivotData = $stok->pivot;

                    RiwayatStok::create([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'id_menu' => $menu->id_menu,
                        'id_barang' => $stok->id_barang,
                        'jumlah_pakai' => $pivotData->jumlah * $quantity,
                        'created_at' => $transactionDate,  // Set created_at to the transaction's date
                        'updated_at' => $transactionDate,  //
                    ]);
                }
            }

            // Update the transaction total
            $transaksi->update(['total_transaksi' => $totalTransaksi]);
        }
    }
}