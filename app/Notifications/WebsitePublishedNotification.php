<?php

namespace App\Notifications;

use App\Models\CompanyProfile;
use Illuminate\Notifications\Notification;

class WebsitePublishedNotification extends Notification
{
    public function __construct(public CompanyProfile $company) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'rocket',
            'title' => 'Website dipublikasikan',
            'message' => "{$this->company->name} sekarang live di {$this->company->primaryHost()}",
            'url' => central_url(route('websites.show', $this->company, absolute: false)),
        ];
    }
}
