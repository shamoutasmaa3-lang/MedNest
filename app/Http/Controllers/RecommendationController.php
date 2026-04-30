<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{

    public function index(Request $request)
    {
        return Recommendation::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
//
    public function markAsRead($id)
    {
        $rec = Recommendation::findOrFail($id);
        $rec->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}
