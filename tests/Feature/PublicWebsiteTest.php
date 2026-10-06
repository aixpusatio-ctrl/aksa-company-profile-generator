<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\ContactMessage;
use App\Models\PageView;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    private const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36';

    private User $owner;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = $this->userWithPlan();
        $this->company = $this->companyFor($this->owner, [
            'name' => 'PT Publik Makmur',
            'slug' => 'publik',
            'tagline' => 'Mitra Terpercaya Anda',
            'description' => 'Perusahaan distribusi terpercaya.',
        ], published: true);
    }

    private function home()
    {
        return $this->withHeader('User-Agent', self::BROWSER)->get($this->tenantUrl($this->company));
    }

    public function test_home_renders_company_information(): void
    {
        $this->home()
            ->assertOk()
            ->assertSee('PT Publik Makmur')
            ->assertSee('Mitra Terpercaya Anda')
            ->assertSee('--brand-primary:#1d4ed8', false);
    }

    public function test_company_data_is_escaped(): void
    {
        $this->company->update(['name' => 'PT <script>alert("x")</script>', 'tagline' => '<img src=x onerror=alert(1)>']);

        $this->home()
            ->assertOk()
            ->assertDontSee('<script>alert("x")</script>', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_branding_overrides_are_applied(): void
    {
        $this->company->update(['branding' => ['primary_color' => '#ff0000']]);

        $this->home()->assertOk()->assertSee('--brand-primary:#ff0000', false);
    }

    public function test_only_enabled_sections_are_rendered(): void
    {
        $this->company->services()->create(['title' => 'Layanan Unik Alpha', 'description' => 'Deskripsi alpha']);

        $this->home()->assertOk()->assertSee('Deskripsi alpha')->assertSee('id="services"', false);

        $sections = $this->company->sections()->get()->map(fn ($s) => [
            'key' => $s->key,
            'is_enabled' => $s->key !== 'services',
        ])->all();

        $this->actingAs($this->owner)
            ->putJson($this->centralUrl("/dashboard/websites/{$this->company->id}/sections"), ['sections' => $sections])
            ->assertOk();

        // (The corporate footer may still list service titles, the section itself is gone.)
        $this->home()->assertOk()->assertDontSee('Deskripsi alpha')->assertDontSee('id="services"', false);
    }

    public function test_sections_without_content_are_skipped(): void
    {
        $this->home()->assertOk()->assertDontSee('id="services"', false)->assertDontSee('id="team"', false);

        $this->company->team()->create(['name' => 'Rina Anggota Tim', 'position' => 'Direktur']);

        $this->home()->assertOk()->assertSee('Rina Anggota Tim');
    }

    public function test_custom_section_titles_are_rendered(): void
    {
        $this->company->services()->create(['title' => 'Layanan A']);
        $this->company->sections()->where('key', 'services')->update(['title' => 'Judul Layanan Kustom']);

        $this->home()->assertOk()->assertSee('Judul Layanan Kustom');
    }

    public function test_menus_are_rendered_in_navigation(): void
    {
        $group = $this->company->menus()->create(['title' => 'Grup Perusahaan', 'type' => 'group', 'sort_order' => 30]);
        $this->company->menus()->create(['title' => 'Sub Visi Misi', 'type' => 'anchor', 'url' => 'about', 'parent_id' => $group->id]);
        $this->company->menus()->create(['title' => 'Portal Mitra', 'type' => 'url', 'url' => 'https://mitra.example.test', 'open_in_new_tab' => true, 'sort_order' => 31]);
        $this->company->menus()->create(['title' => 'Menu Nonaktif', 'type' => 'anchor', 'url' => 'about', 'status' => 'inactive', 'sort_order' => 32]);
        $this->company->menus()->create(['title' => 'Grup Kosong', 'type' => 'group', 'sort_order' => 33]);

        $this->home()
            ->assertOk()
            ->assertSee('Home')
            ->assertSee('href="#services"', false)
            ->assertSee('Grup Perusahaan')
            ->assertSee('Sub Visi Misi')
            ->assertSee('href="https://mitra.example.test" target="_blank" rel="noopener noreferrer"', false)
            ->assertDontSee('Menu Nonaktif')
            ->assertDontSee('Grup Kosong');
    }

    public function test_seo_meta_tags(): void
    {
        $this->company->update([
            'seo_title' => 'Judul SEO Kustom',
            'seo_description' => 'Deskripsi SEO kustom untuk mesin pencari.',
            'seo_keywords' => 'distribusi, logistik',
            'og_title' => 'Judul OG Kustom',
        ]);
        $base = $this->company->publicUrl();

        $this->home()
            ->assertOk()
            ->assertSee('<title>Judul SEO Kustom</title>', false)
            ->assertSee('<meta name="description" content="Deskripsi SEO kustom untuk mesin pencari.">', false)
            ->assertSee('<meta name="keywords" content="distribusi, logistik">', false)
            ->assertSee('<link rel="canonical" href="'.$base.'/">', false)
            ->assertSee('<meta property="og:title" content="Judul OG Kustom">', false)
            ->assertSee('<meta property="og:url" content="'.$base.'/">', false)
            ->assertSee('<meta name="twitter:card" content="summary">', false)
            ->assertSee('<meta name="robots" content="index,follow">', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_seo_defaults_fall_back_to_company_data(): void
    {
        $this->home()
            ->assertOk()
            ->assertSee('<title>PT Publik Makmur — Mitra Terpercaya Anda</title>', false)
            ->assertSee('<meta name="description" content="Perusahaan distribusi terpercaya.">', false);
    }

    public function test_canonical_uses_primary_custom_domain(): void
    {
        $this->company->domains()->create([
            'domain' => 'www.publik-makmur.test', 'type' => 'subdomain', 'verification_token' => 'cpg-x',
            'status' => 'active', 'is_primary' => true,
        ]);

        $this->home()->assertOk()->assertSee('<link rel="canonical" href="http://www.publik-makmur.test/">', false);
    }

    public function test_sitemap_lists_published_pages_only(): void
    {
        $this->company->pages()->create(['title' => 'Karir', 'slug' => 'karir', 'status' => 'published']);
        $this->company->pages()->create(['title' => 'Rahasia', 'slug' => 'rahasia-draf', 'status' => 'draft']);
        $base = $this->company->publicUrl();

        $response = $this->get($this->tenantUrl($this->company, '/sitemap.xml'))->assertOk();

        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $xml = $response->getContent();
        $this->assertNotFalse(simplexml_load_string($xml), 'Sitemap must be valid XML.');
        $this->assertStringContainsString('<loc>'.$base.'/</loc>', $xml);
        $this->assertStringContainsString('<loc>'.$base.'/karir</loc>', $xml);
        $this->assertStringNotContainsString('rahasia-draf', $xml);
    }

    public function test_robots_txt_references_sitemap(): void
    {
        $response = $this->get($this->tenantUrl($this->company, '/robots.txt'))->assertOk();

        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('User-agent: *', $response->getContent());
        $this->assertStringContainsString('Sitemap: '.$this->company->publicUrl().'/sitemap.xml', $response->getContent());
    }

    public function test_contact_form_stores_message_and_notifies_owner(): void
    {
        $this->post($this->tenantUrl($this->company, '/contact'), [
            'name' => 'Rina Pengunjung',
            'email' => 'rina@example.test',
            'phone' => '0812-0000-1111',
            'subject' => 'Permintaan penawaran',
            'message' => 'Mohon kirimkan penawaran harga.',
        ])->assertRedirect($this->tenantUrl($this->company, '/#contact'))
            ->assertSessionHas('contact_success', true);

        $message = ContactMessage::query()->firstOrFail();
        $this->assertSame($this->company->id, $message->company_profile_id);
        $this->assertSame('Rina Pengunjung', $message->name);
        $this->assertSame('Permintaan penawaran', $message->subject);
        $this->assertNotNull($message->ip_address);
        $this->assertNull($message->read_at);

        $notification = $this->owner->notifications()->firstOrFail();
        $this->assertSame(NewContactMessageNotification::class, $notification->type);
        $this->assertStringContainsString('Rina Pengunjung', $notification->data['title']);
        $this->assertStringEndsWith("/dashboard/websites/{$this->company->id}/messages", $notification->data['url']);
    }

    public function test_contact_notification_link_points_to_the_central_dashboard(): void
    {
        // The notification is created while handling a request on the tenant host,
        // but the dashboard only exists on the central host.
        $this->post($this->tenantUrl($this->company, '/contact'), [
            'name' => 'Rina', 'email' => 'rina@example.test', 'message' => 'Mohon info lebih lanjut.',
        ])->assertRedirect();

        $url = $this->owner->notifications()->firstOrFail()->data['url'];
        $host = parse_url($url, PHP_URL_HOST);

        $this->assertTrue(app(\App\Services\DomainService::class)->isCentralHost($host), "Notification URL {$url} must use a central host.");
    }

    public function test_contact_honeypot_silently_discards_spam(): void
    {
        $this->post($this->tenantUrl($this->company, '/contact'), [
            'name' => 'Spam Bot',
            'email' => 'bot@example.test',
            'message' => 'Buy cheap stuff now!!!',
            'website_url' => 'https://spam.example.test',
        ])->assertRedirect($this->tenantUrl($this->company, '/#contact'))
            ->assertSessionHas('contact_success', true);

        $this->assertSame(0, ContactMessage::query()->count());
        $this->assertSame(0, $this->owner->notifications()->count());
    }

    public function test_contact_form_validation_errors(): void
    {
        $this->post($this->tenantUrl($this->company, '/contact'), ['email' => 'not-an-email', 'message' => 'Hi'])
            ->assertRedirect()
            ->assertSessionHasErrorsIn('contact', ['name', 'email', 'message']);

        $this->assertSame(0, ContactMessage::query()->count());
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $payload = ['name' => 'Rina', 'email' => 'rina@example.test', 'message' => 'Pesan berulang-ulang'];

        for ($i = 0; $i < 5; $i++) {
            $this->post($this->tenantUrl($this->company, '/contact'), $payload)->assertRedirect();
        }

        $this->post($this->tenantUrl($this->company, '/contact'), $payload)->assertTooManyRequests();
        $this->assertSame(5, ContactMessage::query()->count());
    }

    public function test_page_views_are_recorded_for_browsers(): void
    {
        $this->home()->assertOk();
        $this->home()->assertOk();

        $views = PageView::query()->where('company_profile_id', $this->company->id)->get();
        $this->assertCount(2, $views);
        $this->assertSame('/', $views->first()->path);
        $this->assertSame(1, $views->pluck('visitor_hash')->unique()->count(), 'Same visitor hashed identically the same day.');

        $stats = app(AnalyticsService::class)->stats($this->company, 7);
        $this->assertSame(2, $stats['total_views']);
        $this->assertSame(1, $stats['total_visitors']);
        $this->assertCount(7, $stats['series']);
    }

    public function test_page_views_are_recorded_for_custom_pages(): void
    {
        $this->company->pages()->create(['title' => 'Karir', 'slug' => 'karir', 'status' => 'published']);

        $this->withHeader('User-Agent', self::BROWSER)->get($this->tenantUrl($this->company, '/karir'))->assertOk();

        $this->assertSame('/karir', PageView::query()->value('path'));
    }

    public function test_bots_are_not_counted_as_page_views(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')
            ->get($this->tenantUrl($this->company))->assertOk();
        $this->withHeader('User-Agent', 'curl/8.0')->get($this->tenantUrl($this->company))->assertOk();

        $this->assertSame(0, PageView::query()->count());
    }

    public function test_owner_preview_does_not_record_page_views(): void
    {
        $this->actingAs($this->owner)
            ->withHeader('User-Agent', self::BROWSER)
            ->get("/dashboard/websites/{$this->company->id}/preview/frame")
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false);

        $this->assertSame(0, PageView::query()->count());
    }
}
