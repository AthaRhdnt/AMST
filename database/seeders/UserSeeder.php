<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_user' => 'Pemilik',
                'id_role' => 1,
                'username' => 'admin',
                'password' => Hash::make('123'),
            ],
            [
                'nama_user' => 'Mulawarman',
                'id_role' => 2,
                'username' => 'kasir',
                'password' => Hash::make('321'),
            ]
        ];
    
        foreach ($data as $value) {
            User::create($value);
        }
    }
}
