<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\DomainService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Checks the DNS records of a custom domain and activates it when valid.
 */
class VerifyDomainJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $domainId) {}

    public function handle(DomainService $domains): void
    {
        $domain = Domain::query()->with('companyProfile.user')->find($this->domainId);

        if ($domain && $domain->status === Domain::STATUS_VERIFYING) {
            $domains->verify($domain);
        }
    }
}
