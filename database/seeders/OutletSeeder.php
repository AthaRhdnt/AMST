<?php

namespace Database\Seeders;

use App\Models\Outlets;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id_user' => 2, 'alamat_outlet' => 'Jl. Mulawarman Raya No. 45'],
            ['id_user' => 3, 'alamat_outlet' => 'Jl. Banjarsari Raya No. 25'],
            ['id_user' => 4, 'alamat_outlet' => 'Jl. Prof. Soedharto No. 15'],
            ['id_user' => 5, 'alamat_outlet' => 'Jl. Cemara Raya Banyumanik'],
        ];
        foreach ($data as $value) {
            Outlets::insert([
                'id_user' => $value['id_user'],
                'alamat_outlet' => $value['alamat_outlet'],
                'status' => 'active',
            ]);
        }
    }
}
