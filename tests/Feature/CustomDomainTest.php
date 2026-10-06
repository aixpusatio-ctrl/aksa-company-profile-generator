<?php

namespace Tests\Feature;

use App\Events\DomainVerified;
use App\Jobs\VerifyDomainJob;
use App\Models\ActivityLog;
use App\Models\CompanyProfile;
use App\Models\Domain;
use App\Models\User;
use App\Notifications\DomainVerifiedNotification;
use App\Services\DomainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CustomDomainTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        config(['platform.custom_domains.verifier' => 'fake']);
        $this->user = $this->userWithPlan('pro');
        $this->company = $this->companyFor($this->user, [], published: true);
    }

    private function url(string $suffix = ''): string
    {
        return "/dashboard/websites/{$this->company->id}/domains{$suffix}";
    }

    private function addDomain(string $domain)
    {
        return $this->actingAs($this->user)->post($this->url(), ['domain' => $domain]);
    }

    // ------------------------------------------------------------------ Adding

    public function test_user_can_add_a_custom_domain(): void
    {
        $this->addDomain('WWW.Perusahaan-Saya.com')->assertRedirect()->assertSessionHasNoErrors();

        $domain = Domain::query()->firstOrFail();
        $this->assertSame($this->company->id, $domain->company_profile_id);
        $this->assertSame('www.perusahaan-saya.com', $domain->domain);
        $this->assertSame(Domain::TYPE_SUBDOMAIN, $domain->type);
        $this->assertSame(Domain::STATUS_PENDING, $domain->status);
        $this->assertTrue($domain->is_primary, 'First domain becomes primary.');
        $this->assertMatchesRegularExpression('/^cpg-[a-z0-9]{32}$/', $domain->verification_token);
        $this->assertTrue(ActivityLog::query()->where('action', 'domain.added')->exists());
    }

    public function test_url_input_is_normalized_and_apex_detected(): void
    {
        $this->addDomain('https://Perusahaan-Saya.com/tentang?x=1')->assertSessionHasNoErrors();
        $this->addDomain('shop.perusahaan-saya.com')->assertSessionHasNoErrors();

        $apex = Domain::query()->where('domain', 'perusahaan-saya.com')->firstOrFail();
        $sub = Domain::query()->where('domain', 'shop.perusahaan-saya.com')->firstOrFail();
        $this->assertSame(Domain::TYPE_APEX, $apex->type);
        $this->assertTrue($apex->is_primary);
        $this->assertSame(Domain::TYPE_SUBDOMAIN, $sub->type);
        $this->assertFalse($sub->is_primary, 'Only the first domain is primary.');
    }

    public function test_apex_domain_on_second_level_tld_is_detected_as_apex(): void
    {
        // Common in Indonesia: perusahaan.co.id is a root domain, a CNAME is not allowed there.
        $this->addDomain('perusahaan.co.id')->assertSessionHasNoErrors();

        $domain = Domain::query()->firstOrFail();
        $this->assertSame(Domain::TYPE_APEX, $domain->type);
        $this->assertSame('A', app(DomainService::class)->dnsInstructions($domain)[0]['type']);

        $this->addDomain('www.perusahaan.co.id')->assertSessionHasNoErrors();
        $www = Domain::query()->where('domain', 'www.perusahaan.co.id')->firstOrFail();
        $records = app(DomainService::class)->dnsInstructions($www);
        $this->assertSame(Domain::TYPE_SUBDOMAIN, $www->type);
        $this->assertSame(['CNAME', 'www'], [$records[0]['type'], $records[0]['name']]);
        $this->assertSame('_cpg-verify.www', $records[1]['name']);
    }

    public function test_invalid_domain_formats_are_rejected(): void
    {
        foreach (['not a domain', 'localhost-only', 'exa mple.com', '-bad.com', 'bad-.com', 'a.b', 'example.c0m', str_repeat('a', 64).'.com', ''] as $invalid) {
            $this->addDomain($invalid)->assertSessionHasErrors('domain');
        }

        $this->assertSame(0, Domain::query()->count());
    }

    public function test_platform_domains_are_rejected(): void
    {
        $platform = config('platform.domain');

        foreach (["acme.{$platform}", $platform, "www.{$platform}", '127.0.0.1'] as $domain) {
            $this->addDomain($domain)->assertSessionHasErrors('domain');
        }

        $this->assertSame(0, Domain::query()->count());
    }

    public function test_duplicate_domains_are_rejected_across_companies(): void
    {
        $this->addDomain('www.duplikat.com')->assertSessionHasNoErrors();
        $this->addDomain('WWW.DUPLIKAT.COM')->assertSessionHasErrors('domain');

        $other = $this->userWithPlan('pro');
        $otherCompany = $this->companyFor($other);
        $this->actingAs($other)->post("/dashboard/websites/{$otherCompany->id}/domains", ['domain' => 'www.duplikat.com'])
            ->assertSessionHasErrors('domain');

        $this->assertSame(1, Domain::query()->count());
    }

    public function test_plan_without_custom_domain_gets_403(): void
    {
        $free = $this->userWithPlan('free');
        $company = $this->companyFor($free);

        $this->actingAs($free)->post("/dashboard/websites/{$company->id}/domains", ['domain' => 'www.gratis.com'])
            ->assertForbidden();

        $this->assertSame(0, Domain::query()->count());
    }

    // ------------------------------------------------------------------ DNS instructions

    public function test_dns_instructions_for_www_domain_use_cname(): void
    {
        $domain = app(DomainService::class)->add($this->company, 'www.perusahaan.com');
        $records = app(DomainService::class)->dnsInstructions($domain);

        $this->assertSame([
            ['type' => 'CNAME', 'name' => 'www', 'value' => config('platform.custom_domains.cname_target'), 'ttl' => 'Auto'],
            ['type' => 'TXT', 'name' => '_cpg-verify.www', 'value' => $domain->verification_token, 'ttl' => 'Auto'],
        ], $records);
    }

    public function test_dns_instructions_for_apex_domain_use_a_record(): void
    {
        $domain = app(DomainService::class)->add($this->company, 'perusahaan.com');
        $records = app(DomainService::class)->dnsInstructions($domain);

        $this->assertSame([
            ['type' => 'A', 'name' => '@', 'value' => config('platform.custom_domains.server_ip'), 'ttl' => 'Auto'],
            ['type' => 'TXT', 'name' => '_cpg-verify', 'value' => $domain->verification_token, 'ttl' => 'Auto'],
        ], $records);
    }

    public function test_dns_instructions_for_deep_subdomain(): void
    {
        $domain = app(DomainService::class)->add($this->company, 'profil.cabang.perusahaan.com');
        $records = app(DomainService::class)->dnsInstructions($domain);

        $this->assertSame('CNAME', $records[0]['type']);
        $this->assertSame('profil.cabang', $records[0]['name']);
        $this->assertSame('_cpg-verify.profil.cabang', $records[1]['name']);
    }

    // ------------------------------------------------------------------ Verification

    public function test_verification_request_queues_the_dns_check(): void
    {
        Queue::fake();
        $domain = app(DomainService::class)->add($this->company, 'www.antri.com');

        $this->actingAs($this->user)->post($this->url('/'.$domain->id.'/verify'))->assertRedirect();

        $this->assertSame(Domain::STATUS_VERIFYING, $domain->fresh()->status);
        Queue::assertPushed(VerifyDomainJob::class, fn (VerifyDomainJob $job) => $job->domainId === $domain->id);
    }

    public function test_verify_activates_domain_and_fires_event(): void
    {
        Event::fake([DomainVerified::class]);
        $domain = app(DomainService::class)->add($this->company, 'www.sukses.com');

        $this->actingAs($this->user)->post($this->url('/'.$domain->id.'/verify'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $domain->refresh();
        $this->assertSame(Domain::STATUS_ACTIVE, $domain->status);
        $this->assertNotNull($domain->verified_at);
        $this->assertNotNull($domain->last_checked_at);
        $this->assertNull($domain->failure_reason);
        Event::assertDispatched(DomainVerified::class, fn (DomainVerified $e) => $e->domain->is($domain));
    }

    public function test_verified_domain_notifies_owner_and_logs_activity(): void
    {
        $domain = app(DomainService::class)->add($this->company, 'www.sukses.com');

        $this->actingAs($this->user)->post($this->url('/'.$domain->id.'/verify'))->assertRedirect();

        $notification = $this->user->notifications()->firstOrFail();
        $this->assertSame(DomainVerifiedNotification::class, $notification->type);
        $this->assertStringContainsString('www.sukses.com', $notification->data['message']);
        $this->assertTrue(ActivityLog::query()->where('action', 'domain.verified')->where('user_id', $this->user->id)->exists());

        // The site is now served on the custom domain.
        $this->get('http://www.sukses.com/')->assertOk()->assertSee(e($this->company->name), false);
    }

    public function test_failing_domain_is_marked_failed_with_reason(): void
    {
        Event::fake([DomainVerified::class]);
        $domain = app(DomainService::class)->add($this->company, 'www.will-fail.com');

        $this->actingAs($this->user)->post($this->url('/'.$domain->id.'/verify'))
            ->assertRedirect()
            ->assertSessionHas('error');

        $domain->refresh();
        $this->assertSame(Domain::STATUS_FAILED, $domain->status);
        $this->assertNotEmpty($domain->failure_reason);
        $this->assertNull($domain->verified_at);
        Event::assertNotDispatched(DomainVerified::class);
        $this->assertTrue(ActivityLog::query()->where('action', 'domain.failed')->exists());
        $this->assertSame(0, $this->user->notifications()->count());

        $this->get('http://www.will-fail.com/')->assertNotFound();
    }

    public function test_dot_invalid_domains_fail_verification_with_fake_verifier(): void
    {
        $domain = app(DomainService::class)->add($this->company, 'www.contoh.invalid');

        app(DomainService::class)->verify($domain);

        $this->assertSame(Domain::STATUS_FAILED, $domain->fresh()->status);
    }

    public function test_failed_domain_can_be_retried(): void
    {
        $domain = app(DomainService::class)->add($this->company, 'www.retry.com');
        $domain->update(['status' => Domain::STATUS_FAILED, 'failure_reason' => 'old reason']);

        $this->actingAs($this->user)->post($this->url('/'.$domain->id.'/verify'));

        $domain->refresh();
        $this->assertSame(Domain::STATUS_ACTIVE, $domain->status);
        $this->assertNull($domain->failure_reason);
    }

    // ------------------------------------------------------------------ Primary & delete

    public function test_domain_can_be_made_primary(): void
    {
        $service = app(DomainService::class);
        $first = $service->add($this->company, 'www.satu.com');
        $second = $service->add($this->company, 'www.dua.com');
        $this->assertTrue($first->fresh()->is_primary);
        $this->assertFalse($second->fresh()->is_primary);

        $this->actingAs($this->user)->post($this->url('/'.$second->id.'/primary'))->assertRedirect();

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
    }

    public function test_domain_can_be_deleted(): void
    {
        $domain = app(DomainService::class)->add($this->company, 'www.hapus.com');
        app(DomainService::class)->activate($domain);
        $this->get('http://www.hapus.com/')->assertOk();

        $this->actingAs($this->user)->delete($this->centralUrl($this->url('/'.$domain->id)))->assertRedirect();

        $this->assertModelMissing($domain);
        $this->assertTrue(ActivityLog::query()->where('action', 'domain.deleted')->exists());
        $this->get('http://www.hapus.com/')->assertNotFound();
    }

    // ------------------------------------------------------------------ Public URL

    public function test_public_url_uses_primary_active_custom_domain(): void
    {
        $scheme = config('platform.scheme');
        $service = app(DomainService::class);

        $this->assertSame($this->company->subdomainUrl(), $this->company->publicUrl());
        $this->assertStringContainsString($this->company->slug.'.'.config('platform.domain'), $this->company->publicUrl());

        $secondary = $service->add($this->company, 'www.kedua.com');
        $primary = $service->add($this->company, 'www.utama.com');
        $service->makePrimary($primary);

        // Pending domains are never used.
        $this->assertSame($this->company->subdomainHost(), $this->company->fresh()->primaryHost());

        $service->activate($secondary);
        $this->assertSame('www.kedua.com', $this->company->fresh()->primaryHost());

        $service->activate($primary);
        $company = $this->company->fresh();
        $this->assertSame('www.utama.com', $company->primaryHost());
        $this->assertSame($scheme.'://www.utama.com', $company->publicUrl(), 'No platform port on custom domains.');
    }
}
