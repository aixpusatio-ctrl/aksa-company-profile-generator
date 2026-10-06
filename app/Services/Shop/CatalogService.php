<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Storefront queries: listing with search, filters & sorting, live search,
 * related products and recently viewed.
 */
class CatalogService
{
    public const SORTS = [
        'newest' => 'Terbaru',
        'oldest' => 'Terlama',
        'price_asc' => 'Harga: rendah → tinggi',
        'price_desc' => 'Harga: tinggi → rendah',
        'popular' => 'Terpopuler',
        'rating' => 'Rating terbaik',
        'name' => 'Nama A → Z',
    ];

    public function baseQuery(CompanyProfile $company): Builder
    {
        return Product::query()
            ->where('company_profile_id', $company->id)
            ->visible()
            ->with(['images', 'variants', 'tags', 'category']);
    }

    /** SQL expression for the price a customer currently pays. */
    public static function priceSql(): string
    {
        $now = DB::getQueryGrammar()->quoteString(now()->toDateTimeString());

        return "CASE WHEN sale_price IS NOT NULL AND sale_price < price
                    AND (sale_starts_at IS NULL OR sale_starts_at <= {$now})
                    AND (sale_ends_at IS NULL OR sale_ends_at >= {$now})
                THEN sale_price ELSE price END";
    }

    /**
     * @param  array{q?: string, category?: ProductCategory|null, min?: numeric, max?: numeric, availability?: string,
     *               rating?: int, brand?: array|string, attr?: array, tag?: string, sale?: bool, sort?: string}  $filters
     */
    public function search(CompanyProfile $company, array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return $this->filtered($company, $filters)->paginate($perPage)->withQueryString();
    }

    public function filtered(CompanyProfile $company, array $filters): Builder
    {
        $price = self::priceSql();
        $query = $this->baseQuery($company);

        if ($term = trim((string) ($filters['q'] ?? ''))) {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';
            $query->where(fn ($q) => $q->where('name', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('brand', 'like', $like)
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like))
                ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', $like)));
        }

        if (($category = $filters['category'] ?? null) instanceof ProductCategory) {
            $query->whereIn('category_id', $category->descendantIds());
        }

        if (is_numeric($filters['min'] ?? null)) {
            $query->whereRaw("({$price}) >= ?", [(float) $filters['min']]);
        }
        if (is_numeric($filters['max'] ?? null)) {
            $query->whereRaw("({$price}) <= ?", [(float) $filters['max']]);
        }

        if (($filters['availability'] ?? null) === 'in_stock') {
            $query->where('stock_status', '!=', 'out_of_stock')
                ->where(fn ($q) => $q->where('track_stock', false)->orWhere('stock_status', 'backorder')
                    ->orWhereRaw('stock - reserved_stock > 0')
                    ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->whereRaw('stock - reserved_stock > 0')));
        }

        if (($rating = (int) ($filters['rating'] ?? 0)) > 0) {
            $query->where('rating_avg', '>=', min(5, $rating));
        }

        if ($brands = array_filter((array) ($filters['brand'] ?? []))) {
            $query->whereIn('brand', $brands);
        }

        // Attribute filters: attr[Size][]=M&attr[Color][]=Black
        foreach ((array) ($filters['attr'] ?? []) as $option => $values) {
            $values = array_filter((array) $values, 'is_string');
            if ($values) {
                $query->whereHas('options', fn ($o) => $o->where('name', (string) $option)->whereHas('values', fn ($v) => $v->whereIn('value', $values)));
            }
        }

        if ($tag = $filters['tag'] ?? null) {
            $query->whereHas('tags', fn ($t) => $t->where('slug', $tag));
        }

        if (! empty($filters['sale'])) {
            $query->onSale();
        }

