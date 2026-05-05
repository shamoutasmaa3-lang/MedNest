<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
   
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:80',
            'description'           => 'nullable|string',
            'price'                 => 'required|numeric',
            'active_ingredient'     => 'nullable|string',
            'manufacturer'          => 'nullable|string',
            'category'              => 'nullable|string',
            'requires_prescription' => 'required|boolean',
        ]);

        $medicine = Medicine::create($request->all());

        return response()->json([
            'message' => 'Medicine added successfully',
            'data'    => $medicine,
        ]);
    }

    // عرض الأدوية + البحث
    public function index(Request $request)
    {
        $query = Medicine::query();

        // بحث بالاسم
        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // بحث بالفئة
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $query->orderBy('name')->get(),
        ]);
    }
}
