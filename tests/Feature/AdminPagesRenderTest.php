<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test: every admin panel page renders for an administrator.
 */
class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $user = User::query()->where('email', 'user@example.com')->firstOrFail();
        $company = CompanyProfile::query()->firstOrFail();
        $template = Template::query()->firstOrFail();

        $urls = [
            route('admin.dashboard'),
            route('admin.users.index'),
            route('admin.users.index', ['q' => 'a', 'role' => 'user', 'status' => 'active']),
            route('admin.users.create'),
            route('admin.users.show', $user),
            route('admin.users.show', $admin),
            route('admin.users.edit', $user),
            route('admin.companies.index'),
            route('admin.companies.index', ['status' => 'published', 'template' => $template->id]),
            route('admin.companies.show', $company),
            route('admin.templates.index'),
            route('admin.templates.create'),
            route('admin.templates.edit', $template),
            route('admin.categories.index'),
            route('admin.pages.index'),
            route('admin.domains.index'),
            route('admin.domains.index', ['status' => 'active']),
            route('admin.subscriptions.index'),
            route('admin.subscriptions.index', ['plan' => 'pro']),
            route('admin.media.index'),
            route('admin.settings.edit'),
            route('admin.logs.index'),
            route('admin.logs.index', ['action' => 'admin']),
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($admin)->get($url);

            $this->assertSame(200, $response->getStatusCode(), "GET {$url} returned {$response->getStatusCode()}: "
                .mb_substr((string) ($response->exception?->getMessage() ?? strip_tags((string) $response->getContent())), 0, 600));
        }
    }

    public function test_regular_users_cannot_access_admin_panel(): void
    {
        $user = User::query()->where('email', 'user@example.com')->firstOrFail();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }
}
