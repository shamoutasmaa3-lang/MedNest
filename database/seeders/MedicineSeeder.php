<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        Medicine::create([
            'name' => 'Ibuprofen',
            'description' => 'Pain reliever',
            'category' => 'NSAID',
            'requires_prescription' => false,
            'price' => 5.00
        ]);

        Medicine::create([
            'name' => 'Warfarin',
            'description' => 'Blood thinner',
            'category' => 'Anticoagulant',
            'requires_prescription' => true,
            'price' => 12.00
        ]);

        Medicine::create([
            'name' => 'Aspirin',
            'description' => 'Pain reliever and blood thinner',
            'category' => 'NSAID',
            'requires_prescription' => false,
            'price' => 3.00
        ]);
    }
}