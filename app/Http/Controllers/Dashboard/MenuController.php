<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Navigation / sub menu builder.
 */
class MenuController extends Controller
{
    public function __construct(private readonly MenuService $menus) {}

    public function index(CompanyProfile $company): View
    {
        $this->authorize('update', $company);

        return view('dashboard.menus.index', [
            'company' => $company,
            'tree' => $this->menus->tree($company->menus()->with('page')->get()),
            'pages' => $company->pages()->get(),
            'types' => Menu::TYPES,
            'sections' => config('website-templates.sections'),
        ]);
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorize('update', $company);
        $this->menus->create($company, $this->validated($request));

        return back()->with('success', 'Menu ditambahkan.');
    }

    public function update(Request $request, CompanyProfile $company, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $company);
        $this->menus->update($menu, $this->validated($request));

        return back()->with('success', 'Menu diperbarui.');
    }

    public function destroy(CompanyProfile $company, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $company);
        $menu->delete();

        return back()->with('success', 'Menu dihapus.');
    }

    /**
     * Save drag & drop order and nesting.
     */
    public function tree(Request $request, CompanyProfile $company): JsonResponse
    {
        $this->authorize('update', $company);

        $data = $request->validate([
            'tree' => ['present', 'array'],
            'tree.*.id' => ['required', 'integer'],
            'tree.*.children' => ['nullable', 'array'],
            'tree.*.children.*.id' => ['required', 'integer'],
        ]);

        $this->menus->saveTree($company, $data['tree']);

        return response()->json(['saved' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'type' => ['required', Rule::in(array_keys(Menu::TYPES))],
            'url' => [
                'nullable', 'string', 'max:1000',
                Rule::when($request->input('type') === Menu::TYPE_URL, ['required', 'url:http,https']),
                Rule::when($request->input('type') === Menu::TYPE_ANCHOR, ['required', Rule::in(array_keys(config('website-templates.sections')))]),
            ],
            'company_page_id' => ['nullable', 'integer', Rule::requiredIf($request->input('type') === Menu::TYPE_PAGE)],
            'parent_id' => ['nullable', 'integer'],
            'open_in_new_tab' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);
    }
}
