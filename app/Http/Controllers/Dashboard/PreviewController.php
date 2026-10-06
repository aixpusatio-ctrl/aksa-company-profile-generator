<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Services\WebsiteRendererService;
use App\Support\Website\SiteContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Owner preview (works for drafts, shows draft pages too).
 */
class PreviewController extends Controller
{
    public function show(CompanyProfile $company): View
    {
        $this->authorize('view', $company);

        return view('dashboard.websites.preview', ['company' => $company->load('template')]);
    }

    public function frame(Request $request, CompanyProfile $company, WebsiteRendererService $renderer)
    {
        $this->authorize('view', $company);

        $page = $request->string('page')->toString() ?: null;
        $site = SiteContext::preview(route('websites.preview.frame', $company), $page === null, $page);

        return $renderer->render($company, $site, $page);
    }
}
