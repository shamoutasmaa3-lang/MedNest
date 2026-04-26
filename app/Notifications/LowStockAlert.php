<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Stock;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $stock;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Low Stock Alert',
            'message' => "Low stock for medicine: {$this->stock->medicine->name}. Quantity: {$this->stock->quantity}",
            'medicine_id' => $this->stock->medicine_id,
            'quantity' => $this->stock->quantity,
        ];
    }
}
