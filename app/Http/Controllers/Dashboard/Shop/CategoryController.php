<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\ProductCategory;
use App\Models\Shop\ProductTag;
use App\Services\MediaService;
use App\Services\Shop\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Nested product categories (parent_id) and product tags.
 */
class CategoryController extends SellerController
{
    public function __construct(
        private readonly ProductService $products,
        private readonly MediaService $media,
    ) {}

    public function index(CompanyProfile $company): View
    {
        $this->authorizeShop($company);
        $all = $company->productCategories()->withCount('products')->get();

        return view('dashboard.shop.categories.index', [
            'company' => $company,
            'tree' => $all->whereNull('parent_id')->map(fn ($root) => $root->setRelation('children', $all->where('parent_id', $root->id)->values()))->values(),
            'roots' => $all->whereNull('parent_id')->values(),
            'tags' => $company->productTags()->withCount('products')->get(),
        ]);
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);
        $company->productCategories()->create($this->validated($request, $company));

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, CompanyProfile $company, ProductCategory $productCategory): RedirectResponse
    {
        $this->authorizeShop($company);
        $productCategory->update($this->validated($request, $company, $productCategory));

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(CompanyProfile $company, ProductCategory $productCategory): RedirectResponse
    {
        $this->authorizeShop($company);
        $productCategory->delete();

        return back()->with('success', 'Kategori dihapus. Produk di dalamnya menjadi tanpa kategori.');
    }

    public function storeTag(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'color' => ['required', Rule::in(ProductTag::COLORS)],
        ]);
        $company->productTags()->firstOrCreate(['slug' => Str::slug($data['name'])], $data);

        return back()->with('success', 'Tag ditambahkan.');
    }

    public function destroyTag(CompanyProfile $company, ProductTag $productTag): RedirectResponse
    {
        $this->authorizeShop($company);
        $productTag->delete();

        return back()->with('success', 'Tag dihapus.');
    }

    private function validated(Request $request, CompanyProfile $company, ?ProductCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'parent_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', MediaService::imageRule()],
            'image_media' => ['nullable', 'string', 'max:255'],
            'image_remove' => ['nullable', 'boolean'],
        ]);

        // Parent must belong to the same shop, be a root, and not be the category itself.
        $parent = ! empty($data['parent_id']) ? $company->productCategories()->whereKey($data['parent_id'])->whereNull('parent_id')->first() : null;
        $data['parent_id'] = $parent && $parent->id !== $category?->id && ! ($category && $category->children()->exists()) ? $parent->id : null;
        $data['slug'] = $this->products->uniqueCategorySlug($company, $data['slug'] ?? $data['name'], $category?->id);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['image'] = $this->media->resolveImageInput($data, 'image', $request->user(), $company, $category?->image);

        return collect($data)->except(['image_media', 'image_remove'])->all();
    }
}
