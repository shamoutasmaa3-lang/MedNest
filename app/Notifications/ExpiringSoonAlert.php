<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Stock;

class ExpiringSoonAlert extends Notification
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
            'title' => 'Expiration Alert',
            'message' => "Medicine {$this->stock->medicine->name} is expiring soon on {$this->stock->expiration_date}.",
            'medicine_id' => $this->stock->medicine_id,
            'expiration_date' => $this->stock->expiration_date,
        ];
    }
}
