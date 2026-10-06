<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Template;
use App\Models\User;
use Database\Seeders\TemplateSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_demo_accounts_are_seeded(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $user = User::query()->where('email', 'user@example.com')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertSame(User::ROLE_USER, $user->role);
        $this->assertSame('pro', $user->planKey());
    }

    public function test_seeded_demo_accounts_can_log_in(): void
    {
        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(User::query()->where('email', 'admin@example.com')->first());

        $this->post('/logout');

        $this->post('/login', ['email' => 'user@example.com', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs(User::query()->where('email', 'user@example.com')->first());
    }

    public function test_templates_are_seeded(): void
    {
        $this->assertGreaterThanOrEqual(10, Template::query()->published()->count());

        $layouts = array_keys(config('website-templates.layouts'));
        foreach (Template::query()->get() as $template) {
            $this->assertContains($template->layout, $layouts, "Template {$template->slug} uses an unknown layout.");
        }
        $this->assertEqualsCanonicalizing($layouts, Template::query()->distinct()->pluck('layout')->all(), 'Every layout has a seeded template.');
    }

    public function test_demo_company_is_seeded_with_content(): void
    {
        $company = CompanyProfile::query()->where('slug', 'example')->firstOrFail();

        $this->assertSame('user@example.com', $company->user->email);
        $this->assertTrue($company->isPublished());
        $this->assertNotNull($company->template_id);
        $this->assertGreaterThan(0, $company->services()->count());
        $this->assertGreaterThan(0, $company->products()->count());
        $this->assertGreaterThan(0, $company->projects()->count());
        $this->assertGreaterThan(0, $company->team()->count());
        $this->assertGreaterThan(0, $company->pages()->count());
        $this->assertGreaterThan(0, $company->menus()->count());
        $this->assertGreaterThan(0, $company->menus()->whereNotNull('parent_id')->count(), 'Demo menu contains sub menus.');
        $this->assertSame(count(config('website-templates.sections')), $company->sections()->count());
    }

    public function test_seeded_demo_websites_render(): void
    {
        $this->get('http://example.localhost/')->assertOk()->assertSee(e(CompanyProfile::query()->where('slug', 'example')->value('name')), false);
        $this->get('http://www.example-indonesia.test/')->assertOk();
        $this->get('http://example-indonesia.test/')->assertOk();

        $page = CompanyProfile::query()->where('slug', 'example')->firstOrFail()->pages()->published()->firstOrFail();
        $this->get('http://example.localhost/'.$page->slug)->assertOk();

        foreach (CompanyProfile::query()->published()->get() as $company) {
            $this->get($this->tenantUrl($company))->assertOk();
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        $users = User::query()->count();
        $templates = Template::query()->count();

        $this->seed(UserSeeder::class);
        $this->seed(TemplateSeeder::class);

        $this->assertSame($users, User::query()->count());
        $this->assertSame($templates, Template::query()->count());
    }
}
