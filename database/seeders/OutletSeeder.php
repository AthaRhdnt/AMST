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
        Outlets::insert([
            ['id_user' => 1, 'alamat_outlet' => 'Jalan Mulawarman'],
        ]);
    }
}
