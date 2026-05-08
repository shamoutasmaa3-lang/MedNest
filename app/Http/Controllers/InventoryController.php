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

        // فحص نقص الكمية حسب min_quantity
        $lowStockItems = $stocks->filter(function ($stock) {
            return $stock->quantity < $stock->min_quantity;
        });

        if ($lowStockItems->isNotEmpty()) {
            $pharmacists = User::where('role', 'pharmacist')->get();

            foreach ($lowStockItems as $stock) {
                foreach ($pharmacists as $pharmacist) {
                    $pharmacist->notify(new LowStockAlert($stock));
                }
            }
        }

        // فحص قرب انتهاء الصلاحية (30 يوم)
        $expiringSoonItems = $stocks->filter(function ($stock) {
            if (!$stock->expiration_date) return false;
            $daysLeft = (strtotime($stock->expiration_date) - time()) / 86400;
            return $daysLeft <= 30 && $daysLeft >= 0;
        });

        if ($expiringSoonItems->isNotEmpty()) {
            $pharmacists = User::where('role', 'pharmacist')->get();
            foreach ($expiringSoonItems as $stock) {
                foreach ($pharmacists as $pharmacist) {
                    $pharmacist->notify(new \App\Notifications\ExpiringSoonAlert($stock));
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $stocks->map(function ($stock) {
                $daysLeft = $stock->expiration_date
                    ? (strtotime($stock->expiration_date) - time()) / 86400
                    : null;

                return [
                    'id' => $stock->id,
                    'medicine_id' => $stock->medicine_id,
                    'medicine_name' => $stock->medicine->name,
                    'category' => $stock->medicine->category,
                    'quantity' => $stock->quantity,
                    'expiration_date' => $stock->expiration_date,
                    'location' => $stock->location,
                    'min_quantity' => $stock->min_quantity,
                    'is_low_stock' => $stock->quantity < $stock->min_quantity,
                    'is_expiring_soon' => $daysLeft !== null && $daysLeft <= 30 && $daysLeft >= 0,
                ];
            }),
            'low_stock_count' => $lowStockItems->count(),
            'expiring_soon_count' => $expiringSoonItems->count(),
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

    /**
     * POST /api/pharmacist/inventory
     * Add a new stock batch (pharmacist only)
     */
    public function storeStock(Request $request)
    {
        $request->validate([
            'medicine_id'     => 'required|exists:medicines,id',
            'quantity'        => 'required|integer|min:0',
            'min_quantity'    => 'nullable|integer|min:0',
            'location'        => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date|after:today',
            'batch_number'    => 'nullable|string|max:50',
        ]);

        $stock = Stock::create([
            'medicine_id'     => $request->medicine_id,
            'quantity'        => $request->quantity,
            'min_quantity'    => $request->min_quantity ?? 10,
            'location'        => $request->location,
            'expiration_date' => $request->expiration_date,
            'batch_number'    => $request->batch_number,
        ]);

        return response()->json([
            'message' => 'Stock batch added successfully',
            'data'    => $stock->load('medicine'),
        ], 201);
    }

    /**
     * GET /api/pharmacist/inventory/expiring
     * List medicines (batches) expiring in the next X days (default 30)
     */
    public function expiringMedicines(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $threshold = now()->addDays($days);

        $expiringStocks = Stock::with('medicine')
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $threshold)
            ->where('expiration_date', '>=', now())
            ->orderBy('expiration_date')
            ->get();

        return response()->json([
            'status' => 'success',
            'days_threshold' => $days,
            'count' => $expiringStocks->count(),
            'data' => $expiringStocks->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'medicine_id' => $stock->medicine_id,
                    'medicine_name' => $stock->medicine->name,
                    'batch_number' => $stock->batch_number ?? 'N/A',
                    'expiration_date' => $stock->expiration_date,
                    'days_left' => (int)((strtotime($stock->expiration_date) - time()) / 86400),
                    'quantity' => $stock->quantity,
                    'location' => $stock->location,
                ];
            }),
        ]);
    }
}