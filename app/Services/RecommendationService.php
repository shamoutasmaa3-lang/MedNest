<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;

class RecommendationService
{
    public function suggestAlternatives(Medicine $medicine, $limit = 3)
    {
        return Medicine::where('category', $medicine->category)
            ->where('id', '!=', $medicine->id)
            ->take($limit)
            ->get();
    }

    public function getUsageRecommendations(User $user, $ordersLimit = 10, $top = 3)
    {
        $orders = Order::with('items.medicine')
            ->where('user_id', $user->id)
            ->latest()
            ->take($ordersLimit)
            ->get();

        return $orders->flatMap->items
            ->groupBy('medicine_id')
            ->sortByDesc(fn($g) => $g->count())
            ->take($top)
            ->map(function ($group) {
                $item = $group->first();
                return [
                    'medicine_id' => $item->medicine_id,
                    'name' => $item->medicine->name,
                    'times_used' => $group->count(),
                ];
            })
            ->values();
    }

    public function getDosageReminder(User $user)
    {
        $orders = Order::with('items.medicine')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return $orders->flatMap->items
            ->groupBy('medicine_id')
            ->filter(fn($g) => $g->count() >= 2)
            ->map(function ($group) {
                $item = $group->first();
                return [
                    'medicine_id' => $item->medicine_id,
                    'name' => $item->medicine->name,
                    'last_order' => $item->created_at,
                ];
            })
            ->values();
    }
}
