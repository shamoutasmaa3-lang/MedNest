<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderRejected extends Notification
{
    use Queueable;

    protected $order;
    protected $reason;

    public function __construct($order, $reason = null)
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Order Rejected',
            'message' => "Your order #{$this->order->id} has been rejected." . 
                ($this->reason ? " Reason: {$this->reason}" : ''),
            'order_id' => $this->order->id,
            'status' => $this->order->status,
        ];
    }
}
