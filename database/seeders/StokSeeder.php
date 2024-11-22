<?php

namespace Database\Seeders;

use App\Models\Stok;
use App\Models\Outlets;
use App\Models\StokOutlet;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_barang' => 'Teh Hitam'],
            ['nama_barang' => 'Teh Poci STM'],
            ['nama_barang' => 'Thai Tea'],
            ['nama_barang' => 'Lemonade Powder'],
            ['nama_barang' => 'Lecy Powder'],
            ['nama_barang' => 'Leci Concentrate'],
            ['nama_barang' => 'Mango Concentrate'],
            ['nama_barang' => 'Sjora Mango Peach'],
            ['nama_barang' => 'Blackcurrant Powder'],
            ['nama_barang' => 'Red Velvet Powder'],
            ['nama_barang' => 'Taro Powder'],
            ['nama_barang' => 'Choco Powder'],
            ['nama_barang' => 'Greentea Latte Powder'],
            ['nama_barang' => 'Cappucino Powder'],
            ['nama_barang' => 'Matcha Powder'],
            ['nama_barang' => 'Milo'],
            ['nama_barang' => 'Kopi Hitam'],
            ['nama_barang' => 'Krimer Powder'],
            ['nama_barang' => 'Ice Cream Soft Docelle'],
            ['nama_barang' => 'Susu UHT'],
            ['nama_barang' => 'Sprite'],
            ['nama_barang' => 'Yakult'],
            ['nama_barang' => 'Susu Kental Manis'],
            ['nama_barang' => 'Gula Pasir'],
            ['nama_barang' => 'Gula Cair'],
            ['nama_barang' => 'Boba Strawberry'],
            ['nama_barang' => 'Strawberry Concentrate'],
            ['nama_barang' => 'Selai Strawberry'],
            ['nama_barang' => 'Brown Sugar Liquid'],
            ['nama_barang' => 'Brown Sugar Bubuk'],
            ['nama_barang' => 'Bobatee Pearl'],
            ['nama_barang' => 'Cheese Powder'],
            ['nama_barang' => 'Rainbow Jelly'],
            ['nama_barang' => 'Oreo Cookie Crumb'],
            ['nama_barang' => 'Egg Puding'],
            ['nama_barang' => 'Aqua Galon'],
            ['nama_barang' => 'Es Batu'],
            ['nama_barang' => 'Plastik Cup 22oz'],
            ['nama_barang' => 'Paper Cup 8oz'],
            ['nama_barang' => 'Lid Paper Cup 8oz'],
            ['nama_barang' => 'Lid Cadangan 22oz'],
            ['nama_barang' => 'Roll Sealer'],
            ['nama_barang' => 'Sedotan Bubble'],
            ['nama_barang' => 'Sedotan Kecil'],
            ['nama_barang' => 'Sedotan Gepeng'],
            ['nama_barang' => 'Plastik Takeaway Pendek 1 Cup'],
            ['nama_barang' => 'Plastik Takeaway 15 (2 Cup)'],
            ['nama_barang' => 'Plastik Takeaway 20/21 (4 Cup)'],
            ['nama_barang' => 'Plastik Takeaway 24'],
            ['nama_barang' => 'Plastik Takeaway 28 (6 Cup)'],
            ['nama_barang' => 'Plastik Takeaway 25'],
            ['nama_barang' => 'Plastik Lem'],
            ['nama_barang' => 'Kertas Thermal'],
            ['nama_barang' => 'Box Takeaway Small'],
            ['nama_barang' => 'Box Takeaway Medium'],
            ['nama_barang' => 'Box Takeaway Pizza'],
            ['nama_barang' => 'Cup Saos'],
            ['nama_barang' => 'Hot Sauce Sachet'],
            ['nama_barang' => 'Sacs Barbeque'],
            ['nama_barang' => 'Saos Blackpepper'],
            ['nama_barang' => 'Saos Keju'],
            ['nama_barang' => 'Kertas Roti'],
            ['nama_barang' => 'Sendok Plastik'],
            ['nama_barang' => 'Sendok Plastik Kecil'],
            ['nama_barang' => 'Garpu Plastik'],
            ['nama_barang' => 'Kantong Plastik 1/4 Kg'],
            ['nama_barang' => 'Kantong Plastik 1 Kg'],
            ['nama_barang' => 'Kantong Plastik 1/2 kg'],
            ['nama_barang' => 'Tissu Dapur'],
            ['nama_barang' => 'Tissu Halus'],
            ['nama_barang' => 'Tissu kotak kecil'],
            ['nama_barang' => 'Plastik Sampah'],
            ['nama_barang' => 'Elpiji'],
            ['nama_barang' => 'Hand Glove'],
            ['nama_barang' => 'Minyak Goreng'],
            ['nama_barang' => 'Cuka'],
            ['nama_barang' => 'Tusuk Gigi'],
        ];

        // Insert stok items into stok table
        foreach ($data as $value) {
            $stok = Stok::create([
                'nama_barang' => $value['nama_barang'],
                'jumlah_barang' => 0, // Set to 0, as quantity will be managed in StokOutlet
            ]);

            $outlets = Outlets::all();

            // Now, insert into StokOutlet for each outlet with the default quantity (e.g., 1000)
            foreach ($outlets as $outlet) {
                StokOutlet::create([
                    'id_outlet' => $outlet->id_outlet,  // Assign outlet ID dynamically
                    'id_barang' => $stok->id_barang,    // The newly created stok item ID
                    'jumlah' => 1000,                    // Default quantity for this outlet
                ]);
            }
            
            Stok::updateJumlahBarang($stok->id_barang);
        }
    }
}
