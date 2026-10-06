<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Template;
use App\Services\CompanyProfileService;
use App\Services\TemplateService;
use App\Support\ContentTypes;
use App\Support\ProfileTabs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Step-by-step company profile creation wizard.
 */
class WizardController extends Controller
{
    /** step key => [label, kind, target] */
    public const STEPS = [
        'template' => ['Choose Template', 'template', null],
        'company' => ['Company Information', 'tab', 'info'],
        'about' => ['About Company', 'tab', 'about'],
        'services' => ['Services', 'content', 'services'],
        'products' => ['Products', 'content', 'products'],
        'team' => ['Team', 'content', 'team'],
        'projects' => ['Projects / Portfolio', 'content', 'projects'],
        'contact' => ['Contact', 'tab', 'contact'],
        'seo' => ['SEO', 'tab', 'seo'],
        'preview' => ['Preview', 'preview', null],
        'publish' => ['Publish', 'publish', null],
    ];

    public function __construct(private readonly CompanyProfileService $companies) {}

    public function show(CompanyProfile $company, TemplateService $templates, ?string $step = null): View
    {
        $this->authorize('update', $company);

        $keys = array_keys(self::STEPS);
        $step ??= $keys[min(max($company->wizard_step, 1), count($keys)) - 1];
        abort_unless(isset(self::STEPS[$step]), 404);

        [$label, $kind, $target] = self::STEPS[$step];
        $index = array_search($step, $keys, true);

        $data = [
            'company' => $company->load('template'),
            'step' => $step,
            'steps' => self::STEPS,
            'index' => $index,
            'label' => $label,
            'kind' => $kind,
            'target' => $target,
            'previous' => $keys[$index - 1] ?? null,
            'next' => $keys[$index + 1] ?? null,
        ];

        if ($kind === 'template') {
            $data['templates'] = $templates->published();
        }

        if ($kind === 'content') {
            $data['type'] = ContentTypes::get($target);
            $data['items'] = $company->{$data['type']['relation']}()->get();
        }

        return view('dashboard.wizard.show', $data);
    }

    public function save(Request $request, CompanyProfile $company, string $step): RedirectResponse
    {
        $this->authorize('update', $company);
        abort_unless(isset(self::STEPS[$step]), 404);

        [, $kind, $target] = self::STEPS[$step];
        $keys = array_keys(self::STEPS);
        $index = array_search($step, $keys, true);

        switch ($kind) {
            case 'template':
                $data = $request->validate([
                    'template' => ['required', Rule::exists('templates', 'slug')->where('status', Template::STATUS_PUBLISHED)],
                ]);
                $template = Template::query()->where('slug', $data['template'])->firstOrFail();
                if ($company->template_id !== $template->id) {
                    $this->companies->changeTemplate($company, $template, resetLayout: true);
                }
                break;

            case 'tab':
                $validated = $request->validate(ProfileTabs::rules($target, $company));
                $this->companies->updateTab($company, $target, $validated, $request->user());
                break;

            case 'publish':
                $this->authorize('publish', $company);
                $this->companies->publish($company);

                return redirect()->route('websites.show', $company)
                    ->with('success', '🎉 Website Anda sudah live di '.$company->primaryHost())
                    ->with('published', true);
        }

        $company->update(['wizard_step' => max($company->wizard_step, $index + 2)]);

        $next = $keys[$index + 1] ?? null;

        return $request->input('action') === 'back' && $index > 0
            ? redirect()->route('websites.wizard', [$company, $keys[$index - 1]])
            : redirect()->route('websites.wizard', [$company, $next ?? 'publish'])->with('success', 'Tersimpan.');
    }

    /**
     * Autosave text fields of a wizard/editor tab (JSON, called by Alpine).
     */
    public function autosave(Request $request, CompanyProfile $company): JsonResponse
    {
        $this->authorize('update', $company);

        $tab = $request->string('_tab')->toString();
        abort_unless(ProfileTabs::exists($tab), 422);

        $validated = $request->validate(ProfileTabs::autosaveRules($tab, $company));

        if ($validated !== []) {
            $this->companies->updateTab($company, $tab, $validated, $request->user());
        }

        return response()->json(['saved' => true, 'at' => now()->format('H:i:s')]);
    }
}
