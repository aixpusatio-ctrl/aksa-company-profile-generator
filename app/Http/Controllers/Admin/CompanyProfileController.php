<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Template;
use App\Services\CompanyProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    public function __construct(private readonly CompanyProfileService $companies) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();
        $template = $request->integer('template');

        $companies = CompanyProfile::query()
            ->with(['user', 'template', 'primaryDomain'])
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")))
            ->when(in_array($status, ['draft', 'published'], true), fn ($q) => $q->where('status', $status))
            ->when($template, fn ($q) => $q->where('template_id', $template))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.companies.index', [
            'companies' => $companies,
            'search' => $search,
            'status' => $status,
            'template' => $template,
            'templates' => Template::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(CompanyProfile $company): View
    {
        $company->load(['user', 'template', 'domains', 'pages'])
            ->loadCount(['services', 'products', 'projects', 'team', 'testimonials', 'gallery', 'menus', 'contactMessages', 'pageViews']);

        return view('admin.companies.show', compact('company'));
    }

    public function toggleStatus(CompanyProfile $company): RedirectResponse
    {
        $company->isPublished() ? $this->companies->unpublish($company) : $this->companies->publish($company);

        return back()->with('success', 'Status website diperbarui.');
    }

    public function destroy(CompanyProfile $company): RedirectResponse
    {
        $this->companies->delete($company);

        return redirect()->route('admin.companies.index')->with('success', 'Website dihapus.');
    }
}
