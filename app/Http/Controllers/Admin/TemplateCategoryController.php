<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TemplateCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => TemplateCategory::query()->withCount('templates')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        TemplateCategory::query()->create($this->validated($request));

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, TemplateCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(TemplateCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Kategori dihapus. Template di dalamnya menjadi tanpa kategori.');
    }

    private function validated(Request $request, ?TemplateCategory $category = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', Rule::unique('template_categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
