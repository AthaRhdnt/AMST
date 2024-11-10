<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\RiwayatStok;
use App\Models\DetailTransaksi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        // The total number of transactions to generate
        $totalTransactions = 15;
        $batchSize = 5; // You can adjust this size as needed for performance

        // Create transactions in chunks to avoid memory overload
        for ($i = 0; $i < $totalTransactions; $i += $batchSize) {
            $transactions = Transaksi::factory($batchSize)->create();  // Use create to persist transactions

            foreach ($transactions as $transaksi) {
                // Create a collection of DetailTransaksi records
                $details = DetailTransaksi::factory(rand(1, 5))->make([
                    'id_transaksi' => $transaksi->id_transaksi,  // Ensure we set id_transaksi properly
                ]);

                // Store the details in a batch insert array
                $detailData = [];
                foreach ($details as $detail) {
                    $detailData[] = $detail->toArray();  // Prepare data for batch insert
                }
                // Insert the DetailTransaksi records in bulk
                DetailTransaksi::insert($detailData);

                // Prepare RiwayatStok entries for each DetailTransaksi record
                $riwayatStokData = [];
                foreach ($details as $detail) {
                    // Eager load the related menu and stok for efficiency
                    $menuStocks = $detail->menu->stok()->take(3)->get();  // Only fetch a limited number of stocks

                    foreach ($menuStocks as $stok) {
                        // Access the pivot data (quantity available for this menu item)
                        $pivotData = $stok->pivot;

                        // Ensure enough stock is available based on the pivot quantity
                        if ($stok->jumlah_barang >= $pivotData->jumlah) {
                            // Deduct the stock from the stok table (based on the pivot quantity)
                            $stok->jumlah_barang -= $pivotData->jumlah;  // Decrease the stock quantity based on the pivot data
                            $stok->save();  // Save the updated stock quantity

                            // Log the stock usage in RiwayatStok
                            $riwayatStokData[] = [
                                'id_transaksi' => $transaksi->id_transaksi,
                                'id_menu' => $detail->id_menu,
                                'id_barang' => $stok->id_barang,
                                'jumlah_pakai' => $pivotData->jumlah,  // Use the quantity from the pivot table
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        } else {
                            // Not enough stock available, throw an exception
                            throw new \Exception("Not enough stock for item {$stok->nama_barang}. Available: {$stok->jumlah_barang}, Required: {$pivotData->jumlah}");
                        }
                    }
                }

                // Insert RiwayatStok records in bulk
                RiwayatStok::insert($riwayatStokData);
            }
        }
    }
}
