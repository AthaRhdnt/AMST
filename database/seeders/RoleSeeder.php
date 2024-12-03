<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_role' => 'Pemilik'],
            ['nama_role' => 'Karyawan'],
        ];
        foreach ($data as $value) {
            Role::insert([
                'nama_role' => $value['nama_role'],
            ]);
        }
    }
}
