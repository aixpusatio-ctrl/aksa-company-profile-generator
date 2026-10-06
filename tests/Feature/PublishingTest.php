<?php

namespace Tests\Feature;

use App\Events\CompanyProfilePublished;
use App\Models\ActivityLog;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Notifications\WebsitePublishedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PublishingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->userWithPlan();
        $this->company = $this->companyFor($this->user, ['name' => 'PT Siap Terbit', 'wizard_step' => 10]);
    }

    public function test_publish_sets_status_and_published_at(): void
    {
        $this->actingAs($this->user)
            ->from('/dashboard/websites/'.$this->company->id)
            ->post("/dashboard/websites/{$this->company->id}/publish")
            ->assertRedirect('/dashboard/websites/'.$this->company->id)
            ->assertSessionHas('success');

        $company = $this->company->fresh();
        $this->assertSame(CompanyProfile::STATUS_PUBLISHED, $company->status);
        $this->assertTrue($company->isPublished());
        $this->assertNotNull($company->published_at);
        $this->assertSame(11, $company->wizard_step);
    }

    public function test_publish_dispatches_event(): void
    {
        Event::fake([CompanyProfilePublished::class]);

        $this->actingAs($this->user)->post("/dashboard/websites/{$this->company->id}/publish");

        Event::assertDispatched(CompanyProfilePublished::class, fn (CompanyProfilePublished $e) => $e->company->is($this->company));
    }

    public function test_publish_stores_notification_and_activity_log(): void
    {
        $this->actingAs($this->user)->post("/dashboard/websites/{$this->company->id}/publish");

        $notification = $this->user->notifications()->firstOrFail();
        $this->assertSame(WebsitePublishedNotification::class, $notification->type);
        $this->assertStringContainsString('PT Siap Terbit', $notification->data['message']);
        $this->assertStringContainsString($this->company->subdomainHost(), $notification->data['message']);
        $this->assertSame(1, $this->user->unreadNotifications()->count());

        $log = ActivityLog::query()->where('action', 'website.published')->firstOrFail();
        $this->assertSame($this->user->id, $log->user_id);
        $this->assertSame($this->company->id, $log->subject_id);
        $this->assertSame($this->company->publicUrl(), $log->properties['url']);
    }

    public function test_notifications_can_be_marked_as_read(): void
    {
        $this->actingAs($this->user)->post("/dashboard/websites/{$this->company->id}/publish");
        $this->assertSame(1, $this->user->unreadNotifications()->count());

        $this->actingAs($this->user)->post('/dashboard/notifications/read')->assertRedirect();

        $this->assertSame(0, $this->user->fresh()->unreadNotifications()->count());
    }

    public function test_republishing_keeps_original_published_at(): void
    {
        $original = now()->subDays(10)->startOfSecond();
        $this->company->update(['status' => CompanyProfile::STATUS_DRAFT, 'published_at' => $original]);

        $this->actingAs($this->user)->post("/dashboard/websites/{$this->company->id}/publish");

        $this->assertTrue($original->equalTo($this->company->fresh()->published_at));
    }

    public function test_website_without_template_cannot_be_published(): void
    {
        $this->company->update(['template_id' => null]);

        $this->actingAs($this->user)->post("/dashboard/websites/{$this->company->id}/publish")
            ->assertSessionHasErrors('publish');

        $this->assertSame(CompanyProfile::STATUS_DRAFT, $this->company->fresh()->status);
    }

    public function test_unpublish_returns_site_to_draft(): void
    {
        $this->company->update(['status' => CompanyProfile::STATUS_PUBLISHED, 'published_at' => now()]);
        $this->get($this->tenantUrl($this->company))->assertOk();

        $this->actingAs($this->user)->post($this->centralUrl("/dashboard/websites/{$this->company->id}/unpublish"))->assertRedirect();

        $this->assertSame(CompanyProfile::STATUS_DRAFT, $this->company->fresh()->status);
        $this->assertTrue(ActivityLog::query()->where('action', 'website.unpublished')->exists());
        $this->get($this->tenantUrl($this->company))->assertNotFound();
    }

    public function test_draft_site_returns_404_publicly(): void
    {
        $this->get($this->tenantUrl($this->company))->assertNotFound();
        $this->get($this->tenantUrl($this->company, '/sitemap.xml'))->assertNotFound();
        $this->get($this->tenantUrl($this->company, '/robots.txt'))->assertNotFound();
        $this->post($this->tenantUrl($this->company, '/contact'), ['name' => 'A', 'email' => 'a@example.test', 'message' => 'Halo dunia'])
            ->assertNotFound();

        $this->assertSame(0, $this->company->contactMessages()->count());
    }

    public function test_draft_site_is_visible_in_owner_preview(): void
    {
        $this->actingAs($this->user)
            ->get("/dashboard/websites/{$this->company->id}/preview/frame")
            ->assertOk()
            ->assertSee('PT Siap Terbit');
    }

    public function test_suspended_owners_site_returns_404(): void
    {
        $this->company->update(['status' => CompanyProfile::STATUS_PUBLISHED, 'published_at' => now()]);
        $this->get($this->tenantUrl($this->company))->assertOk();

        $this->user->forceFill(['suspended_at' => now()])->save();

        $this->get($this->tenantUrl($this->company))->assertNotFound();
    }

    public function test_publish_via_wizard_publish_step(): void
    {
        $this->actingAs($this->user)
            ->post("/dashboard/websites/{$this->company->id}/wizard/publish")
            ->assertRedirect(route('websites.show', $this->company))
            ->assertSessionHas('published', true);

        $company = $this->company->fresh();
        $this->assertTrue($company->isPublished());
        $this->assertSame(11, $company->wizard_step);
        $this->get($this->tenantUrl($company))->assertOk()->assertSee('PT Siap Terbit');
    }
}
