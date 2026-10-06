<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Services\MediaService;
use App\Services\TemplateService;
use App\Support\Activity;
use App\Support\DemoContent;
use App\Support\Website\ComponentRegistry;
use App\Support\Website\DesignSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __construct(
        private readonly TemplateService $templates,
        private readonly MediaService $media,
    ) {}

    public function index(Request $request): View
    {
        $templates = Template::query()
            ->with('category')
            ->withCount(['companyProfiles', 'companyProfiles as users_count' => fn ($q) => $q->select(DB::raw('count(distinct user_id)'))])
            ->when($request->string('q')->toString(), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->integer('category'), fn ($q, $category) => $q->where('template_category_id', $category))
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        return view('admin.templates.index', [
            'templates' => $templates,
            'categories' => TemplateCategory::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.templates.form', $this->formData(new Template([
            'status' => Template::STATUS_DRAFT,
            'layout' => 'corporate',
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $template = $this->templates->create($this->validated($request));

        return redirect()->route('admin.templates.edit', $template)->with('success', 'Template dibuat.');
    }

    public function edit(Template $template): View
    {
        return view('admin.templates.form', $this->formData($template));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $data = $this->validated($request, $template);
        $data['slug'] = $this->templates->uniqueSlug($data['slug'] ?? $data['name'], $template->id);
        $template->update($data);
        Activity::log('template.updated', "Template {$template->name} diperbarui", $template);

        return redirect()->route('admin.templates.edit', $template)->with('success', 'Template disimpan.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        if ($template->companyProfiles()->exists()) {
            return back()->with('error', 'Template sedang dipakai website. Unpublish saja atau pindahkan website terlebih dahulu.');
        }

        Activity::log('template.deleted', "Template {$template->name} dihapus", null, ['slug' => $template->slug]);
        $template->delete();

        return redirect()->route('admin.templates.index')->with('success', 'Template dihapus.');
    }

    public function duplicate(Template $template): RedirectResponse
    {
        $copy = $this->templates->duplicate($template);

        return redirect()->route('admin.templates.edit', $copy)->with('success', 'Template diduplikasi sebagai draft.');
    }

    public function togglePublish(Template $template): RedirectResponse
    {
        $template->update(['status' => $template->isPublished() ? Template::STATUS_DRAFT : Template::STATUS_PUBLISHED]);
        Activity::log('template.status', "Template {$template->name} → {$template->status}", $template);

        return back()->with('success', $template->isPublished() ? 'Template dipublikasikan.' : 'Template di-unpublish.');
    }

    public function toggleFeatured(Template $template): RedirectResponse
    {
        $template->update(['is_featured' => ! $template->is_featured]);

        return back()->with('success', $template->is_featured ? 'Template ditandai featured.' : 'Featured dilepas.');
    }

    private function formData(Template $template): array
    {
        return [
            'template' => $template,
            'categories' => TemplateCategory::query()->orderBy('sort_order')->get(),
            'layouts' => $this->templates->layouts(),
            'settings' => $template->resolvedSettings(),
            'demoOptions' => collect(DemoContent::layouts())->merge(DemoContent::dataKeys())->unique()->sort()
                ->mapWithKeys(fn ($key) => [$key => ucwords(str_replace('-', ' ', $key))])->all(),
        ];
    }

    private function validated(Request $request, ?Template $template = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'layout' => ['required', Rule::in(array_keys($this->templates->layouts()))],
            'template_category_id' => ['nullable', 'exists:template_categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'style' => ['nullable', 'string', 'max:255'],
            'demo' => ['nullable', 'string', 'max:60', Rule::in(array_merge(DemoContent::layouts(), DemoContent::dataKeys()))],
            'mobile_thumbnail' => ['nullable', MediaService::imageRule()],
            'mobile_thumbnail_remove' => ['nullable', 'boolean'],
            'config' => ['nullable', 'array'],
            'config.components' => ['nullable', 'array'],
            'config.design' => ['nullable', 'array'],
            'preview_url' => ['nullable', 'url:http,https', 'max:255'],
            'status' => ['required', Rule::in([Template::STATUS_DRAFT, Template::STATUS_PUBLISHED])],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', MediaService::imageRule()],
            'thumbnail_remove' => ['nullable', 'boolean'],
            'settings.primary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'settings.secondary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'settings.heading_font' => ['nullable', Rule::in(config('website-templates.fonts'))],
            'settings.body_font' => ['nullable', Rule::in(config('website-templates.fonts'))],
            'settings.button_style' => ['nullable', Rule::in(array_keys(config('website-templates.button_styles')))],
            'settings.border_radius' => ['nullable', Rule::in(array_keys(config('website-templates.radii')))],
            'settings.sections' => ['nullable', 'array'],
            'settings.sections.*' => [Rule::in(array_keys(config('website-templates.sections')))],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['settings'] = array_filter($data['settings'] ?? [], fn ($v) => filled($v));
        $data['thumbnail'] = $this->media->resolveImageInput($data, 'thumbnail', $request->user(), null, $template?->thumbnail);
        $data['mobile_thumbnail'] = $this->media->resolveImageInput($data, 'mobile_thumbnail', $request->user(), null, $template?->mobile_thumbnail);
        unset($data['thumbnail_remove'], $data['mobile_thumbnail_remove']);

        // Keep only known component variants & design tokens.
        $components = collect($data['config']['components'] ?? [])
            ->filter(fn ($variant, $slot) => is_string($variant) && ComponentRegistry::exists($slot, $variant))->all();
        $design = collect($data['config']['design'] ?? [])
            ->filter(fn ($value, $token) => in_array($value, DesignSystem::OPTIONS[$token] ?? [], true))->all();
        $data['config'] = $data['layout'] === 'composer' ? ['components' => $components, 'design' => $design] : null;

        return $data;
    }
}
