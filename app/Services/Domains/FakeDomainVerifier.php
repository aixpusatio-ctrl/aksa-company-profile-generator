<?php

namespace App\Services\Domains;

use App\Models\Domain;

/**
 * Development/demo verifier: no real DNS lookups are made.
 *
 * Every domain verifies successfully, except domains containing "fail"
 * or ending in ".invalid", which lets you demo the failure flow.
 */
class FakeDomainVerifier implements DomainVerifier
{
    public function verify(Domain $domain): array
    {
        if (str_contains($domain->domain, 'fail') || str_ends_with($domain->domain, '.invalid')) {
            return ['verified' => false, 'reason' => 'DNS record tidak ditemukan (simulasi). Pastikan CNAME/A record sudah benar.'];
        }

        return ['verified' => true, 'reason' => null];
    }
}
