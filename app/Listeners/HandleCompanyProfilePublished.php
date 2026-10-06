<?php

namespace App\Listeners;

use App\Events\CompanyProfilePublished;
use App\Notifications\WebsitePublishedNotification;
use App\Support\Activity;

class HandleCompanyProfilePublished
{
    public function handle(CompanyProfilePublished $event): void
    {
        $company = $event->company;

        Activity::log('website.published', "Website {$company->name} dipublikasikan", $company, ['url' => $company->publicUrl()]);

        $company->user?->notify(new WebsitePublishedNotification($company));
    }
}
