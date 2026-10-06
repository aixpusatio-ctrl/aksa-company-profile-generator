<?php

namespace App\Support;

use App\Models\CompanyProfile;

/**
 * Holds the company profile resolved for the current request host.
 */
class CurrentTenant
{
    private ?CompanyProfile $company = null;

    public function set(?CompanyProfile $company): void
    {
        $this->company = $company;
    }

    public function get(): ?CompanyProfile
    {
        return $this->company;
    }

    public function check(): bool
    {
        return $this->company !== null;
    }
}
