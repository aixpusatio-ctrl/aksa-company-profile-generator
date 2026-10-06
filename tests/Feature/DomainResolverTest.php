<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Domain;
use App\Services\CompanyProfileService;
use App\Services\DomainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainResolverTest extends TestCase
{
    use RefreshDatabase;

    private DomainService $domains;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domains = app(DomainService::class);
    }

    private function addDomain(CompanyProfile $company, string $domain, string $status = Domain::STATUS_ACTIVE): Domain
    {
        return $company->domains()->create([
            'domain' => $domain,
            'type' => substr_count($domain, '.') >= 2 ? Domain::TYPE_SUBDOMAIN : Domain::TYPE_APEX,
            'verification_token' => 'cpg-'.md5($domain),
            'status' => $status,
        ]);
    }

    // ------------------------------------------------------------------ DomainService::resolveHost

    public function test_platform_subdomain_resolves_to_company(): void
    {
        $company = $this->companyFor(null, ['slug' => 'acme']);

        $this->assertTrue($company->is($this->domains->resolveHost('acme.localhost')));
        $this->assertTrue($company->is($this->domains->resolveHost('ACME.localhost:8000')));
        $this->assertTrue($company->is($this->domains->resolveHost('acme.localhost.')));
    }

    public function test_unknown_or_nested_subdomains_do_not_resolve(): void
    {
        $this->companyFor(null, ['slug' => 'acme']);

        $this->assertNull($this->domains->resolveHost('nobody.localhost'));
        $this->assertNull($this->domains->resolveHost('deep.acme.localhost'));
        $this->assertNull($this->domains->resolveHost('acme.other-platform.test'));
        $this->assertNull($this->domains->resolveHost(''));
    }

    public function test_central_hosts_never_resolve_to_a_tenant(): void
    {
        $this->companyFor(null, ['slug' => 'localhost-company']);

        $this->assertNull($this->domains->resolveHost('localhost'));
        $this->assertNull($this->domains->resolveHost('127.0.0.1'));
        $this->assertNull($this->domains->resolveHost('www.localhost'));
        $this->assertTrue($this->domains->isCentralHost('LOCALHOST:8000'));
        $this->assertFalse($this->domains->isCentralHost('acme.localhost'));
    }

    public function test_active_custom_domain_resolves_to_company(): void
    {
        $company = $this->companyFor();
        $this->addDomain($company, 'www.acme-corp.test');

        $this->assertTrue($company->is($this->domains->resolveHost('www.acme-corp.test')));
        $this->assertTrue($company->is($this->domains->resolveHost('https://WWW.ACME-CORP.TEST/some/path')));
    }

    public function test_www_and_apex_alternates_resolve(): void
    {
        $www = $this->companyFor();
        $apex = $this->companyFor();
        $this->addDomain($www, 'www.first.test');
        $this->addDomain($apex, 'second.test');

        $this->assertTrue($www->is($this->domains->resolveHost('first.test')), 'apex → www record');
        $this->assertTrue($apex->is($this->domains->resolveHost('www.second.test')), 'www → apex record');
    }

    public function test_inactive_custom_domains_are_not_resolved(): void
    {
        $company = $this->companyFor();
        $this->addDomain($company, 'pending.test', Domain::STATUS_PENDING);
        $this->addDomain($company, 'verifying.test', Domain::STATUS_VERIFYING);
        $this->addDomain($company, 'failed.test', Domain::STATUS_FAILED);

        $this->assertNull($this->domains->resolveHost('pending.test'));
        $this->assertNull($this->domains->resolveHost('www.verifying.test'));
        $this->assertNull($this->domains->resolveHost('failed.test'));
    }

    public function test_changing_slug_invalidates_cached_host(): void
    {
        $company = $this->companyFor(null, ['slug' => 'lama']);
        $this->assertNotNull($this->domains->resolveHost('lama.localhost'));

        app(CompanyProfileService::class)->update($company, ['slug' => 'baru']);

        $this->assertNull($this->domains->resolveHost('lama.localhost'));
        $this->assertTrue($company->is($this->domains->resolveHost('baru.localhost')));
    }

    public function test_deleting_domain_invalidates_cached_host(): void
    {
        $company = $this->companyFor();
        $domain = $this->addDomain($company, 'www.temp.test');
        $this->assertNotNull($this->domains->resolveHost('www.temp.test'));

        $this->domains->delete($domain);

        $this->assertNull($this->domains->resolveHost('www.temp.test'));
    }

    public function test_subdomain_from_host(): void
    {
        $this->assertSame('acme', $this->domains->subdomainFromHost('acme.localhost'));
        $this->assertNull($this->domains->subdomainFromHost('localhost'));
        $this->assertNull($this->domains->subdomainFromHost('a.b.localhost'));
        $this->assertNull($this->domains->subdomainFromHost('acme.example.com'));
    }

    // ------------------------------------------------------------------ HTTP

    public function test_subdomain_renders_published_site(): void
    {
        $company = $this->companyFor(null, ['slug' => 'acme', 'name' => 'PT Acme Sejahtera'], published: true);

        $this->get('http://acme.localhost/')
            ->assertOk()
            ->assertSee('PT Acme Sejahtera');
    }

    public function test_unknown_subdomain_returns_404(): void
    {
        $this->get('http://nobody.localhost/')->assertNotFound();
        $this->get('http://nobody.localhost/sitemap.xml')->assertNotFound();
    }

    public function test_custom_domain_renders_the_same_company(): void
    {
        $company = $this->companyFor(null, ['name' => 'PT Domain Sendiri'], published: true);
        $this->addDomain($company, 'www.domain-sendiri.test');

        $this->get('http://www.domain-sendiri.test/')->assertOk()->assertSee('PT Domain Sendiri');
        $this->get('http://domain-sendiri.test/')->assertOk()->assertSee('PT Domain Sendiri');
    }

    public function test_pending_custom_domain_returns_404(): void
    {
        $company = $this->companyFor(null, [], published: true);
        $this->addDomain($company, 'www.belum-aktif.test', Domain::STATUS_PENDING);

        $this->get('http://www.belum-aktif.test/')->assertNotFound();
    }

    public function test_central_routes_return_404_on_tenant_hosts(): void
    {
        $company = $this->companyFor(null, ['slug' => 'acme'], published: true);
        $this->actingAs($company->user);

        $this->get('http://acme.localhost/login')->assertNotFound();
        $this->get('http://acme.localhost/register')->assertNotFound();
        $this->get('http://acme.localhost/dashboard')->assertNotFound();
        $this->get('http://acme.localhost/admin')->assertNotFound();
        $this->get('http://acme.localhost/templates')->assertNotFound();
        $this->post('http://acme.localhost/login', ['email' => 'x@example.test', 'password' => 'x'])->assertNotFound();
        $this->post('http://acme.localhost/dashboard/websites', ['name' => 'X'])->assertNotFound();
        $this->get('http://acme.localhost/api/templates')->assertNotFound();
    }

    public function test_tenant_robots_differs_from_central_robots(): void
    {
        $company = $this->companyFor(null, ['slug' => 'acme'], published: true);

        $tenant = $this->get('http://acme.localhost/robots.txt')->assertOk();
        $this->assertStringContainsString('Sitemap: '.$company->publicUrl().'/sitemap.xml', $tenant->getContent());
        $this->assertStringNotContainsString('/dashboard', $tenant->getContent());

        $central = $this->get($this->centralUrl('/robots.txt'))->assertOk();
        $this->assertStringContainsString('Disallow: /dashboard', $central->getContent());
        $this->assertStringContainsString('Disallow: /admin', $central->getContent());
    }

    public function test_central_sitemap_is_xml(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('<urlset', $response->getContent());
    }

    public function test_landing_page_on_central_host(): void
    {
        $this->get('/')->assertOk();
        $this->get('http://www.localhost/')->assertOk();
    }
}
