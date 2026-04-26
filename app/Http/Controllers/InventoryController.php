<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\LowStockAlert;

class InventoryController extends Controller
{

    public function index()
    {
        $stocks = Stock::with('medicine')
            ->orderBy('created_at', 'desc')
            ->get();


        $lowStockThreshold = 10;
        $lowStockItems = $stocks->filter(function ($stock) use ($lowStockThreshold) {
            return $stock->quantity < $lowStockThreshold;
        });
        if ($lowStockItems->isNotEmpty()) {
        $pharmacists = User::where('role', 'pharmacist')->get();

        foreach ($lowStockItems as $stock) {
            foreach ($pharmacists as $pharmacist) {
                $pharmacist->notify(new LowStockAlert($stock));
            }
        }
    }

        return response()->json([
            'status' => 'success',
            'data' => $stocks->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'medicine_id' => $stock->medicine_id,
                    'medicine_name' => $stock->medicine->name,
                    'category' => $stock->medicine->category,
                    'quantity' => $stock->quantity,
                    'expiration_date' => $stock->expiration_date,
                    'location' => $stock->location,
                    'is_low_stock' => $stock->quantity < 10,
                ];
            }),
            'low_stock_count' => $lowStockItems->count(),
            'total_items' => $stocks->count(),
        ]);
    }
    /**
     * POST /api/system/inventory/deduct
     * خصم كمية من المخزون بعد صرف الدواء
     */
    public function deductStock(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $results = [];
        $errors = [];

        foreach ($request->items as $item) {
            $stock = Stock::where('medicine_id', $item['medicine_id'])->first();

            if (!$stock) {
                $errors[] = [
                    'medicine_id' => $item['medicine_id'],
                    'error' => 'Stock record not found for this medicine'
                ];
                continue;
            }

            if ($stock->quantity < $item['quantity']) {
                $errors[] = [
                    'medicine_id' => $item['medicine_id'],
                    'error' => "Insufficient stock. Available: {$stock->quantity}, Requested: {$item['quantity']}"
                ];
                continue;
            }

            $stock->quantity -= $item['quantity'];
            $stock->save();

            $results[] = [
                'medicine_id' => $item['medicine_id'],
                'previous_quantity' => $stock->quantity + $item['quantity'],
                'new_quantity' => $stock->quantity,
            ];
        }

        return response()->json([
            'status' => empty($errors) ? 'success' : 'partial',
            'deducted' => $results,
            'errors' => $errors,
        ]);
    }
}
