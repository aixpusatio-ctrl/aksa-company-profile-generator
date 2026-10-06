<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateCategory;
use App\Services\TemplateService;
use App\Services\WebsiteRendererService;
use App\Support\Website\SiteContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTemplateController extends Controller
{
    public function index(Request $request, TemplateService $templates): View
    {
        return view('templates.index', [
            'templates' => $templates->published($request->string('category')->toString() ?: null, $request->string('q')->toString() ?: null),
            'categories' => TemplateCategory::query()->whereHas('templates', fn ($q) => $q->published())->orderBy('sort_order')->get(),
            'activeCategory' => $request->string('category')->toString(),
        ]);
    }

    public function show(Request $request, Template $template): View
    {
        $this->ensureVisible($request, $template);

        return view('templates.show', [
            'template' => $template->load('category'),
            'related' => Template::query()->published()->whereKeyNot($template->id)->ordered()->take(3)->get(),
        ]);
    }

    /**
     * Full website preview of the template filled with demo content.
     */
    public function render(Request $request, Template $template, TemplateService $templates, WebsiteRendererService $renderer)
    {
        $this->ensureVisible($request, $template);

        $company = $templates->sampleCompany($template);
        $page = $request->string('page')->toString() ?: null;
        $site = SiteContext::template(route('templates.render', $template), $page === null, $page);

        return $renderer->render($company, $site, $page);
    }

    private function ensureVisible(Request $request, Template $template): void
    {
        abort_unless($template->isPublished() || $request->user()?->isAdmin(), 404);
    }
}
