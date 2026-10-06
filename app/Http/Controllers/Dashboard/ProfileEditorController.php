<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Template;
use App\Services\CompanyProfileService;
use App\Services\TemplateService;
use App\Support\ProfileTabs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Editor tabs of a company profile: info, about, contact, branding, seo.
 */
class ProfileEditorController extends Controller
{
    public function __construct(private readonly CompanyProfileService $companies) {}

    public function edit(CompanyProfile $company, string $tab): View
    {
        $this->authorize('update', $company);
        abort_unless(ProfileTabs::exists($tab), 404);

        return view('dashboard.websites.edit', [
            'company' => $company->load('template'),
            'tab' => $tab,
            'tabs' => ProfileTabs::TABS,
        ]);
    }

    public function update(Request $request, CompanyProfile $company, string $tab): RedirectResponse
    {
        $this->authorize('update', $company);
        abort_unless(ProfileTabs::exists($tab), 404);

        if ($tab === 'branding' && $request->boolean('reset_branding')) {
            $company->update(['branding' => null]);

            return back()->with('success', 'Branding dikembalikan ke default template.');
        }

        $validated = $request->validate(ProfileTabs::rules($tab, $company));
        $this->companies->updateTab($company, $tab, $validated, $request->user());

        return back()->with('success', ProfileTabs::TABS[$tab]['label'].' berhasil disimpan.');
    }

    public function template(CompanyProfile $company, TemplateService $templates): View
    {
        $this->authorize('update', $company);

        return view('dashboard.websites.template', [
            'company' => $company->load('template'),
            'templates' => $templates->published(),
        ]);
    }

    public function updateTemplate(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $data = $request->validate([
            'template' => ['required', Rule::exists('templates', 'slug')->where('status', Template::STATUS_PUBLISHED)],
            'reset_layout' => ['nullable', 'boolean'],
        ]);

        $template = Template::query()->where('slug', $data['template'])->firstOrFail();
        $this->companies->changeTemplate($company, $template, (bool) ($data['reset_layout'] ?? false));

        if ($request->filled('wizard')) {
            return redirect()->route('websites.wizard', [$company, 'company']);
        }

        return back()->with('success', "Template diganti ke {$template->name}.");
    }
}
