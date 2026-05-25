<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
   
public function store(Request $request)
{
    $validated = $request->validate([
        'name'                  => 'required|string|max:80',
        'description'           => 'nullable|string',
        'price'                 => 'required|numeric',
        'active_ingredient'     => 'nullable|string',
        'manufacturer'          => 'nullable|string',
        'category'              => 'nullable|string',
        'requires_prescription' => 'required|boolean',
        'expiration_date'       => 'nullable|date|after:today',
        'batch_number'          => 'nullable|string|max:50',
        'image'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
    ]);

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('medicines', 'public');
    }

    $medicine = Medicine::create($validated);

    return response()->json([
        'message' => 'Medicine added successfully',
        'data'    => $medicine,
    ]);
}


public function index(Request $request)
{
    $query = Medicine::query();

    if ($request->filled('name')) {
        $query->where('name', 'LIKE', '%' . $request->name . '%');
    }

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    $medicines = $query->orderBy('name')->get();

    foreach ($medicines as $medicine) {
        $medicine->image_url = $medicine->image
            ? asset('storage/' . $medicine->image)
            : null;
    }

    return response()->json([
        'status' => 'success',
        'data'   => $medicines,
    ]);
}

}