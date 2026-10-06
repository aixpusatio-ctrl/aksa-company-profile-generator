<?php

namespace App\Notifications;

use App\Models\Domain;
use Illuminate\Notifications\Notification;

class DomainVerifiedNotification extends Notification
{
    public function __construct(public Domain $domain) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'globe',
            'title' => 'Domain aktif',
            'message' => "Domain {$this->domain->domain} berhasil diverifikasi.",
            'url' => central_url(route('websites.domains.index', $this->domain->company_profile_id, absolute: false)),
        ];
    }
}
