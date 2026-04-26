<?php
namespace App\Http\Controllers;
use App\Services\DrugInteractionService;
use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Notifications\SafetyCheckResult;
use App\Models\User;

class SafetyCheckController extends Controller
{
    protected $interactionService;
    public function __construct(DrugInteractionService $interactionService)
    { $this->interactionService = $interactionService; }
    public function check(Request $request)
    {
        $request->validate([
            'medicine_ids' => 'required|array|min:1',
            'medicine_ids.*' => 'exists:medicines,id'
        ]);
        $medicineIds = $request->medicine_ids;
        $interactions = $this->interactionService->checkInteractions($medicineIds);
        $hasSevere = $this->interactionService->hasSevereInteraction($medicineIds);

        if ($request->user_id) {
        $user = User::find($request->user_id);
        if ($user) {
            $user->notify(new SafetyCheckResult($hasSevere, $interactions));
        }
    }
        return response()->json([
            'status' => $hasSevere ? 'danger' : 'safe',
            'message' => $hasSevere 
                ? 'Serious drug interactions detected!' 
                : 'No serious interactions found',
            'interactions' => $interactions,
            'medicines' => Medicine::whereIn('id', $medicineIds)->get(['id', 'name'])
        ]);}}