<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SafetyCheckResult extends Notification
{
    use Queueable;

    protected $hasSevere;
    protected $interactions;

    public function __construct($hasSevere, $interactions)
    {
        $this->hasSevere = $hasSevere;
        $this->interactions = $interactions;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->hasSevere ? 'Dangerous Drug Interaction' : 'Safety Check Result',
            'message' => $this->hasSevere
                ? 'Serious drug interactions detected in your medicines.'
                : 'No serious interactions found in your medicines.',
            'has_severe' => $this->hasSevere,
            'interactions' => $this->interactions,
        ];
    }
}
