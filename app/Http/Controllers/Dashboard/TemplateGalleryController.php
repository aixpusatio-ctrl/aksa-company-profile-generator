<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TemplateCategory;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateGalleryController extends Controller
{
    public function index(Request $request, TemplateService $templates): View
    {
        $category = $request->string('category')->toString() ?: null;

        return view('dashboard.templates.index', [
            'templates' => $templates->published($category, $request->string('q')->toString() ?: null),
            'categories' => TemplateCategory::query()->orderBy('sort_order')->get(),
            'activeCategory' => $category,
        ]);
    }
}
