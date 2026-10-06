<?php

namespace App\Services;

use App\Models\CompanyGalleryItem;
use App\Models\CompanyPage;
use App\Models\CompanyProduct;
use App\Models\CompanyProfile;
use App\Models\CompanyProject;
use App\Models\CompanySection;
use App\Models\CompanyService;
use App\Models\CompanyTeamMember;
use App\Models\CompanyTestimonial;
use App\Models\Menu;
use App\Models\Template;
use App\Support\Activity;
use App\Support\DemoContent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TemplateService
{
    /**
     * Layout themes available on disk (resources/views/websites/templates/*).
     */
    public function layouts(): array
    {
        return config('website-templates.layouts');
    }

    public function layoutExists(string $layout): bool
    {
        return array_key_exists($layout, $this->layouts());
    }

    public function published(?string $category = null, ?string $search = null): Collection
    {
        return Template::query()
            ->with('category')
            ->published()
            ->when($category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $category)))
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$search.'%')
                ->orWhere('style', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')))
            ->ordered()
            ->get();
    }

    public function defaultTemplate(): ?Template
    {
        $slug = setting('default_template', 'corporate');

        return Template::query()->published()->where('slug', $slug)->first()
            ?? Template::query()->published()->ordered()->first();
    }

    public function create(array $data): Template
    {
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);
        $template = Template::query()->create($data);
        Activity::log('template.created', "Template {$template->name} dibuat", $template);

        return $template;
    }

    public function duplicate(Template $template): Template
    {
        $copy = $template->replicate(['slug']);
        $copy->name = $template->name.' (Copy)';
        $copy->slug = $this->uniqueSlug($template->slug.'-copy');
        $copy->status = Template::STATUS_DRAFT;
        $copy->is_featured = false;
        $copy->save();

        Activity::log('template.duplicated', "Template {$template->name} diduplikasi", $copy);

        return $copy;
    }

    public function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'template';
        $slug = $base;
        $i = 2;

        while (Template::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /**
     * Build an unsaved company profile filled with demo content, used to
     * preview a template before a user picks it.
     */
    public function sampleCompany(Template $template): CompanyProfile
    {
        $demo = DemoContent::for($template->demoKey());

        $company = new CompanyProfile($demo['company']);
        $company->id = 0;
        $company->slug = 'preview';
        $company->status = CompanyProfile::STATUS_PUBLISHED;
        $company->setRelation('template', $template);
        $company->setRelation('primaryDomain', null);

        $all = array_keys(config('website-templates.sections'));
        $order = array_values(array_unique(array_merge(array_intersect($template->resolvedSettings()['sections'] ?? [], $all), $all)));
        $company->setRelation('sections', new Collection(collect($order)->values()->map(
            fn ($key, $i) => new CompanySection(['key' => $key, 'is_enabled' => true, 'sort_order' => $i])
        )->all()));

        $relations = [
            'services' => CompanyService::class,
            'products' => CompanyProduct::class,
            'projects' => CompanyProject::class,
            'team' => CompanyTeamMember::class,
            'testimonials' => CompanyTestimonial::class,
            'gallery' => CompanyGalleryItem::class,
            'pages' => CompanyPage::class,
        ];

        foreach ($relations as $relation => $class) {
            $company->setRelation($relation, new Collection(collect($demo[$relation] ?? [])->values()->map(function ($attributes, $i) use ($class) {
                $model = new $class($attributes);
                $model->id = $i + 1;

                return $model;
            })->all()));
        }

        // Flat list with parent_id, exactly like menus stored in the database.
        $menus = [];
        $id = 0;
        foreach (DemoContent::menus() as $item) {
            $parent = new Menu(collect($item)->except('children')->all());
            $parent->id = ++$id;
            $menus[] = $parent;

            foreach ($item['children'] ?? [] as $child) {
                $menu = new Menu($child + ['parent_id' => $parent->id]);
                $menu->id = ++$id;
                $menus[] = $menu;
            }
        }
        $company->setRelation('menus', new Collection($menus));

        return $company;
    }
}
