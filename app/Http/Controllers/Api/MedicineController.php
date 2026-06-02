<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicineController extends Controller
{
    /**
     * Display the specified medicine.
     */
    public function show(int|string $id): JsonResponse
    {
        $medicine = Medicine::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $medicine
        ]);
    }

    /**
     * Search for medicines by name, category, or description.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1'
        ]);

        $query = $request->input('query');

        $medicines = Medicine::where('name', 'like', '%' . $query . '%')
            ->orWhere('category', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->orWhere('active_ingredient', 'like', '%' . $query . '%')
            ->limit(30)
            ->get([
                'id',
                'name',
                'category',
                'price',
                'requires_prescription',
                'image',
                'description',
                'active_ingredient',
            ]);

        return response()->json([
            'success' => true,
            'count'   => $medicines->count(),
            'data'    => $medicines
        ]);
    }
}
