<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Services\CompanyProfileService;
use App\Services\WebsiteRendererService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Section builder: enable / disable / reorder home page sections.
 */
class SectionController extends Controller
{
    public function __construct(private readonly CompanyProfileService $companies) {}

    public function index(CompanyProfile $company, WebsiteRendererService $renderer): View
    {
        $this->authorize('update', $company);
        $this->companies->syncSections($company, $company->template);

        $company->load(['sections', 'services', 'products', 'projects', 'team', 'testimonials', 'gallery']);

        return view('dashboard.sections.index', [
            'company' => $company,
            'sections' => $company->sections->map(fn ($section) => [
                'key' => $section->key,
                'label' => $section->label(),
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'is_enabled' => $section->is_enabled,
                'has_content' => $renderer->hasContent($company, $section->key),
            ])->values(),
        ]);
    }

    public function update(Request $request, CompanyProfile $company): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $company);

        $data = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.key' => ['required', 'string', 'max:40'],
            'sections.*.is_enabled' => ['required', 'boolean'],
            'sections.*.title' => ['nullable', 'string', 'max:150'],
            'sections.*.subtitle' => ['nullable', 'string', 'max:400'],
        ]);

        $this->companies->saveSections($company, $data['sections']);

        return $request->expectsJson()
            ? response()->json(['saved' => true])
            : back()->with('success', 'Susunan section disimpan.');
    }
}
