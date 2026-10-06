<?php

namespace Tests\Feature;

use App\Models\CompanyPage;
use App\Models\CompanyProfile;
use App\Models\ContactMessage;
use App\Models\Domain;
use App\Models\Media;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $alice;

    private User $bob;

    private CompanyProfile $aliceCompany;

    private CompanyProfile $bobCompany;

    private CompanyPage $bobPage;

    private Menu $bobMenu;

    private Domain $bobDomain;

    private ContactMessage $bobMessage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alice = $this->userWithPlan('pro');
        $this->bob = $this->userWithPlan('pro');
        $this->aliceCompany = $this->companyFor($this->alice);
        $this->bobCompany = $this->companyFor($this->bob, ['name' => 'PT Bob Original']);

        $this->bobPage = $this->bobCompany->pages()->create(['title' => 'Karir', 'slug' => 'karir', 'status' => 'published']);
        $this->bobMenu = $this->bobCompany->menus()->first();
        $this->bobDomain = $this->bobCompany->domains()->create([
            'domain' => 'www.bob-company.test', 'type' => Domain::TYPE_SUBDOMAIN,
            'verification_token' => 'cpg-bob', 'status' => Domain::STATUS_PENDING, 'is_primary' => true,
        ]);
        $this->bobMessage = $this->bobCompany->contactMessages()->create([
            'name' => 'Visitor', 'email' => 'visitor@example.test', 'message' => 'Halo Bob, apa kabar?',
        ]);
        $this->bobCompany->services()->create(['title' => 'Bob Service']);
    }

    private function bobUrl(string $path = ''): string
    {
        return '/dashboard/websites/'.$this->bobCompany->id.$path;
    }

    private function aliceUrl(string $path = ''): string
    {
        return '/dashboard/websites/'.$this->aliceCompany->id.$path;
    }

    public function test_user_cannot_view_or_preview_another_users_website(): void
    {
        $this->actingAs($this->alice);

        $this->get($this->bobUrl())->assertForbidden();
        $this->get($this->bobUrl('/preview'))->assertForbidden();
        $this->get($this->bobUrl('/preview/frame'))->assertForbidden();
        $this->get($this->bobUrl('/wizard'))->assertForbidden();
        $this->get($this->bobUrl('/wizard/company'))->assertForbidden();
        $this->get($this->bobUrl('/edit/info'))->assertForbidden();
        $this->get($this->bobUrl('/template'))->assertForbidden();
        $this->get($this->bobUrl('/sections'))->assertForbidden();
    }

    public function test_user_cannot_update_another_users_website(): void
    {
        $this->actingAs($this->alice);

        $this->put($this->bobUrl('/edit/info'), ['name' => 'Hacked'])->assertForbidden();
        $this->post($this->bobUrl('/wizard/company'), ['name' => 'Hacked'])->assertForbidden();
        $this->postJson($this->bobUrl('/autosave'), ['_tab' => 'info', 'name' => 'Hacked'])->assertForbidden();
        $this->put($this->bobUrl('/template'), ['template' => $this->aliceCompany->template->slug])->assertForbidden();
        $this->put($this->bobUrl('/domains/subdomain'), ['slug' => 'hacked-slug'])->assertForbidden();
        $this->putJson($this->bobUrl('/sections'), ['sections' => [['key' => 'hero', 'is_enabled' => false]]])->assertForbidden();

        $fresh = $this->bobCompany->fresh();
        $this->assertSame('PT Bob Original', $fresh->name);
        $this->assertNotSame('hacked-slug', $fresh->slug);
        $this->assertTrue((bool) $fresh->sections()->where('key', 'hero')->value('is_enabled'));
    }

    public function test_user_cannot_delete_another_users_website(): void
    {
        $this->actingAs($this->alice)
            ->delete($this->bobUrl(), ['confirm_name' => 'PT Bob Original'])
            ->assertForbidden();

        $this->assertModelExists($this->bobCompany);
    }

    public function test_user_cannot_publish_or_unpublish_another_users_website(): void
    {
        $this->actingAs($this->alice);

        $this->post($this->bobUrl('/publish'))->assertForbidden();
        $this->assertSame(CompanyProfile::STATUS_DRAFT, $this->bobCompany->fresh()->status);

        $this->bobCompany->update(['status' => CompanyProfile::STATUS_PUBLISHED]);
        $this->post($this->bobUrl('/unpublish'))->assertForbidden();
        $this->assertSame(CompanyProfile::STATUS_PUBLISHED, $this->bobCompany->fresh()->status);
    }

    public function test_user_cannot_manage_another_users_content_items(): void
    {
        $this->actingAs($this->alice);
        $service = $this->bobCompany->services()->first();

        $this->get($this->bobUrl('/content/services'))->assertForbidden();
        $this->post($this->bobUrl('/content/services'), ['title' => 'Injected'])->assertForbidden();
        $this->put($this->bobUrl('/content/services/'.$service->id), ['title' => 'Hacked'])->assertForbidden();
        $this->delete($this->bobUrl('/content/services/'.$service->id))->assertForbidden();
        $this->postJson($this->bobUrl('/content/services/reorder'), ['ids' => [$service->id]])->assertForbidden();

        $this->assertSame('Bob Service', $service->fresh()->title);
        $this->assertSame(1, $this->bobCompany->services()->count());
    }

    public function test_content_item_ids_of_another_company_are_not_found(): void
    {
        $service = $this->bobCompany->services()->first();

        $this->actingAs($this->alice)
            ->put($this->aliceUrl('/content/services/'.$service->id), ['title' => 'Hacked'])
            ->assertNotFound();
        $this->actingAs($this->alice)
            ->delete($this->aliceUrl('/content/services/'.$service->id))
            ->assertNotFound();

        $this->assertSame('Bob Service', $service->fresh()->title);
    }

    public function test_reorder_ignores_ids_of_another_company(): void
    {
        $service = $this->bobCompany->services()->first();
        $service->update(['sort_order' => 7]);

        $this->actingAs($this->alice)
            ->postJson($this->aliceUrl('/content/services/reorder'), ['ids' => [$service->id]])
            ->assertOk();

        $this->assertSame(7, (int) $service->fresh()->sort_order);
    }

    public function test_user_cannot_manage_another_users_pages(): void
    {
        $this->actingAs($this->alice);

        $this->get($this->bobUrl('/pages'))->assertForbidden();
        $this->get($this->bobUrl('/pages/create'))->assertForbidden();
        $this->post($this->bobUrl('/pages'), ['title' => 'Injected', 'status' => 'draft'])->assertForbidden();
        $this->get($this->bobUrl('/pages/'.$this->bobPage->id.'/edit'))->assertForbidden();
        $this->put($this->bobUrl('/pages/'.$this->bobPage->id), ['title' => 'Hacked', 'status' => 'draft'])->assertForbidden();
        $this->delete($this->bobUrl('/pages/'.$this->bobPage->id))->assertForbidden();

        $this->assertSame('Karir', $this->bobPage->fresh()->title);
    }

    public function test_page_of_another_company_is_not_found_via_scoped_binding(): void
    {
        $this->actingAs($this->alice);

        $this->get($this->aliceUrl('/pages/'.$this->bobPage->id.'/edit'))->assertNotFound();
        $this->put($this->aliceUrl('/pages/'.$this->bobPage->id), ['title' => 'Hacked', 'status' => 'draft'])->assertNotFound();
        $this->delete($this->aliceUrl('/pages/'.$this->bobPage->id))->assertNotFound();

        $this->assertModelExists($this->bobPage);
        $this->assertSame('Karir', $this->bobPage->fresh()->title);
    }

    public function test_user_cannot_manage_another_users_menus(): void
    {
        $this->actingAs($this->alice);

        $this->get($this->bobUrl('/menus'))->assertForbidden();
        $this->post($this->bobUrl('/menus'), ['title' => 'X', 'type' => 'url', 'url' => 'https://evil.test'])->assertForbidden();
        $this->put($this->bobUrl('/menus/'.$this->bobMenu->id), ['title' => 'X', 'type' => 'url', 'url' => 'https://evil.test'])->assertForbidden();
        $this->delete($this->bobUrl('/menus/'.$this->bobMenu->id))->assertForbidden();
        $this->postJson($this->bobUrl('/menus/tree'), ['tree' => []])->assertForbidden();

        $this->assertModelExists($this->bobMenu);
    }

    public function test_menu_of_another_company_is_not_found_via_scoped_binding(): void
    {
        $this->actingAs($this->alice);

        $this->put($this->aliceUrl('/menus/'.$this->bobMenu->id), ['title' => 'X', 'type' => 'anchor', 'url' => 'about'])->assertNotFound();
        $this->delete($this->aliceUrl('/menus/'.$this->bobMenu->id))->assertNotFound();

        $this->assertModelExists($this->bobMenu);
        $this->assertSame('Home', $this->bobMenu->fresh()->title);
    }

    public function test_user_cannot_manage_another_users_domains(): void
    {
        $this->actingAs($this->alice);

        $this->get($this->bobUrl('/domains'))->assertForbidden();
        $this->post($this->bobUrl('/domains'), ['domain' => 'www.injected.test'])->assertForbidden();
        $this->post($this->bobUrl('/domains/'.$this->bobDomain->id.'/verify'))->assertForbidden();
        $this->post($this->bobUrl('/domains/'.$this->bobDomain->id.'/primary'))->assertForbidden();
        $this->delete($this->bobUrl('/domains/'.$this->bobDomain->id))->assertForbidden();

        $this->assertModelExists($this->bobDomain);
        $this->assertSame(Domain::STATUS_PENDING, $this->bobDomain->fresh()->status);
        $this->assertDatabaseMissing('domains', ['domain' => 'www.injected.test']);
    }

    public function test_domain_of_another_company_is_not_found_via_scoped_binding(): void
    {
        $this->actingAs($this->alice);

        $this->post($this->aliceUrl('/domains/'.$this->bobDomain->id.'/verify'))->assertNotFound();
        $this->post($this->aliceUrl('/domains/'.$this->bobDomain->id.'/primary'))->assertNotFound();
        $this->delete($this->aliceUrl('/domains/'.$this->bobDomain->id))->assertNotFound();

        $this->assertModelExists($this->bobDomain);
        $this->assertSame(Domain::STATUS_PENDING, $this->bobDomain->fresh()->status);
    }

    public function test_user_cannot_read_or_manage_another_users_messages(): void
    {
        $this->actingAs($this->alice);

        $this->get($this->bobUrl('/messages'))->assertForbidden();
        $this->patch($this->bobUrl('/messages/'.$this->bobMessage->id))->assertForbidden();
        $this->delete($this->bobUrl('/messages/'.$this->bobMessage->id))->assertForbidden();

        $this->assertModelExists($this->bobMessage);
        $this->assertNull($this->bobMessage->fresh()->read_at);
    }

    public function test_message_of_another_company_is_not_found_via_scoped_binding(): void
    {
        $this->actingAs($this->alice);

        $this->patch($this->aliceUrl('/messages/'.$this->bobMessage->id))->assertNotFound();
        $this->delete($this->aliceUrl('/messages/'.$this->bobMessage->id))->assertNotFound();

        $this->assertModelExists($this->bobMessage);
    }

    public function test_owner_can_toggle_and_delete_own_messages(): void
    {
        $this->actingAs($this->bob);

        $this->patch($this->bobUrl('/messages/'.$this->bobMessage->id))->assertRedirect();
        $this->assertNotNull($this->bobMessage->fresh()->read_at);

        $this->patch($this->bobUrl('/messages/'.$this->bobMessage->id))->assertRedirect();
        $this->assertNull($this->bobMessage->fresh()->read_at);

        $this->delete($this->bobUrl('/messages/'.$this->bobMessage->id))->assertRedirect();
        $this->assertModelMissing($this->bobMessage);
    }

    public function test_user_cannot_delete_another_users_media(): void
    {
        $media = Media::query()->create([
            'user_id' => $this->bob->id, 'collection' => 'images', 'disk' => 'public',
            'path' => 'media/x.png', 'filename' => 'x.png', 'mime_type' => 'image/png', 'size' => 10,
        ]);

        $this->actingAs($this->alice)->delete('/dashboard/media/'.$media->id)->assertForbidden();

        $this->assertModelExists($media);
    }

    public function test_media_library_json_only_lists_own_files(): void
    {
        Media::query()->create([
            'user_id' => $this->bob->id, 'collection' => 'images', 'disk' => 'public',
            'path' => 'media/bob.png', 'filename' => 'bob.png', 'mime_type' => 'image/png', 'size' => 10,
        ]);

        $this->actingAs($this->alice)
            ->getJson('/dashboard/media')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_admin_can_manage_any_website(): void
    {
        $this->actingAs($this->admin())
            ->put($this->bobUrl('/edit/seo'), ['seo_title' => 'Admin SEO'])
            ->assertRedirect();

        $this->assertSame('Admin SEO', $this->bobCompany->fresh()->seo_title);
    }
}
