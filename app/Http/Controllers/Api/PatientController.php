<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Search for patients by name, email, or phone.
     * Accepts ?query=... or ?name=... (both work)
     */
    public function search(Request $request): JsonResponse
    {
        // Accept either ?query= or ?name= for flexibility
        $search = $request->input('query') ?? $request->input('name');

        if (!$search || strlen(trim($search)) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a search term using ?query= or ?name=',
            ], 422);
        }

        $search = trim($search);

        $patients = Patient::where('name', 'like', '%' . $search . '%')
                           ->orWhere('email', 'like', '%' . $search . '%')
                           ->orWhere('phone', 'like', '%' . $search . '%')
                           ->limit(20)
                           ->get(['id', 'name', 'email', 'phone']);

        return response()->json([
            'success' => true,
            'count'   => $patients->count(),
            'data'    => $patients,
        ]);
    }
}
