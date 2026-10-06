<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\Domain;
use App\Models\Menu;
use App\Models\PageView;
use App\Models\Template;
use App\Models\User;
use App\Services\CompanyProfileService;
use App\Support\DemoContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo websites: a complete published company profile for user@example.com
 * (+ a draft) and a few showcase websites for the landing page.
 */
class DemoCompanySeeder extends Seeder
{
    public function __construct(private readonly CompanyProfileService $companies) {}

    public function run(): void
    {
        $user = User::query()->where('email', 'user@example.com')->firstOrFail();

        $main = $this->createCompany($user, 'corporate-blue', 'corporate', [
            'name' => 'PT Example Indonesia',
            'slug' => 'example',
            'tagline' => 'Solusi Bisnis Terintegrasi untuk Indonesia',
        ], published: true);

        $main->domains()->create([
            'domain' => 'www.example-indonesia.test',
            'type' => Domain::TYPE_SUBDOMAIN,
            'verification_token' => 'cpg-'.Str::lower(Str::random(32)),
            'status' => Domain::STATUS_ACTIVE,
            'is_primary' => false,
            'verified_at' => now()->subDays(10),
            'last_checked_at' => now()->subDays(10),
        ]);
        $main->domains()->create([
            'domain' => 'example-indonesia.test',
            'type' => Domain::TYPE_APEX,
            'verification_token' => 'cpg-'.Str::lower(Str::random(32)),
            'status' => Domain::STATUS_PENDING,
        ]);

        $main->contactMessages()->createMany([
            ['name' => 'Rina Wijaya', 'email' => 'rina@contoh.co.id', 'phone' => '0812-1111-2222', 'subject' => 'Permintaan penawaran', 'message' => 'Halo, kami tertarik dengan layanan logistik Anda untuk distribusi ke Jawa Timur. Mohon kirimkan penawaran.'],
            ['name' => 'Andi Pratama', 'email' => 'andi@contoh.id', 'subject' => 'Kerja sama', 'message' => 'Kami ingin menjajaki kerja sama strategis. Kapan kita bisa bertemu?', 'read_at' => now()],
        ]);

        // A little analytics history.
        foreach (range(0, 13) as $daysAgo) {
            foreach (range(1, random_int(8, 40)) as $n) {
                PageView::query()->create([
                    'company_profile_id' => $main->id,
                    'path' => collect(['/', '/', '/', '/karir', '/kebijakan-privasi'])->random(),
                    'visitor_hash' => hash('sha256', $daysAgo.'-'.random_int(1, 25)),
                    'viewed_on' => now()->subDays($daysAgo)->toDateString(),
                ]);
            }
        }

        $this->createCompany($user, 'technology', 'technology', [], published: false, wizardStep: 4);

        // Showcase websites (Example Websites on the landing page).
        foreach ([
            ['construction', 'construction', 'Siti Rahma', 'siti@example.com'],
            ['creative-agency', 'creative-agency', 'Dimas Arya', 'dimas@example.com'],
            ['executive', 'executive', 'Hendra Gunawan', 'hendra@example.com'],
        ] as [$template, $layout, $name, $email]) {
            $owner = User::query()->updateOrCreate(['email' => $email], [
                'name' => $name, 'password' => 'password', 'role' => User::ROLE_USER, 'email_verified_at' => now(),
            ]);
            $owner->subscriptions()->firstOrCreate(['plan' => 'pro'], ['status' => 'active', 'price' => config('platform.plans.pro.price'), 'starts_at' => now()]);
            $this->createCompany($owner, $template, $layout, [], published: true);
        }
    }

    private function createCompany(User $user, string $templateSlug, string $layout, array $overrides, bool $published, int $wizardStep = 11): CompanyProfile
    {
        $template = Template::query()->where('slug', $templateSlug)->firstOrFail();
        $demo = DemoContent::forLayout($layout);
        $attributes = array_merge($demo['company'], $overrides);

        $company = $this->companies->create($user, ['name' => $attributes['name'], 'slug' => $attributes['slug'] ?? null], $template);
        $company->update(collect($attributes)->except(['slug'])->all() + [
            'wizard_step' => $wizardStep,
            'og_title' => $attributes['seo_title'] ?? null,
            'og_description' => $attributes['seo_description'] ?? null,
        ]);

        foreach (['services', 'products', 'projects', 'team', 'testimonials', 'gallery'] as $relation) {
            foreach ($demo[$relation] ?? [] as $item) {
                $company->{$relation}()->create($item);
            }
        }

        $pages = collect($demo['pages'] ?? [])->map(fn ($page) => $company->pages()->create($page))->keyBy('slug');

        // Replace the default navigation with a richer menu + sub menus.
        $company->menus()->delete();
        foreach (DemoContent::menus() as $i => $item) {
            $parent = $company->menus()->create($this->menuAttributes($item, $pages, $i));
            foreach ($item['children'] ?? [] as $j => $child) {
                $company->menus()->create($this->menuAttributes($child, $pages, $j) + ['parent_id' => $parent->id]);
            }
        }

        if ($published) {
            $company->update(['status' => CompanyProfile::STATUS_PUBLISHED, 'published_at' => now()->subDays(random_int(3, 60))]);
        }

        return $company;
    }

    private function menuAttributes(array $item, $pages, int $index): array
    {
        $type = $item['type'] ?? Menu::TYPE_ANCHOR;
        $page = $type === Menu::TYPE_PAGE ? $pages->get($item['url']) : null;

        return [
            'title' => $item['title'],
            'slug' => Str::slug($item['title']),
            'type' => $type === Menu::TYPE_PAGE && ! $page ? Menu::TYPE_ANCHOR : $type,
            'url' => $type === Menu::TYPE_PAGE ? ($page ? null : 'hero') : ($item['url'] ?? null),
            'company_page_id' => $page?->id,
            'sort_order' => $index + 1,
            'status' => 'active',
        ];
    }
}
