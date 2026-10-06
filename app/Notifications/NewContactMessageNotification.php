<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewContactMessageNotification extends Notification
{
    public function __construct(public ContactMessage $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'mail',
            'title' => 'Pesan baru dari '.$this->message->name,
            'message' => Str::limit($this->message->message, 90),
            'url' => central_url(route('websites.messages.index', $this->message->company_profile_id, absolute: false)),
        ];
    }
}
