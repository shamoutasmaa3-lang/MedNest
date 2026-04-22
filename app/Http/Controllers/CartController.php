<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
   
    public function index()
    {
        $cartItems = Cart::with('medicine')
            ->where('user_id', Auth::id())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->medicine->price;
        });

        return response()->json([
            'status' => 'success',
            'data' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'medicine_id' => $item->medicine_id,
                    'name' => $item->medicine->name,
                    'price' => $item->medicine->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->medicine->price,
                ];
            }),
            'total' => $total,
            'count' => $cartItems->count(),
        ]);
    }

    
    public function addItem(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $medicineId = $request->medicine_id;
        $quantity = $request->quantity;

       
        $cartItem = Cart::where('user_id', $userId)
            ->where('medicine_id', $medicineId)
            ->first();

        if ($cartItem) {
           
            $cartItem->quantity += $quantity;
            $cartItem->save();
            $message = 'Quantity updated in cart';
        } else {
           
            $cartItem = Cart::create([
                'user_id' => $userId,
                'medicine_id' => $medicineId,
                'quantity' => $quantity,
            ]);
            $message = 'Item added to cart';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $cartItem,
        ], 201);
    }

    
   public function removeItem($itemId)
{
    $cartItem = Cart::where('user_id', Auth::id())
        ->where('id', $itemId)
        ->first();

    if (!$cartItem) {
        return response()->json([
            'status' => 'error',
            'message' => 'Cart item not found',
        ], 404);
    }

    $cartItem->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Item removed from cart',
    ]);
}
  
    
    

}