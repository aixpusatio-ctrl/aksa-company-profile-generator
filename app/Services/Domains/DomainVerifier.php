<?php

namespace App\Services\Domains;

use App\Models\Domain;

interface DomainVerifier
{
    /**
     * Check whether the DNS records for the domain point to the platform.
     *
     * @return array{verified: bool, reason: ?string}
     */
    public function verify(Domain $domain): array;
}
