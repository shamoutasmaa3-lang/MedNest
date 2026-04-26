<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderAccepted;
use App\Notifications\OrderRejected;


class OrderController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string|min:10',
            'payment_method' => 'required|in:cash,card,online',
            'notes' => 'nullable|string',
        ]);

        $userId = Auth::id();


        $cartItems = Cart::with('medicine')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty, cannot create order',
            ], 400);
        }


        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->medicine->price;
        });


        DB::beginTransaction();

        try {

            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);


            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'medicine_id' => $item->medicine_id,
                    'quantity' => $item->quantity,
                    'price' => $item->medicine->price,
                ]);
            }


            Cart::where('user_id', $userId)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => $order->load('items.medicine'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the order: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function index()
    {
        $orders = Order::with('items.medicine')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }


    public function show($id)
    {
        $order = Order::with('items.medicine')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }
    
    public function dispense($id)
{
    $order = Order::with('items.medicine')->findOrFail($id);

    // Check order status
    if (!in_array($order->status, ['pending', 'verified'])) {
        return response()->json([
            'status' => 'error',
            'message' => 'Order cannot be dispensed because its status is ' . $order->status
        ], 400);
    }

    DB::beginTransaction();

    try {
        foreach ($order->items as $item) {
            $stock = \App\Models\Stock::where('medicine_id', $item->medicine_id)->lockForUpdate()->first();

            if (!$stock) {
                throw new \Exception("Stock record not found for medicine ID: {$item->medicine_id}");
            }

            if ($stock->quantity < $item->quantity) {
                throw new \Exception(
                    "Insufficient stock for medicine: {$item->medicine->name}. Available: {$stock->quantity}, Required: {$item->quantity}"
                );
            }

            // Deduct stock
            $stock->quantity -= $item->quantity;
            $stock->save();
        }

        // Update order status
        $order->status = 'dispensed';
        $order->save();

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Order dispensed successfully and stock updated',
            'data' => $order->load('items.medicine')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to dispense order: ' . $e->getMessage()
        ], 500);
    }
}
    public function verify($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Order cannot be verified'], 400);
        }
        $order->status = 'verified';
        $order->save();
        $order->user->notify(new OrderAccepted($order));
        return response()->json(['message' => 'Order verified successfully', 'order' => $order]);
    }
    public function reject(Request $request, $id)
{
    $request->validate([
        'reason' => 'nullable|string|max:255',
    ]);

    $order = Order::with('user')->findOrFail($id);

    if ($order->status !== 'pending') {
        return response()->json([
            'status' => 'error',
            'message' => 'Order cannot be rejected because its status is ' . $order->status
        ], 400);
    }

    $order->status = 'rejected';
    $order->save();

    $order->user->notify(new OrderRejected($order, $request->reason));

    return response()->json([
        'status' => 'success',
        'message' => 'Order rejected successfully',
        'order' => $order
    ]);
}

}
