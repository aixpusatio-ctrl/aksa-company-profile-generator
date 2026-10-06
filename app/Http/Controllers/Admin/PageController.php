<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyPage;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Moderation of custom pages across all tenant websites.
 */
class PageController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $pages = CompanyPage::query()
            ->with('companyProfile.user')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when(in_array($request->string('status')->toString(), ['draft', 'published'], true), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.index', compact('pages', 'search'));
    }

    public function toggleStatus(CompanyPage $page): RedirectResponse
    {
        $page->update(['status' => $page->isPublished() ? CompanyPage::STATUS_DRAFT : CompanyPage::STATUS_PUBLISHED]);
        Activity::log('admin.page_status', "Halaman {$page->title} → {$page->status}", $page);

        return back()->with('success', 'Status halaman diperbarui.');
    }

    public function destroy(CompanyPage $page): RedirectResponse
    {
        Activity::log('admin.page_deleted', "Halaman {$page->title} dihapus admin", null, ['company_profile_id' => $page->company_profile_id]);
        $page->delete();

        return back()->with('success', 'Halaman dihapus.');
    }
}
