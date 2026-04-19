<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ShowtimeReminderNotification extends Notification
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
            'type' => 'reminder',
            'status' => 'reminder',
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }
}
