<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PromotionNotification extends Notification
{
    public function __construct(
        protected string $title,
        protected string $message,
        protected ?string $actionUrl = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'promotion',
            'status' => 'promotion',
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }
}
