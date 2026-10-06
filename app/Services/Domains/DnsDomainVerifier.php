<?php

namespace App\Services\Domains;

use App\Models\Domain;

/**
 * Production verifier using real DNS queries.
 *
 * A domain is verified when either the TXT ownership record matches the
 * verification token, or the domain already points to the platform
 * (CNAME target for sub domains, A record for apex domains).
 */
class DnsDomainVerifier implements DomainVerifier
{
    public function verify(Domain $domain): array
    {
        $prefix = config('platform.custom_domains.txt_prefix');
        $txtRecords = @dns_get_record($prefix.'.'.$domain->domain, DNS_TXT) ?: [];

        foreach ($txtRecords as $record) {
            if (trim($record['txt'] ?? '') === $domain->verification_token) {
                return $this->pointsToPlatform($domain)
                    ? ['verified' => true, 'reason' => null]
                    : ['verified' => false, 'reason' => 'TXT record valid, tetapi domain belum diarahkan ke server platform.'];
            }
        }

        return ['verified' => false, 'reason' => 'TXT record verifikasi tidak ditemukan. Perubahan DNS bisa memerlukan waktu hingga 24 jam.'];
    }

    private function pointsToPlatform(Domain $domain): bool
    {
        if ($domain->type === Domain::TYPE_SUBDOMAIN) {
            $target = rtrim(config('platform.custom_domains.cname_target'), '.');
            foreach (@dns_get_record($domain->domain, DNS_CNAME) ?: [] as $record) {
                if (rtrim($record['target'] ?? '', '.') === $target) {
                    return true;
                }
            }
        }

        $ip = config('platform.custom_domains.server_ip');
        foreach (@dns_get_record($domain->domain, DNS_A) ?: [] as $record) {
            if (($record['ip'] ?? null) === $ip) {
                return true;
            }
        }

        return false;
    }
}
