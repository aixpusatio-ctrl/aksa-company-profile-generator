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
            // Filtering & search happen instantly in the browser.
            'templates' => $templates->published(),
            'categories' => TemplateCategory::query()->whereHas('templates', fn ($q) => $q->published())->orderBy('sort_order')->get(),
            'activeCategory' => $request->string('category')->toString(),
        ]);
    }

    public function show(Request $request, Template $template): View
    {
        $this->ensureVisible($request, $template);

        return view('templates.show', [
            'template' => $template->load('category'),
            'related' => Template::query()->published()->whereKeyNot($template->id)
                ->where('template_category_id', $template->template_category_id)->ordered()->take(3)->get(),
        ]);
    }

    /**
     * Full website preview of the template filled with demo content.
     */
    public function render(Request $request, Template $template, TemplateService $templates, WebsiteRendererService $renderer)
    {
        $this->ensureVisible($request, $template);

        $this->applyPreviewOverrides($request, $template);

        $company = $templates->sampleCompany($template);
        $page = $request->string('page')->toString() ?: null;
        $site = SiteContext::template(route('templates.render', $template), $page === null, $page);

        $response = response($renderer->render($company, $site, $page)->render());

        // Previews of published templates are identical for everyone: let browsers cache them briefly.
        if ($template->isPublished() && ! $request->hasAny(['c', 'd'])) {
            $response->setPublic()->setMaxAge(300);
        }

        return $response;
    }

    /**
     * Admins (and local development) can try other component variants or
     * design tokens without saving: ?c[hero]=editorial&d[theme]=dark
     */
    private function applyPreviewOverrides(Request $request, Template $template): void
    {
        if (! $template->isComposed() || ! (app()->isLocal() || $request->user()?->isAdmin())) {
            return;
        }

        $components = array_filter((array) $request->query('c', []), 'is_string');
        $design = array_filter((array) $request->query('d', []), 'is_string');

        if ($components || $design) {
            $config = $template->config ?? [];
            $config['components'] = array_merge($config['components'] ?? [], $components);
            $config['design'] = array_merge($config['design'] ?? [], $design);
            $template->config = $config;
        }
    }

    private function ensureVisible(Request $request, Template $template): void
    {
        abort_unless($template->isPublished() || $request->user()?->isAdmin(), 404);
    }
}
