<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\TemplateCategory;
use App\Services\TemplateService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(TemplateService $templates): View
    {
        $all = $templates->published();

        return view('landing.index', [
            'templates' => $all->take(6),
            'templateCount' => $all->count(),
            'categories' => TemplateCategory::query()->whereHas('templates', fn ($q) => $q->published())->orderBy('sort_order')->get(),
            'examples' => CompanyProfile::query()->published()->with('template')->latest('published_at')->take(3)->get(),
            'plans' => config('platform.plans'),
        ]);
    }

    public function robots(): Response
    {
        return response("User-agent: *\nDisallow: /dashboard\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n", 200, ['Content-Type' => 'text/plain']);
    }

    public function sitemap(TemplateService $templates): Response
    {
        $urls = collect([route('home'), route('templates.index'), route('register'), route('login')])
            ->merge($templates->published()->map(fn ($t) => route('templates.show', $t)));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.e($url).'</loc></url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
