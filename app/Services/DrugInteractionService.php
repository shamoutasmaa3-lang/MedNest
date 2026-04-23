<?php

namespace App\Services;

use App\Models\DrugInteraction;
use App\Models\Medicine;

class DrugInteractionService
{
    public function checkInteractions(array $medicineIds): array
    {
        $interactions = [];
        for ($i = 0; $i < count($medicineIds); $i++) {
            for ($j = $i + 1; $j < count($medicineIds); $j++) {
                $interaction = DrugInteraction::where(function ($query) use ($medicineIds, $i, $j) {
                    $query->where('medicine_id_1', $medicineIds[$i])
                        ->where('medicine_id_2', $medicineIds[$j]);
                })->orWhere(function ($query) use ($medicineIds, $i, $j) {
                    $query->where('medicine_id_1', $medicineIds[$j])
                        ->where('medicine_id_2', $medicineIds[$i]);
                })->first();
                if ($interaction) {
                    $medicine1 = Medicine::find($medicineIds[$i]);
                    $medicine2 = Medicine::find($medicineIds[$j]);

                    $interactions[] = [
                        'medicine_1' => $medicine1->name,
                        'medicine_2' => $medicine2->name,
                        'severity'   => $interaction->severity,
                        'description' => $interaction->description,
                    ];
                }
            }
        }
        return $interactions;
    }
    public function hasSevereInteraction(array $medicineIds): bool
    {
        foreach ($this->checkInteractions($medicineIds) as $interaction) {
            if (in_array($interaction['severity'], ['high', 'severe'])) {
                return true;
            }
        }
        return false;
    }
}
