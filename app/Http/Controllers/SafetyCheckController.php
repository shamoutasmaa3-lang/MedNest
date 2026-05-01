<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Recommendation;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use App\Notifications\SafetyCheckResult;
use App\Services\DrugInteractionService;

class SafetyCheckController extends Controller
{
   

    protected $interactionService;
    public function __construct(DrugInteractionService $interactionService)
    { 
        $this->interactionService = $interactionService; 
    }
    

    public function check(Request $request, RecommendationService $service)
    {
        $request->validate([
            'medicine_ids' => 'required|array|min:1',
            'medicine_ids.*' => 'exists:medicines,id'
        ]);

        $medicineIds = $request->medicine_ids;

        $interactions = $this->interactionService->checkInteractions($medicineIds);
        $hasSevere = $this->interactionService->hasSevereInteraction($medicineIds);

        $request->user()->notify(new SafetyCheckResult($hasSevere, $interactions));

        foreach ($medicineIds as $id) {
            $medicine = Medicine::find($id);

            if ($medicine && $medicine->stock <= 0) {

                $alternatives = $service->suggestAlternatives($medicine);

                Recommendation::create([
                    'user_id' => $request->user()->id,
                    'medicine_id' => $medicine->id,
                    'type' => 'alternative',
                    'message' => "Medicine {$medicine->name} is unavailable. Suggested alternatives:",
                    'data' => ['alternatives' => $alternatives],
                ]);
            }
        }

        return response()->json([
            'status' => $hasSevere ? 'danger' : 'safe',
            'message' => $hasSevere 
                ? 'Serious drug interactions detected!'
                : 'No serious interactions found',
            'interactions' => $interactions,
            'medicines' => Medicine::whereIn('id', $medicineIds)->get(['id', 'name'])
        ]);
    }
}
