<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // Search patients by name
    public function search(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2'
        ]);

        $patients = Patient::where('name', 'like', '%' . $request->name . '%')
                           ->limit(20)  // limit results for performance
                           ->get(['id', 'name', 'email', 'phone']); // select only needed fields

        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }
}