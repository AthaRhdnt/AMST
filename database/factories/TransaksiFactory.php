<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransaksiFactory extends Factory
{
    public function definition()
    {
        // Generate a random date between yesterday and today
        $randomDateTime = fake()->dateTimeBetween('-2 month', '-2 day');
        
        // Convert the random datetime to a timestamp
        $timestamp = $randomDateTime->getTimestamp(); // Convert datetime to Unix timestamp
        
        // Convert the timestamp to hexadecimal
        $hexTimestamp = strtoupper(dechex($timestamp * 1000)); // Convert to hex, multiplied for precision

        return [
            'id_outlet' => fake()->numberBetween(1, 4), // Random outlet ID
            'kode_transaksi' => 'TRX-' . $hexTimestamp, // Generate kode_transaksi with timestamp in hex
            'tanggal_transaksi' => $randomDateTime, // Random date between yesterday and today
            'total_transaksi' => 0, // Placeholder, calculated later
            'created_at' => $randomDateTime, // Set created_at to the random date
            'updated_at' => $randomDateTime, // Set updated_at to the random date
        ];
    }
}