        return match ($filters['sort'] ?? 'newest') {
            'oldest' => $query->orderBy('published_at')->orderBy('id'),
            'price_asc' => $query->orderByRaw("({$price}) asc"),
            'price_desc' => $query->orderByRaw("({$price}) desc"),
            'popular' => $query->orderByDesc('sold_count')->orderByDesc('view_count'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('rating_count'),
            'name' => $query->orderBy('name'),
            default => $query->latest('published_at')->latest('id'),
        };
    }

    /**
     * Facets for the filter sidebar (brands, options, price bounds).
     */
    public function facets(CompanyProfile $company, ?ProductCategory $category = null): array
    {
        $ids = $this->filtered($company, ['category' => $category])->reorder()->pluck('id');
        $price = self::priceSql();

        $options = DB::table('product_options')
            ->join('product_option_values', 'product_option_values.product_option_id', '=', 'product_options.id')
            ->whereIn('product_options.product_id', $ids)
            ->select('product_options.name', 'product_option_values.value')
            ->distinct()->get()
            ->groupBy('name')->map(fn ($rows) => $rows->pluck('value')->unique()->values()->all())->all();

        $bounds = Product::query()->whereIn('id', $ids)->selectRaw("min({$price}) as min, max({$price}) as max")->first();

        return [
            'brands' => Product::query()->whereIn('id', $ids)->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand')->all(),
            'options' => $options,
            'min_price' => (float) ($bounds->min ?? 0),
            'max_price' => (float) ($bounds->max ?? 0),
        ];
    }

    /** Quick results for the search box (JSON). */
    public function quickSearch(CompanyProfile $company, string $term, int $limit = 6): Collection
    {
        return $this->filtered($company, ['q' => $term, 'sort' => 'popular'])->limit($limit)->get();
    }

    /**
     * Related products: manual selection first, then same category / shared tags.
     */
    public function related(Product $product, int $limit = 4): Collection
    {
        $company = $product->companyProfile;
        $manual = collect($product->related_ids ?? []);

        $related = $manual->isNotEmpty()
            ? $this->baseQuery($company)->whereIn('id', $manual)->get()->sortBy(fn ($p) => $manual->search($p->id))->values()
            : collect();

        if ($related->count() < $limit) {
            $tagIds = $product->tags->pluck('id');
            $more = $this->baseQuery($company)
                ->whereKeyNot($product->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->where(fn ($q) => $q->where('category_id', $product->category_id)
                    ->when($tagIds->isNotEmpty(), fn ($q) => $q->orWhereHas('tags', fn ($t) => $t->whereIn('product_tags.id', $tagIds))))
                ->orderByDesc('sold_count')
                ->limit($limit - $related->count())
                ->get();
            $related = $related->concat($more);
        }

        return $related->take($limit)->values();
    }

    public function rememberViewed(CompanyProfile $company, Product $product): void
    {
        $key = 'shop_viewed_'.$company->id;
        $ids = collect(session($key, []))->reject(fn ($id) => $id === $product->id)->prepend($product->id)->take(12)->values()->all();
        session([$key => $ids]);
        Product::query()->whereKey($product->id)->increment('view_count');
    }

    public function recentlyViewed(CompanyProfile $company, ?Product $except = null, int $limit = 4): Collection
    {
        $ids = collect(session('shop_viewed_'.$company->id, []))->reject(fn ($id) => $id === $except?->id)->take($limit)->values();

        return $ids->isEmpty() ? collect() : $this->baseQuery($company)->whereIn('id', $ids)->get()->sortBy(fn ($p) => $ids->search($p->id))->values();
    }

    /** Active categories as a tree (roots with children). */
    public function categoryTree(CompanyProfile $company): Collection
    {
        $all = ProductCategory::query()->where('company_profile_id', $company->id)->active()->withCount(['products' => fn ($q) => $q->visible()])->orderBy('sort_order')->orderBy('name')->get();

        return $all->whereNull('parent_id')->map(function ($root) use ($all) {
            $root->setRelation('children', $all->where('parent_id', $root->id)->values());

            return $root;
        })->values();
    }
}
