<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification
{
    use Queueable;

    protected $order;
    protected $user;

    public function __construct($order, $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Order Placed',
            'message' => "User {$this->user->name} placed a new order (#{$this->order->id}).",
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
        ];
    }
}
