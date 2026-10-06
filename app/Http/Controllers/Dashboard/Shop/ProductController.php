<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Product;
use App\Models\Shop\ProductImage;
use App\Services\MediaService;
use App\Services\Shop\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends SellerController
{
    public function __construct(private readonly ProductService $products) {}

    public function index(Request $request, CompanyProfile $company): View
    {
        $this->authorizeShop($company);
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();

        return view('dashboard.shop.products.index', [
            'company' => $company,
            'products' => $company->shopProducts()->with(['images', 'category', 'variants', 'tags'])
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
                ->when(in_array($status, Product::STATUSES, true), fn ($q) => $q->where('status', $status))
                ->when($request->integer('category'), fn ($q, $c) => $q->where('category_id', $c))
                ->latest()->paginate(20)->withQueryString(),
            'categories' => $company->productCategories()->get(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(CompanyProfile $company): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.products.form', $this->formData($company, new Product(['status' => 'draft', 'track_stock' => true, 'stock_status' => 'in_stock'])));
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);
        $product = $this->products->save($company, $this->validated($request, $company), $request->user());

        return redirect()->route('websites.shop.products.edit', [$company, $product])->with('success', 'Produk dibuat.');
    }

    public function edit(CompanyProfile $company, Product $shopProduct): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.products.form', $this->formData($company, $shopProduct->load(['images', 'options.values', 'variants', 'tags'])));
    }

    public function update(Request $request, CompanyProfile $company, Product $shopProduct): RedirectResponse
    {
        $this->authorizeShop($company);
        $this->products->save($company, $this->validated($request, $company, $shopProduct), $request->user(), $shopProduct);

        return back()->with('success', 'Produk disimpan.');
    }

    public function destroy(CompanyProfile $company, Product $shopProduct): RedirectResponse
    {
        $this->authorizeShop($company);

        // Products with order history are archived instead of deleted.
        if (\App\Models\Shop\OrderItem::query()->where('product_id', $shopProduct->id)->exists()) {
            $shopProduct->update(['status' => Product::STATUS_ARCHIVED]);

            return back()->with('success', 'Produk memiliki riwayat pesanan, sehingga diarsipkan.');
        }

        $shopProduct->delete();

        return redirect()->route('websites.shop.products.index', $company)->with('success', 'Produk dihapus.');
    }

    public function duplicate(CompanyProfile $company, Product $shopProduct): RedirectResponse
    {
        $this->authorizeShop($company);
        $copy = $this->products->duplicate($shopProduct);

        return redirect()->route('websites.shop.products.edit', [$company, $copy])->with('success', 'Produk diduplikasi sebagai draft.');
    }

    public function reorderImages(Request $request, CompanyProfile $company, Product $shopProduct): JsonResponse
    {
        $this->authorizeShop($company);
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];
        $this->products->reorderImages($shopProduct, $ids);

        return response()->json(['saved' => true]);
    }

    public function destroyImage(CompanyProfile $company, Product $shopProduct, ProductImage $image): RedirectResponse|JsonResponse
    {
        $this->authorizeShop($company);
        $image->delete();

        return request()->expectsJson() ? response()->json(['deleted' => true]) : back()->with('success', 'Gambar dihapus.');
    }

    private function formData(CompanyProfile $company, Product $product): array
    {
        return [
            'company' => $company,
            'product' => $product,
            'categories' => $company->productCategories()->get(),
            'tags' => $company->productTags()->get(),
            'allProducts' => $company->shopProducts()->whereKeyNot($product->id ?? 0)->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validated(Request $request, CompanyProfile $company, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'sku' => ['nullable', 'string', 'max:64', Rule::unique('products')->where('company_profile_id', $company->id)->ignore($product?->id)],
            'brand' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:50000'],
            'specifications' => ['nullable', 'array', 'max:30'],
            'specifications.*.label' => ['nullable', 'string', 'max:60'],
            'specifications.*.value' => ['nullable', 'string', 'max:250'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'sale_starts_at' => ['nullable', 'date'],
            'sale_ends_at' => ['nullable', 'date', 'after_or_equal:sale_starts_at'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'stock_status' => ['required', Rule::in(array_keys(Product::STOCK_STATUSES))],
            'weight' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'status' => ['required', Rule::in(Product::STATUSES)],
            'featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:120'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer'],
            'related_ids' => ['nullable', 'array', 'max:12'],
            'related_ids.*' => ['integer'],
            'cta' => ['nullable', 'array'],
            'cta.*' => ['nullable', Rule::in(['default', '1', '0'])],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => [MediaService::imageRule()],
            'options' => ['nullable', 'array', 'max:3'],
            'options.*.name' => ['nullable', 'string', 'max:60'],
            'options.*.values' => ['nullable', 'string', 'max:500'],
            'variants' => ['nullable', 'array', 'max:100'],
            'variants.*.sku' => ['nullable', 'string', 'max:64'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'variants.*.weight' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'has_variants' => ['nullable', 'boolean'],
        ], ['sale_price.lt' => 'Harga promo harus lebih kecil dari harga normal.']);

        $data['track_stock'] = $request->boolean('track_stock');
        $data['featured'] = $request->boolean('featured');
        $data['cta'] = collect($data['cta'] ?? [])->only(Product::CTAS)->reject(fn ($v) => $v === null || $v === 'default')->map(fn ($v) => $v === '1')->all() ?: null;

        // Variant data is keyed by the variant label (md5 to stay form-safe).
        if ($request->boolean('has_variants')) {
            $data['options'] ??= [];
        } else {
            $data['options'] = [];
            unset($data['variants']);
        }

        return $data;
    }
}
