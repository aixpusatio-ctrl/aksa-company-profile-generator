<?php

namespace Tests;

use App\Models\CompanyProfile;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        // Factories rely on en_US formatters (e.g. catchPhrase) that some
        // locales such as id_ID do not provide; keep tests locale independent.
        config(['app.faker_locale' => 'en_US']);
    }

    /**
     * A regular user on a running plan ("pro" by default, "free" = no subscription).
     */
    protected function userWithPlan(string $plan = 'pro', array $attributes = []): User
    {
        $user = User::factory()->create($attributes);

        if ($plan !== 'free') {
            $user->subscriptions()->create([
                'plan' => $plan,
                'status' => 'active',
                'price' => config("platform.plans.{$plan}.price", 0),
                'starts_at' => now()->subDay(),
            ]);
        }

        return $user;
    }

    protected function admin(array $attributes = []): User
    {
        return User::factory()->admin()->create($attributes);
    }

    protected function corporateTemplate(array $attributes = []): Template
    {
        return Template::factory()->layout('corporate')->create($attributes);
    }

    /**
     * Company with default sections & menus (like the real creation flow),
     * using the fully implemented "corporate" layout.
     */
    protected function companyFor(?User $user = null, array $attributes = [], bool $published = false): CompanyProfile
    {
        $factory = CompanyProfile::factory()->withDefaults();

        if ($published) {
            $factory = $factory->published();
        }

        return $factory->create(array_merge([
            'user_id' => ($user ?? $this->userWithPlan())->id,
            'template_id' => $this->corporateTemplate()->id,
        ], $attributes));
    }

    /**
     * Absolute URL on the central host. Needed after a tenant request because
     * relative test URLs are resolved against the previous request's host.
     */
    protected function centralUrl(string $path = '/'): string
    {
        return 'http://localhost'.$path;
    }

    /** Base URL of a tenant website on the platform sub domain (port-less, as in tests). */
    protected function tenantUrl(CompanyProfile $company, string $path = '/'): string
    {
        return 'http://'.$company->slug.'.'.config('platform.domain').$path;
    }
}
