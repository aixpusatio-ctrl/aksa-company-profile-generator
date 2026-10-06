<?php

namespace App\Services;

use App\Events\CompanyProfilePublished;
use App\Models\CompanyProfile;
use App\Models\Template;
use App\Models\User;
use App\Support\Activity;
use App\Support\HtmlSanitizer;
use App\Support\ProfileTabs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CompanyProfileService
{
    public function __construct(
        private readonly MenuService $menus,
        private readonly DomainService $domains,
        private readonly MediaService $media,
    ) {}

    /**
     * Apply a validated editor tab (info, about, contact, branding, seo),
     * including image uploads / media library picks.
     */
    public function updateTab(CompanyProfile $company, string $tab, array $validated, User $user): CompanyProfile
    {
        $data = collect($validated)
            ->reject(fn ($value, $key) => str_ends_with($key, '_media') || str_ends_with($key, '_remove'))
            ->all();

        foreach (ProfileTabs::IMAGES[$tab] ?? [] as $field => $collection) {
            $data[$field] = $this->media->resolveImageInput($validated, $field, $user, $company, $company->{$field}, $collection);
        }

        if ($tab === 'info' && isset($data['social_links'])) {
            $data['social_links'] = collect($data['social_links'])
                ->only(array_keys(CompanyProfile::SOCIAL_NETWORKS))
                ->filter()
                ->all();
        }

        if ($tab === 'about' && array_key_exists('highlights', $data)) {
            $data['highlights'] = collect($data['highlights'] ?? [])
                ->map(fn ($row) => ['value' => trim((string) ($row['value'] ?? '')), 'label' => trim((string) ($row['label'] ?? ''))])
                ->filter(fn ($row) => $row['value'] !== '' && $row['label'] !== '')
                ->values()
                ->all() ?: null;
        }

        if ($tab === 'branding') {
            $data['branding'] = array_merge($company->branding ?? [], array_filter($data['branding'] ?? [], fn ($v) => $v !== null));
        }

        return $this->update($company, $data);
    }

    /**
     * Create a draft company profile with default sections and navigation.
     */
    public function create(User $user, array $data, ?Template $template = null): CompanyProfile
    {
        return DB::transaction(function () use ($user, $data, $template) {
            $company = $user->companyProfiles()->create([
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['slug'] ?? $data['name']),
                'template_id' => $template?->getKey(),
                'tagline' => $data['tagline'] ?? null,
                'email' => $data['email'] ?? $user->email,
                'country' => $data['country'] ?? 'Indonesia',
                'status' => CompanyProfile::STATUS_DRAFT,
                'wizard_step' => 2,
            ]);

            $this->syncSections($company, $template);
            $this->menus->createDefaults($company);

            Activity::log('website.created', "Website {$company->name} dibuat", $company);

            return $company->fresh(['template']);
        });
    }

    public function update(CompanyProfile $company, array $data): CompanyProfile
    {
        foreach (['about', 'mission', 'history', 'company_values'] as $richField) {
            if (array_key_exists($richField, $data)) {
                $data[$richField] = HtmlSanitizer::clean($data[$richField]);
            }
        }

        if (array_key_exists('slug', $data)) {
            $slug = Str::slug((string) $data['slug']);
            if ($slug !== $company->slug) {
                $this->assertSlugAvailable($slug, $company->getKey());
                $this->domains->forgetHost($company->subdomainHost());
            }
            $data['slug'] = $slug;
        }

        $company->update($data);

        return $company;
    }

    public function changeTemplate(CompanyProfile $company, Template $template, bool $resetLayout = false): void
    {
        $company->update(['template_id' => $template->getKey()]);

        if ($resetLayout) {
            $company->update(['branding' => null]);
            $this->syncSections($company, $template, reorder: true);
        }

        Activity::log('website.template_changed', "Template {$company->name} diganti ke {$template->name}", $company);
    }

    /**
     * Make sure every known section exists for the company (in the template's default order).
     */
    public function syncSections(CompanyProfile $company, ?Template $template = null, bool $reorder = false): void
    {
        $all = array_keys(config('website-templates.sections'));
        $order = $template?->resolvedSettings()['sections'] ?? $all;
        $order = array_values(array_unique(array_merge(array_intersect($order, $all), $all)));

        foreach ($order as $i => $key) {
            $section = $company->sections()->firstOrNew(['key' => $key]);
            if (! $section->exists || $reorder) {
                $section->sort_order = $i + 1;
            }
            if (! $section->exists) {
                $section->is_enabled = true;
            }
            $section->save();
        }
    }

    /**
     * @param  array<int, array{key: string, is_enabled: bool}>  $sections  in the desired order
     */
    public function saveSections(CompanyProfile $company, array $sections): void
    {
        $existing = $company->sections()->get()->keyBy('key');

        DB::transaction(function () use ($sections, $existing) {
            foreach (array_values($sections) as $i => $item) {
                if ($section = $existing->get($item['key'] ?? '')) {
                    $section->update([
                        'sort_order' => $i + 1,
                        'is_enabled' => (bool) ($item['is_enabled'] ?? false),
                        'title' => isset($item['title']) ? Str::limit(strip_tags((string) $item['title']), 150, '') ?: null : $section->title,
                        'subtitle' => isset($item['subtitle']) ? Str::limit(strip_tags((string) $item['subtitle']), 400, '') ?: null : $section->subtitle,
                    ]);
                }
            }
        });
    }

    public function publish(CompanyProfile $company): void
    {
        if (blank($company->name) || ! $company->template_id) {
            throw ValidationException::withMessages(['publish' => 'Lengkapi nama perusahaan dan pilih template sebelum publish.']);
        }

        $company->update([
            'status' => CompanyProfile::STATUS_PUBLISHED,
            'published_at' => $company->published_at ?? now(),
            'wizard_step' => 11,
        ]);

        CompanyProfilePublished::dispatch($company);
    }

    public function unpublish(CompanyProfile $company): void
    {
        $company->update(['status' => CompanyProfile::STATUS_DRAFT]);
        Activity::log('website.unpublished', "Website {$company->name} di-unpublish", $company);
    }

    public function delete(CompanyProfile $company): void
    {
        foreach ($company->domains as $domain) {
            $this->domains->forgetHost($domain->domain);
        }
        $this->domains->forgetHost($company->subdomainHost());

        Activity::log('website.deleted', "Website {$company->name} dihapus", null, ['slug' => $company->slug]);
        $company->delete();
    }

    // ------------------------------------------------------------ Slugs

    public static function isValidSlug(string $slug): bool
    {
        return (bool) preg_match('/^[a-z0-9](?:[a-z0-9-]{1,61}[a-z0-9])$/', $slug)
            && ! in_array($slug, config('platform.reserved_subdomains'), true);
    }

    public function assertSlugAvailable(string $slug, ?int $ignoreId = null): void
    {
        if (! self::isValidSlug($slug)) {
            throw ValidationException::withMessages(['slug' => 'Subdomain hanya boleh huruf kecil, angka dan tanda hubung (3-63 karakter) dan bukan nama yang dicadangkan.']);
        }

        if (CompanyProfile::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            throw ValidationException::withMessages(['slug' => 'Subdomain sudah digunakan.']);
        }
    }

    public function uniqueSlug(string $value): string
    {
        $base = Str::limit(Str::slug($value), 50, '') ?: 'company';
        $base = trim($base, '-');
        if (strlen($base) < 3 || in_array($base, config('platform.reserved_subdomains'), true)) {
            $base = $base.'-site';
        }

        $slug = $base;
        $i = 2;
        while (CompanyProfile::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
