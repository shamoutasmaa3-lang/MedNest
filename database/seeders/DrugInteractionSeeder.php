<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DrugInteraction;
use App\Models\Medicine;

class DrugInteractionSeeder extends Seeder
{
    public function run()
    {
        $ibuprofen = Medicine::where('name', 'Ibuprofen')->first();
        $warfarin = Medicine::where('name', 'Warfarin')->first();
        $aspirin = Medicine::where('name', 'Aspirin')->first();

        // Serious interaction between Warfarin and Aspirin
        DrugInteraction::create([
            'medicine_id_1' => $warfarin->id,
            'medicine_id_2' => $aspirin->id,
            'severity' => 'high',
            'description' => 'Greatly increases the risk of bleeding'
        ]);

        // Moderate interaction between Ibuprofen and Warfarin
        DrugInteraction::create([
            'medicine_id_1' => $ibuprofen->id,
            'medicine_id_2' => $warfarin->id,
            'severity' => 'moderate',
            'description' => 'May increase the effect of blood thinning'
        ]);
    }
}