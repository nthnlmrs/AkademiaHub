<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminBroadcast extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $sender_name;

    public function __construct($title, $message, $sender_name = 'System')
    {
        $this->title = $title;
        $this->message = $message;
        $this->sender_name = $sender_name;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'sender_name' => $this->sender_name,
        ];
    }
}
