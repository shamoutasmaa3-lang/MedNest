<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsultationReplyNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

   
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New reply from the pharmacist',
            'message' => $this->message->message,
            'consultation_id' => $this->message->consultation_id,
            'sender_id' => $this->message->sender_id,
        ];
    }
}
