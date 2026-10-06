<?php

namespace Tests\Feature;

use App\Models\CompanyPage;
use App\Models\CompanyProfile;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->userWithPlan();
        $this->company = $this->companyFor($this->user, ['name' => 'PT Halaman Kustom'], published: true);
    }

    private function url(string $suffix = ''): string
    {
        return "/dashboard/websites/{$this->company->id}/pages{$suffix}";
    }

    public function test_page_can_be_created_with_auto_generated_slug(): void
    {
        $response = $this->actingAs($this->user)->post($this->url(), [
            'title' => 'Kebijakan Privasi Kami',
            'status' => 'draft',
            'seo_title' => 'Privasi',
        ]);

        $page = CompanyPage::query()->firstOrFail();
        $response->assertRedirect(route('websites.pages.edit', [$this->company, $page]));

        $this->assertSame($this->company->id, $page->company_profile_id);
        $this->assertSame('kebijakan-privasi-kami', $page->slug);
        $this->assertSame('draft', $page->status);
        $this->assertSame('Privasi', $page->seo_title);
        $this->assertSame(0, $this->company->menus()->where('type', Menu::TYPE_PAGE)->count());
    }

    public function test_custom_slug_is_normalized(): void
    {
        $this->actingAs($this->user)->post($this->url(), ['title' => 'Karir', 'slug' => 'Lowongan Kerja 2026', 'status' => 'published'])
            ->assertSessionHasNoErrors();

        $this->assertSame('lowongan-kerja-2026', CompanyPage::query()->value('slug'));
    }

    public function test_reserved_slugs_are_rejected(): void
    {
        foreach (['contact', 'sitemap', 'robots', 'api', 'storage', 'build', 'up'] as $slug) {
            $this->actingAs($this->user)->post($this->url(), ['title' => 'X', 'slug' => $slug, 'status' => 'draft'])
                ->assertSessionHasErrors('slug');
        }

        $this->actingAs($this->user)->post($this->url(), ['title' => 'Contact', 'status' => 'draft'])
            ->assertSessionHasErrors('slug');

        $this->assertSame(0, CompanyPage::query()->count());
    }

    public function test_slug_is_unique_per_company(): void
    {
        $this->company->pages()->create(['title' => 'Karir', 'slug' => 'karir', 'status' => 'draft']);
        $other = $this->companyFor($this->user);
        $other->pages()->create(['title' => 'Layanan', 'slug' => 'layanan', 'status' => 'draft']);

        $this->actingAs($this->user)->post($this->url(), ['title' => 'Karir', 'status' => 'draft'])
            ->assertSessionHasErrors('slug');

        // Same slug in another company is fine.
        $this->actingAs($this->user)->post($this->url(), ['title' => 'Layanan', 'status' => 'draft'])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, $this->company->pages()->count());
    }

    public function test_page_validation(): void
    {
        $this->actingAs($this->user)->post($this->url(), ['status' => 'live'])
            ->assertSessionHasErrors(['title', 'status']);
    }

    public function test_page_can_be_updated_keeping_its_own_slug(): void
    {
        $page = $this->company->pages()->create(['title' => 'Karir', 'slug' => 'karir', 'status' => 'draft']);

        $this->actingAs($this->user)->put($this->url('/'.$page->id), [
            'title' => 'Karir & Lowongan', 'slug' => 'karir', 'status' => 'published',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $page->refresh();
        $this->assertSame('Karir & Lowongan', $page->title);
        $this->assertSame('karir', $page->slug);
        $this->assertSame('published', $page->status);
    }

    public function test_page_content_is_sanitized(): void
    {
        $this->actingAs($this->user)->post($this->url(), [
            'title' => 'Tentang',
            'status' => 'published',
            'content' => '<h2>Halo</h2><p onmouseover="x()">Isi</p><script>alert(1)</script><a href="javascript:alert(2)">x</a><style>body{}</style>',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $content = CompanyPage::query()->value('content');
        $this->assertStringContainsString('<h2>Halo</h2>', $content);
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('onmouseover', $content);
        $this->assertStringNotContainsString('javascript:', $content);
        $this->assertStringNotContainsString('<style', $content);
    }

    public function test_add_to_menu_creates_a_page_menu_item(): void
    {
        $this->actingAs($this->user)->post($this->url(), ['title' => 'Karir', 'status' => 'published', 'add_to_menu' => '1'])
            ->assertSessionHasNoErrors();

        $page = CompanyPage::query()->firstOrFail();
        $menu = $this->company->menus()->where('type', Menu::TYPE_PAGE)->firstOrFail();

        $this->assertSame('Karir', $menu->title);
        $this->assertSame($page->id, $menu->company_page_id);
        $this->assertNull($menu->parent_id);
        $this->assertSame(8, (int) $menu->sort_order, 'Appended after the 7 default menus.');
    }

    public function test_page_can_be_deleted(): void
    {
        $page = $this->company->pages()->create(['title' => 'Karir', 'slug' => 'karir', 'status' => 'draft']);
        $menu = $this->company->menus()->create(['title' => 'Karir', 'type' => 'page', 'company_page_id' => $page->id]);

        $this->actingAs($this->user)->delete($this->url('/'.$page->id))
            ->assertRedirect(route('websites.pages.index', $this->company));

        $this->assertModelMissing($page);
        $this->assertNull($menu->fresh()->company_page_id, 'Menu link is detached (nullOnDelete).');
    }

    public function test_published_pages_render_publicly(): void
    {
        $this->company->pages()->create(['title' => 'Kebijakan Privasi', 'slug' => 'kebijakan-privasi', 'status' => 'published', 'content' => '<p>Isi kebijakan privasi kami.</p>']);

        $this->get($this->tenantUrl($this->company, '/kebijakan-privasi'))
            ->assertOk()
            ->assertSee('Kebijakan Privasi')
            ->assertSee('Isi kebijakan privasi kami.')
            ->assertSee('<title>Kebijakan Privasi | PT Halaman Kustom</title>', false);
    }

    public function test_draft_pages_are_hidden_publicly(): void
    {
        $this->company->pages()->create(['title' => 'Rahasia', 'slug' => 'rahasia', 'status' => 'draft']);

        $this->get($this->tenantUrl($this->company, '/rahasia'))->assertNotFound();
        $this->get($this->tenantUrl($this->company, '/does-not-exist'))->assertNotFound();
    }

    public function test_draft_pages_are_visible_in_owner_preview(): void
    {
        $this->company->pages()->create(['title' => 'Draf Halaman', 'slug' => 'draf-halaman', 'status' => 'draft', 'content' => '<p>Konten draf</p>']);

        $this->actingAs($this->user)
            ->get("/dashboard/websites/{$this->company->id}/preview/frame?page=draf-halaman")
            ->assertOk()
            ->assertSee('Konten draf')
            ->assertSee('noindex,nofollow', false);

        $this->actingAs($this->user)
            ->get("/dashboard/websites/{$this->company->id}/preview/frame?page=unknown")
            ->assertNotFound();
    }

    public function test_menu_links_to_draft_pages_are_hidden_publicly(): void
    {
        $draft = $this->company->pages()->create(['title' => 'Halaman Draf', 'slug' => 'halaman-draf', 'status' => 'draft']);
        $published = $this->company->pages()->create(['title' => 'Halaman Live', 'slug' => 'halaman-live', 'status' => 'published']);
        $this->company->menus()->create(['title' => 'Menu Draf Unik', 'type' => 'page', 'company_page_id' => $draft->id, 'sort_order' => 20]);
        $this->company->menus()->create(['title' => 'Menu Live Unik', 'type' => 'page', 'company_page_id' => $published->id, 'sort_order' => 21]);

        $this->get($this->tenantUrl($this->company))
            ->assertOk()
            ->assertSee('Menu Live Unik')
            ->assertSee('/halaman-live', false)
            ->assertDontSee('Menu Draf Unik');

        $this->actingAs($this->user)
            ->get($this->centralUrl("/dashboard/websites/{$this->company->id}/preview/frame"))
            ->assertOk()
            ->assertSee('Menu Draf Unik');
    }
}
