<?php

namespace App\Http\Controllers\Site;

use App\Events\ContactMessageReceived;
use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Services\AnalyticsService;
use App\Services\SeoService;
use App\Services\WebsiteRendererService;
use App\Support\CurrentTenant;
use App\Support\Website\SiteContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Public company profile website (tenant host).
 */
class WebsiteController extends Controller
{
    public function __construct(
        private readonly CurrentTenant $tenant,
        private readonly WebsiteRendererService $renderer,
        private readonly AnalyticsService $analytics,
    ) {}

    public function home(Request $request)
    {
        $company = $this->company();
        $this->analytics->record($company, $request);

        return $this->renderer->render($company, SiteContext::live($request->getSchemeAndHttpHost()));
    }

    public function page(Request $request, string $slug)
    {
        $company = $this->company();
        $response = $this->renderer->render($company, SiteContext::live($request->getSchemeAndHttpHost(), false, $slug), $slug);
        $this->analytics->record($company, $request);

        return $response;
    }

    public function contact(Request $request): RedirectResponse
    {
        $company = $this->company();

        // Honeypot: bots fill hidden fields, humans don't.
        if (filled($request->input('website_url'))) {
            return redirect()->to($request->getSchemeAndHttpHost().'/#contact')->with('contact_success', true);
        }

        $data = $request->validateWithBag('contact', [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
        ]);

        $message = $company->contactMessages()->create($data + ['ip_address' => $request->ip()]);
        ContactMessageReceived::dispatch($message);

        return redirect()->to($request->getSchemeAndHttpHost().'/#contact')->with('contact_success', true);
    }

    public function sitemap(SeoService $seo): Response
    {
        return response($seo->sitemap($this->company()), 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(SeoService $seo): Response
    {
        return response($seo->robots($this->company()), 200, ['Content-Type' => 'text/plain']);
    }

    private function company(): CompanyProfile
    {
        return $this->tenant->get() ?? abort(404);
    }
}
