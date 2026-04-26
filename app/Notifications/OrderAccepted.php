<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderAccepted extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Order Accepted',
            'message' => "Your order #{$this->order->id} has been accepted.",
            'order_id' => $this->order->id,
            'status' => $this->order->status,
        ];
    }
}
