<?php

namespace App\Http\Controllers\Shop;

use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Services\Shop\CatalogService;
use App\Support\Shop\Money;
use App\Support\Shop\ProductSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorefrontController extends ShopController
{
    public function __construct(private readonly CatalogService $catalog) {}

    /** Shop homepage built from the shop section builder. */
    public function home(Request $request)
    {
        $company = $this->company();
        $base = fn () => $this->catalog->baseQuery($company);

        return $this->shopView($request, 'websites.shop.home', [
            'featured' => $base()->where('featured', true)->latest('published_at')->take(8)->get(),
            'bestSellers' => $base()->orderByDesc('sold_count')->orderByDesc('rating_avg')->take(8)->get(),
            'newArrivals' => $base()->latest('published_at')->latest('id')->take(8)->get(),
            'saleProducts' => $base()->onSale()->take(8)->get(),
            'brands' => Product::query()->where('company_profile_id', $company->id)->visible()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand'),
            'testimonials' => $company->testimonials()->get(),
        ], [
            'title' => $company->shopSetting->option('shop_title') ?: 'Shop',
            'description' => $company->shopSetting->description ?: 'Belanja produk '.$company->name.' secara online.',
        ]);
    }

    public function catalog(Request $request)
    {
        return $this->listing($request, null);
    }

    public function category(Request $request, string $slug)
    {
        $category = ProductCategory::query()->where('company_profile_id', $this->company()->id)->active()->where('slug', $slug)->firstOrFail();

        return $this->listing($request, $category);
    }

    private function listing(Request $request, ?ProductCategory $category)
    {
        $company = $this->company();
        $filters = $this->filters($request) + ['category' => $category];
        $perPage = (int) $company->shopSetting->option('products_per_page', 12);
        $products = $this->catalog->search($company, $filters, max(4, min(48, $perPage)));

        return $this->shopView($request, 'websites.shop.catalog', [
            'category' => $category?->load('children', 'parent'),
            'products' => $products,
            'filters' => $filters,
            'facets' => $this->catalog->facets($company, $category),
            'sorts' => CatalogService::SORTS,
        ], [
            'title' => $category?->name ?? ($filters['q'] ? 'Hasil pencarian “'.$filters['q'].'”' : 'Semua Produk'),
            'description' => $category?->description ?: 'Katalog produk '.$company->name,
            'canonical' => $request->url(),
            'robots' => $filters['q'] || $request->query() ? 'noindex,follow' : 'index,follow',
        ]);
    }

    /** Live search (JSON) used by the search box. */
    public function search(Request $request): JsonResponse
    {
        $term = Str::limit(trim($request->string('q')->toString()), 80, '');
        $company = $this->company();
        $site = $this->site($request);

        $results = mb_strlen($term) < 2 ? collect() : $this->catalog->quickSearch($company, $term);

        return response()->json([
            'query' => $term,
            'results' => $results->map(fn (Product $p) => [
                'name' => $p->name,
                'url' => $site->shop('product/'.$p->slug),
                'image' => $p->mainImage(),
                'price' => $p->formattedPrice(),
                'original' => $p->originalPrice() ? Money::format($p->originalPrice()) : null,
                'rating' => (float) $p->rating_avg,
                'reviews' => $p->rating_count,
                'available' => $p->isInStock(),
                'availability' => $p->availabilityLabel(),
                'category' => $p->category?->name,
            ])->values(),
            'all_url' => $site->shop('products').'?q='.urlencode($term),
        ]);
    }

    public function product(Request $request, string $slug)
    {
        $company = $this->company();
        $product = $this->catalog->baseQuery($company)->with(['options.values', 'category.parent'])->where('slug', $slug)->firstOrFail();
        $this->catalog->rememberViewed($company, $product);

        $reviews = $product->reviews()->approved()->latest()->take(20)->get();
        $site = $this->site($request);
        $canonical = $site->shop('product/'.$product->slug);

        return $this->shopView($request, 'websites.shop.product', [
            'product' => $product,
            'reviews' => $reviews,
            'ratingBreakdown' => $product->reviews()->approved()->selectRaw('rating, count(*) as total')->groupBy('rating')->pluck('total', 'rating'),
            'related' => $this->catalog->related($product),
            'recent' => $this->catalog->recentlyViewed($company, $product),
            'variantsJson' => $product->variants->where('is_active', true)->map(fn ($v) => [
                'id' => $v->id,
                'label' => $v->label,
                'values' => $v->option_value_ids,
                'sku' => $v->sku,
                'price' => $v->currentPrice($product),
                'price_formatted' => Money::format($v->currentPrice($product)),
                'original' => $v->basePrice($product) > $v->currentPrice($product) ? Money::format($v->basePrice($product)) : null,
                'available' => $product->track_stock && $product->stock_status !== 'backorder' ? $v->availableStock() : 999,
                'image' => $v->url('image'),
            ])->values(),
            'schema' => ProductSchema::for($product, $reviews, $canonical, $company),
        ], [
            'title' => $product->seo_title ?: $product->name,
            'description' => $product->seo_description ?: Str::limit(strip_tags((string) ($product->short_description ?: $product->description)), 160),
            'canonical' => $canonical,
            'og_image' => $product->mainImage() ?? null,
            'og_type' => 'product',
        ]);
    }

    private function filters(Request $request): array
    {
        return [
            'q' => Str::limit(trim($request->string('q')->toString()), 80, ''),
            'min' => is_numeric($request->query('min')) ? (float) $request->query('min') : null,
            'max' => is_numeric($request->query('max')) ? (float) $request->query('max') : null,
            'availability' => $request->query('availability') === 'in_stock' ? 'in_stock' : null,
            'rating' => (int) $request->query('rating', 0) ?: null,
            'brand' => array_slice(array_filter((array) $request->query('brand', []), 'is_string'), 0, 20),
            'attr' => collect((array) $request->query('attr', []))->filter(fn ($v, $k) => is_string($k))->map(fn ($v) => array_slice(array_filter((array) $v, 'is_string'), 0, 20))->take(5)->all(),
            'tag' => is_string($request->query('tag')) ? $request->query('tag') : null,
            'sale' => $request->boolean('sale'),
            'sort' => array_key_exists($request->query('sort'), CatalogService::SORTS) ? $request->query('sort') : 'newest',
        ];
    }
}
