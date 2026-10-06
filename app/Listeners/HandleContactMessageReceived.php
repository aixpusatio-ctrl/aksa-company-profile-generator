<?php

namespace App\Listeners;

use App\Events\ContactMessageReceived;
use App\Notifications\NewContactMessageNotification;

class HandleContactMessageReceived
{
    public function handle(ContactMessageReceived $event): void
    {
        $event->message->companyProfile?->user?->notify(new NewContactMessageNotification($event->message));
    }
}
