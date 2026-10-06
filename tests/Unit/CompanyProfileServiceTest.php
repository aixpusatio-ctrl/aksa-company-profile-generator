<?php

namespace Tests\Unit;

use App\Services\CompanyProfileService;
use App\Services\DomainService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CompanyProfileServiceTest extends TestCase
{
    public static function validSlugs(): array
    {
        return [['acme'], ['pt-maju-jaya'], ['a1b'], ['123'], ['a-b'], [str_repeat('a', 63)]];
    }

    public static function invalidSlugs(): array
    {
        return [
            'too short' => ['ab'],
            'single char' => ['a'],
            'empty' => [''],
            'leading dash' => ['-acme'],
            'trailing dash' => ['acme-'],
            'uppercase' => ['Acme'],
            'underscore' => ['ac_me'],
            'dot' => ['ac.me'],
            'space' => ['ac me'],
            'too long' => [str_repeat('a', 64)],
            'reserved www' => ['www'],
            'reserved admin' => ['admin'],
            'reserved api' => ['api'],
            'reserved dashboard' => ['dashboard'],
        ];
    }

    #[DataProvider('validSlugs')]
    public function test_valid_slugs(string $slug): void
    {
        $this->assertTrue(CompanyProfileService::isValidSlug($slug));
    }

    #[DataProvider('invalidSlugs')]
    public function test_invalid_slugs(string $slug): void
    {
        $this->assertFalse(CompanyProfileService::isValidSlug($slug));
    }

    public function test_every_reserved_subdomain_is_invalid(): void
    {
        foreach (config('platform.reserved_subdomains') as $reserved) {
            if (strlen($reserved) >= 3) {
                $this->assertFalse(CompanyProfileService::isValidSlug($reserved), $reserved);
            }
        }
    }

    public function test_domain_normalization(): void
    {
        $this->assertSame('www.example.com', DomainService::normalize('  HTTPS://WWW.Example.com:8443/path?q=1 '));
        $this->assertSame('example.com', DomainService::normalize('example.com.'));
        $this->assertSame('acme.localhost', DomainService::normalize('acme.localhost:8000'));
    }
}
