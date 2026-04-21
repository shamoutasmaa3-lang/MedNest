<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Medicine;

class StockSeeder extends Seeder
{
    public function run()
    {
        // Fetch all medicines
        $medicines = Medicine::all();

        foreach ($medicines as $medicine) {
            Stock::create([
                'medicine_id' => $medicine->id,
                'quantity' => rand(10, 500),
                'expiration_date' => now()->addMonths(rand(6, 24)), 
                'location' => 'Shelf ' . rand(1, 10),
            ]);
        }
    }
}