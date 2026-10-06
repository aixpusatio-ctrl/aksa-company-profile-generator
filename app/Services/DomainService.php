<?php

namespace App\Services;

use App\Events\DomainVerified;
use App\Jobs\VerifyDomainJob;
use App\Models\CompanyProfile;
use App\Models\Domain;
use App\Services\Domains\DomainVerifier;
use App\Support\Activity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DomainService
{
    public function __construct(private readonly DomainVerifier $verifier) {}

    // ------------------------------------------------------------ Host helpers

    public static function normalize(string $host): string
    {
        $host = strtolower(trim($host));
        $host = preg_replace('#^[a-z]+://#', '', $host);
        $host = explode('/', $host)[0];
        $host = explode(':', $host)[0];

        return rtrim($host, '.');
    }

    public function isCentralHost(string $host): bool
    {
        return in_array(self::normalize($host), array_map('strtolower', config('platform.central_domains')), true);
    }

    /**
     * Extract the tenant slug from "{slug}.{platform domain}" hosts.
     */
    public function subdomainFromHost(string $host): ?string
    {
        $host = self::normalize($host);
        $suffix = '.'.strtolower(config('platform.domain'));

        if (! str_ends_with($host, $suffix)) {
            return null;
        }

        $sub = substr($host, 0, -strlen($suffix));

        return $sub !== '' && ! str_contains($sub, '.') ? $sub : null;
    }

    /**
     * Request host → company profile.
     *
     * 1. Look for an active custom domain (also tries with/without "www.")
     * 2. Fall back to the platform sub domain
     */
    public function resolveHost(string $host): ?CompanyProfile
    {
        $host = self::normalize($host);

        if ($host === '' || $this->isCentralHost($host)) {
            return null;
        }

        $companyId = Cache::remember('tenant-host:'.$host, now()->addMinutes(5), function () use ($host) {
            $alternate = str_starts_with($host, 'www.') ? substr($host, 4) : 'www.'.$host;

            $domain = Domain::query()->active()->where('domain', $host)->first()
                ?? Domain::query()->active()->where('domain', $alternate)->first();

            if ($domain) {
                return $domain->company_profile_id;
            }

            $slug = $this->subdomainFromHost($host);

            return $slug ? CompanyProfile::query()->where('slug', $slug)->value('id') : null;
        });

        return $companyId ? CompanyProfile::query()->with('template')->find($companyId) : null;
    }

    public function forgetHost(string $host): void
    {
        $host = self::normalize($host);
        Cache::forget('tenant-host:'.$host);
        Cache::forget('tenant-host:'.(str_starts_with($host, 'www.') ? substr($host, 4) : 'www.'.$host));
    }

    // ------------------------------------------------------------ Custom domains

    public function add(CompanyProfile $company, string $domain): Domain
    {
        $domain = self::normalize($domain);
        $this->assertAllowed($domain);

        $record = $company->domains()->create([
            'domain' => $domain,
            'type' => substr_count($domain, '.') >= 2 ? Domain::TYPE_SUBDOMAIN : Domain::TYPE_APEX,
            'verification_token' => 'cpg-'.Str::lower(Str::random(32)),
            'status' => Domain::STATUS_PENDING,
            'is_primary' => ! $company->domains()->exists(),
        ]);

        Activity::log('domain.added', "Domain {$domain} ditambahkan ke {$company->name}", $record);

        return $record;
    }

    public function assertAllowed(string $domain): void
    {
        $platform = strtolower(config('platform.domain'));

        $error = match (true) {
            ! preg_match('/^(?=.{4,253}$)([a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $domain) => 'Format domain tidak valid. Contoh: www.perusahaan.com',
            $this->isCentralHost($domain), $domain === $platform, str_ends_with($domain, '.'.$platform) => 'Domain platform tidak dapat digunakan sebagai custom domain.',
            Domain::query()->where('domain', $domain)->exists() => 'Domain sudah terdaftar.',
            default => null,
        };

        if ($error) {
            throw ValidationException::withMessages(['domain' => $error]);
        }
    }

    /**
     * DNS records the customer has to create.
     */
    public function dnsInstructions(Domain $domain): array
    {
        $parts = explode('.', $domain->domain);
        $name = $domain->type === Domain::TYPE_SUBDOMAIN ? implode('.', array_slice($parts, 0, -2)) : '@';
        $prefix = config('platform.custom_domains.txt_prefix');

        $records = [];

        $records[] = $domain->type === Domain::TYPE_SUBDOMAIN
            ? ['type' => 'CNAME', 'name' => $name, 'value' => config('platform.custom_domains.cname_target'), 'ttl' => 'Auto']
            : ['type' => 'A', 'name' => '@', 'value' => config('platform.custom_domains.server_ip'), 'ttl' => 'Auto'];

        $records[] = [
            'type' => 'TXT',
            'name' => $name === '@' ? $prefix : $prefix.'.'.$name,
            'value' => $domain->verification_token,
            'ttl' => 'Auto',
        ];

        return $records;
    }

    /**
     * Mark the domain as verifying and queue the DNS check.
     */
    public function requestVerification(Domain $domain): void
    {
        $domain->update(['status' => Domain::STATUS_VERIFYING, 'failure_reason' => null]);

        VerifyDomainJob::dispatch($domain->getKey());
    }

    public function verify(Domain $domain): Domain
    {
        $result = $this->verifier->verify($domain);

        $domain->forceFill([
            'last_checked_at' => now(),
            'status' => $result['verified'] ? Domain::STATUS_ACTIVE : Domain::STATUS_FAILED,
            'verified_at' => $result['verified'] ? ($domain->verified_at ?? now()) : null,
            'failure_reason' => $result['reason'],
        ])->save();

        $this->forgetHost($domain->domain);

        if ($result['verified']) {
            DomainVerified::dispatch($domain);
        } else {
            Activity::log('domain.failed', "Verifikasi domain {$domain->domain} gagal", $domain, ['reason' => $result['reason']], $domain->companyProfile?->user_id);
        }

        return $domain;
    }

    public function activate(Domain $domain): Domain
    {
        $domain->forceFill(['status' => Domain::STATUS_ACTIVE, 'verified_at' => now(), 'failure_reason' => null, 'last_checked_at' => now()])->save();
        $this->forgetHost($domain->domain);
        DomainVerified::dispatch($domain);

        return $domain;
    }

    public function makePrimary(Domain $domain): void
    {
        Domain::query()->where('company_profile_id', $domain->company_profile_id)->update(['is_primary' => false]);
        $domain->update(['is_primary' => true]);
    }

    public function delete(Domain $domain): void
    {
        $this->forgetHost($domain->domain);
        Activity::log('domain.deleted', "Domain {$domain->domain} dihapus", $domain);
        $domain->delete();
    }
}
