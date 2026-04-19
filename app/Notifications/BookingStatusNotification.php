<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    public function __construct(
        protected string $status,
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
            'type' => 'booking',
            'status' => $this->status,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }
}
