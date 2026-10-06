<?php

namespace App\Events;

use App\Models\CompanyProfile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyProfilePublished
{
    use Dispatchable, SerializesModels;

    public function __construct(public CompanyProfile $company) {}
}
