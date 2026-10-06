<?php

namespace App\Listeners;

use App\Events\DomainVerified;
use App\Notifications\DomainVerifiedNotification;
use App\Support\Activity;

class HandleDomainVerified
{
    public function handle(DomainVerified $event): void
    {
        $domain = $event->domain;
        $owner = $domain->companyProfile?->user;

        Activity::log('domain.verified', "Domain {$domain->domain} aktif", $domain, [], $owner?->getKey());

        $owner?->notify(new DomainVerifiedNotification($domain));
    }
}
