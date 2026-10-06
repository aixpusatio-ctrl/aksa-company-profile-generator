<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Template;
use App\Services\AnalyticsService;
use App\Services\CompanyProfileService;
use App\Services\TemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function __construct(private readonly CompanyProfileService $companies) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();

        $websites = $request->user()->companyProfiles()
            ->with(['template', 'primaryDomain'])
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")))
            ->when(in_array($status, ['draft', 'published'], true), fn ($q) => $q->where('status', $status))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('dashboard.websites.index', compact('websites', 'search', 'status'));
    }

    /**
     * Step 1 of the wizard: choose a template and name the company.
     */
    public function create(Request $request, TemplateService $templates): View
    {
        $this->authorize('create', CompanyProfile::class);

        return view('dashboard.websites.create', [
            'templates' => $templates->published(),
            'selected' => $request->string('template')->toString() ?: $templates->defaultTemplate()?->slug,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CompanyProfile::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'template' => ['required', Rule::exists('templates', 'slug')->where('status', Template::STATUS_PUBLISHED)],
        ]);

        $template = Template::query()->where('slug', $data['template'])->firstOrFail();
        $company = $this->companies->create($request->user(), $data, $template);

        return redirect()->route('websites.wizard', [$company, 'company'])
            ->with('success', 'Website dibuat! Lanjutkan dengan mengisi informasi perusahaan.');
    }

    /**
     * Manage overview of a website.
     */
    public function show(CompanyProfile $company, AnalyticsService $analytics): View
    {
        $this->authorize('view', $company);

        $company->load(['template', 'primaryDomain', 'domains', 'sections'])
            ->loadCount(['services', 'products', 'projects', 'team', 'testimonials', 'gallery', 'pages', 'menus', 'contactMessages']);

        return view('dashboard.websites.show', [
            'company' => $company,
            'analytics' => $analytics->stats($company, 14),
            'checklist' => $this->checklist($company),
            'unreadMessages' => $company->contactMessages()->whereNull('read_at')->count(),
        ]);
    }

    public function publish(CompanyProfile $company): RedirectResponse
    {
        $this->authorize('publish', $company);
        $this->companies->publish($company);

        return back()->with('success', 'Website berhasil dipublikasikan di '.$company->primaryHost());
    }

    public function unpublish(CompanyProfile $company): RedirectResponse
    {
        $this->authorize('publish', $company);
        $this->companies->unpublish($company);

        return back()->with('success', 'Website dikembalikan ke draft.');
    }

    public function destroy(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        $request->validate(['confirm_name' => ['required', Rule::in([$company->name])]], [
            'confirm_name.in' => 'Ketik nama website dengan benar untuk konfirmasi.',
        ]);

        $this->companies->delete($company);

        return redirect()->route('websites.index')->with('success', 'Website dihapus.');
    }

    private function checklist(CompanyProfile $company): array
    {
        return [
            ['label' => 'Pilih template', 'done' => (bool) $company->template_id, 'url' => route('websites.template.edit', $company)],
            ['label' => 'Informasi perusahaan', 'done' => filled($company->description) && filled($company->phone ?? $company->email), 'url' => route('websites.edit', [$company, 'info'])],
            ['label' => 'Logo perusahaan', 'done' => filled($company->logo), 'url' => route('websites.edit', [$company, 'branding'])],
            ['label' => 'Tentang perusahaan', 'done' => filled($company->about), 'url' => route('websites.edit', [$company, 'about'])],
            ['label' => 'Tambah layanan', 'done' => $company->services_count > 0, 'url' => route('websites.content.index', [$company, 'services'])],
            ['label' => 'Portofolio proyek', 'done' => $company->projects_count > 0, 'url' => route('websites.content.index', [$company, 'projects'])],
            ['label' => 'Optimasi SEO', 'done' => filled($company->seo_title) && filled($company->seo_description), 'url' => route('websites.edit', [$company, 'seo'])],
            ['label' => 'Publish website', 'done' => $company->isPublished(), 'url' => route('websites.wizard', [$company, 'publish'])],
        ];
    }
}
