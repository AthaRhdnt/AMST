<?php

namespace Database\Seeders;

use App\Models\Stok;
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
            ['nama_barang' => 'Teh Hitam', 'jumlah_barang' => 500],
            ['nama_barang' => 'Teh Poci STM', 'jumlah_barang' => 500],
            ['nama_barang' => 'Thai Tea', 'jumlah_barang' => 500],
            ['nama_barang' => 'Lemonade Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Lecy Powder', 'jumlah_barang' => 5000],
            ['nama_barang' => 'Leci Concentrate', 'jumlah_barang' => 500],
            ['nama_barang' => 'Mango Concentrate', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sjora Mango Peach', 'jumlah_barang' => 500],
            ['nama_barang' => 'Blackcurrant Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Red Velvet Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Taro Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Choco Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Greentea Latte Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Cappucino Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Matcha Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Milo', 'jumlah_barang' => 500],
            ['nama_barang' => 'Kopi Hitam', 'jumlah_barang' => 500],
            ['nama_barang' => 'Krimer Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Ice Cream Soft Docelle', 'jumlah_barang' => 500],
            ['nama_barang' => 'Susu UHT', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sprite', 'jumlah_barang' => 500],
            ['nama_barang' => 'Yakult', 'jumlah_barang' => 500],
            ['nama_barang' => 'Susu Kental Manis', 'jumlah_barang' => 500],
            ['nama_barang' => 'Gula Pasir', 'jumlah_barang' => 500],
            ['nama_barang' => 'Gula Cair', 'jumlah_barang' => 500],
            ['nama_barang' => 'Boba Strawberry', 'jumlah_barang' => 500],
            ['nama_barang' => 'Strawberry Concentrate', 'jumlah_barang' => 500],
            ['nama_barang' => 'Selai Strawberry', 'jumlah_barang' => 500],
            ['nama_barang' => 'Brown Sugar Liquid', 'jumlah_barang' => 500],
            ['nama_barang' => 'Brown Sugar Bubuk', 'jumlah_barang' => 500],
            ['nama_barang' => 'Bobatee Pearl', 'jumlah_barang' => 500],
            ['nama_barang' => 'Cheese Powder', 'jumlah_barang' => 500],
            ['nama_barang' => 'Rainbow Jelly', 'jumlah_barang' => 500],
            ['nama_barang' => 'Oreo Cookie Crumb', 'jumlah_barang' => 500],
            ['nama_barang' => 'Egg Puding', 'jumlah_barang' => 500],
            ['nama_barang' => 'Aqua Galon', 'jumlah_barang' => 500],
            ['nama_barang' => 'Es Batu', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Cup 22oz', 'jumlah_barang' => 500],
            ['nama_barang' => 'Paper Cup 8oz', 'jumlah_barang' => 500],
            ['nama_barang' => 'Lid Paper Cup 8oz', 'jumlah_barang' => 500],
            ['nama_barang' => 'Lid Cadangan 22oz', 'jumlah_barang' => 500],
            ['nama_barang' => 'Roll Sealer', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sedotan Bubble', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sedotan Kecil', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sedotan Gepeng', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Takeaway Pendek 1 Cup', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Takeaway 15 (2 Cup)', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Takeaway 20/21 (4 Cup)', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Takeaway 24', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Takeaway 28 (6 Cup)', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Takeaway 25', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Lem', 'jumlah_barang' => 500],
            ['nama_barang' => 'Kertas Thermal', 'jumlah_barang' => 500],
            ['nama_barang' => 'Box Takeaway Small', 'jumlah_barang' => 500],
            ['nama_barang' => 'Box Takeaway Medium', 'jumlah_barang' => 500],
            ['nama_barang' => 'Box Takeaway Pizza', 'jumlah_barang' => 500],
            ['nama_barang' => 'Cup Saos', 'jumlah_barang' => 500],
            ['nama_barang' => 'Hot Sauce Sachet', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sacs Barbeque', 'jumlah_barang' => 500],
            ['nama_barang' => 'Saos Blackpepper', 'jumlah_barang' => 500],
            ['nama_barang' => 'Saos Keju', 'jumlah_barang' => 500],
            ['nama_barang' => 'Kertas Roti', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sendok Plastik', 'jumlah_barang' => 500],
            ['nama_barang' => 'Sendok Plastik Kecil', 'jumlah_barang' => 500],
            ['nama_barang' => 'Garpu Plastik', 'jumlah_barang' => 500],
            ['nama_barang' => 'Kantong Plastik 1/4 Kg', 'jumlah_barang' => 500],
            ['nama_barang' => 'Kantong Plastik 1 Kg', 'jumlah_barang' => 500],
            ['nama_barang' => 'Kantong Plastik 1/2 kg', 'jumlah_barang' => 500],
            ['nama_barang' => 'Tissu Dapur', 'jumlah_barang' => 500],
            ['nama_barang' => 'Tissu Halus', 'jumlah_barang' => 500],
            ['nama_barang' => 'Tissu kotak kecil', 'jumlah_barang' => 500],
            ['nama_barang' => 'Plastik Sampah', 'jumlah_barang' => 500],
            ['nama_barang' => 'Elpiji', 'jumlah_barang' => 500],
            ['nama_barang' => 'Hand Glove', 'jumlah_barang' => 500],
            ['nama_barang' => 'Minyak Goreng', 'jumlah_barang' => 500],
            ['nama_barang' => 'Cuka', 'jumlah_barang' => 500],
            ['nama_barang' => 'Tusuk Gigi', 'jumlah_barang' => 500],
        ];
        foreach ($data as $value) {
            Stok::insert([
                'nama_barang' => $value['nama_barang'],
                'jumlah_barang' => $value['jumlah_barang'],
            ]);
        }
    }
}
