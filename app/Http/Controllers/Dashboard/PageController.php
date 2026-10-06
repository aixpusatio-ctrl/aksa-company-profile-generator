<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyPage;
use App\Models\CompanyProfile;
use App\Services\MediaService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Custom pages (About Us, Career, Privacy Policy, ...).
 */
class PageController extends Controller
{
    /** Slugs that collide with system routes of tenant websites. */
    private const RESERVED = ['contact', 'sitemap', 'robots', 'up', 'api', 'storage', 'build'];

    public function __construct(private readonly MediaService $media) {}

    public function index(CompanyProfile $company): View
    {
        $this->authorize('update', $company);

        return view('dashboard.pages.index', [
            'company' => $company,
            'pages' => $company->pages()->get(),
        ]);
    }

    public function create(CompanyProfile $company): View
    {
        $this->authorize('update', $company);

        return view('dashboard.pages.form', ['company' => $company, 'page' => new CompanyPage(['status' => 'draft'])]);
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $page = $company->pages()->create($this->validated($request, $company));

        if ($request->boolean('add_to_menu')) {
            $company->menus()->create([
                'title' => $page->title, 'slug' => $page->slug, 'type' => 'page', 'company_page_id' => $page->id,
                'sort_order' => (int) $company->menus()->whereNull('parent_id')->max('sort_order') + 1,
            ]);
        }

        return redirect()->route('websites.pages.edit', [$company, $page])->with('success', 'Halaman dibuat.');
    }

    public function edit(CompanyProfile $company, CompanyPage $page): View
    {
        $this->authorize('update', $company);

        return view('dashboard.pages.form', compact('company', 'page'));
    }

    public function update(Request $request, CompanyProfile $company, CompanyPage $page): RedirectResponse
    {
        $this->authorize('update', $company);

        $page->update($this->validated($request, $company, $page));

        return back()->with('success', 'Halaman disimpan.');
    }

    public function destroy(CompanyProfile $company, CompanyPage $page): RedirectResponse
    {
        $this->authorize('update', $company);
        $page->delete();

        return redirect()->route('websites.pages.index', $company)->with('success', 'Halaman dihapus.');
    }

    private function validated(Request $request, CompanyProfile $company, ?CompanyPage $page = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title'))]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => [
                'required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::notIn(self::RESERVED),
                Rule::unique('company_pages', 'slug')->where('company_profile_id', $company->id)->ignore($page?->id),
            ],
            'content' => ['nullable', 'string', 'max:100000'],
            'seo_title' => ['nullable', 'string', 'max:120'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'featured_image' => ['nullable', MediaService::imageRule()],
            'featured_image_media' => ['nullable', 'string', 'max:255'],
            'featured_image_remove' => ['nullable', 'boolean'],
        ], ['slug.not_in' => 'Slug ini dipakai sistem, gunakan slug lain.']);

        $validated['content'] = HtmlSanitizer::clean($validated['content'] ?? null);
        $validated['featured_image'] = $this->media->resolveImageInput($validated, 'featured_image', $request->user(), $company, $page?->featured_image);

        return collect($validated)->except(['featured_image_media', 'featured_image_remove'])->all();
    }
}
