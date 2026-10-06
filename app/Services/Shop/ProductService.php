<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Models\Shop\ProductVariant;
use App\Models\User;
use App\Services\MediaService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seller-side product management: CRUD, images, options & variants, tags.
 */
class ProductService
{
    public function __construct(
        private readonly MediaService $media,
        private readonly InventoryService $inventory,
    ) {}

    public function save(CompanyProfile $company, array $data, User $user, ?Product $product = null): Product
    {
        return DB::transaction(function () use ($company, $data, $user, $product) {
            $attributes = collect($data)->only([
                'category_id', 'name', 'sku', 'brand', 'short_description', 'description', 'price', 'compare_price',
                'sale_price', 'sale_starts_at', 'sale_ends_at', 'cost_price', 'track_stock', 'low_stock_threshold',
                'stock_status', 'weight', 'status', 'featured', 'seo_title', 'seo_description', 'published_at', 'cta', 'related_ids',
            ])->all();

            $attributes['description'] = HtmlSanitizer::clean($attributes['description'] ?? null);
            $attributes['slug'] = $this->uniqueSlug($company, $data['slug'] ?? $data['name'], $product?->id);
            $attributes['specifications'] = collect($data['specifications'] ?? [])
                ->filter(fn ($row) => filled($row['label'] ?? null) && filled($row['value'] ?? null))
                ->map(fn ($row) => ['label' => Str::limit($row['label'], 60, ''), 'value' => Str::limit($row['value'], 250, '')])
                ->values()->all() ?: null;
            $attributes['related_ids'] = collect($data['related_ids'] ?? [])->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id !== $product?->id)
                ->intersect($company->shopProducts()->pluck('id'))->values()->all() ?: null;

            if (($attributes['status'] ?? null) === Product::STATUS_PUBLISHED && empty($attributes['published_at']) && ! $product?->published_at) {
                $attributes['published_at'] = now();
            }

            if (! empty($attributes['category_id']) && ! $company->productCategories()->whereKey($attributes['category_id'])->exists()) {
                $attributes['category_id'] = null;
            }

            if ($product) {
                $product->update($attributes);
            } else {
                $product = $company->shopProducts()->create($attributes + ['stock' => 0]);
                if (! empty($data['stock']) && (int) $data['stock'] > 0 && empty($data['variants'])) {
                    $this->inventory->adjust($product, null, (int) $data['stock'], 'Stok awal', $user->id);
                }
            }

            $tagIds = collect($data['tags'] ?? [])->map(fn ($id) => (int) $id)->intersect($company->productTags()->pluck('id'));
            $product->tags()->sync($tagIds->all());

            foreach ($data['images'] ?? [] as $upload) {
                if ($upload instanceof UploadedFile) {
                    $media = $this->media->upload($upload, $user, $company, 'images');
                    $product->images()->create(['image' => $media->path, 'alt' => $product->name, 'sort_order' => (int) $product->images()->max('sort_order') + 1]);
                }
            }

            if (array_key_exists('options', $data)) {
                $this->syncVariants($product, $data['options'] ?? [], $data['variants'] ?? [], $user);
            }

            return $product->fresh(['images', 'options.values', 'variants', 'tags', 'category']);
        });
    }

    /**
     * Options: [['name' => 'Size', 'values' => 'S, M, L'], ...]
     * Variants (keyed by label): ['M / Black' => ['sku' => ..., 'price' => ..., 'stock' => ...]]
     * Every combination of option values becomes a variant; existing
     * variants keep their id (and order history) when the label still exists.
     */
    public function syncVariants(Product $product, array $options, array $variantData, User $user): void
    {
        $options = collect($options)
            ->map(fn ($o) => ['name' => trim((string) ($o['name'] ?? '')), 'values' => collect(is_array($o['values'] ?? null) ? $o['values'] : explode(',', (string) ($o['values'] ?? '')))->map(fn ($v) => trim($v))->filter()->unique()->take(30)->values()])
            ->filter(fn ($o) => $o['name'] !== '' && $o['values']->isNotEmpty())
            ->take(3)
            ->values();

        $product->options()->delete();

        if ($options->isEmpty()) {
            $product->variants()->delete();

            return;
        }

        $valueSets = [];
        foreach ($options as $i => $option) {
            $model = $product->options()->create(['name' => $option['name'], 'sort_order' => $i]);
            $valueSets[] = $option['values']->map(fn ($value, $j) => $model->values()->create(['value' => $value, 'sort_order' => $j]))->all();
        }

        $combinations = [[]];
        foreach ($valueSets as $values) {
            $next = [];
            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $next[] = [...$combination, $value];
                }
            }
            $combinations = $next;
        }

        $existing = $product->variants()->get()->keyBy('label');
        $keep = [];

        foreach (array_slice($combinations, 0, 100) as $sort => $combination) {
            $label = collect($combination)->pluck('value')->implode(' / ');
            $input = $variantData[$label] ?? $variantData[md5($label)] ?? [];
            $variant = $existing->get($label) ?? new ProductVariant(['product_id' => $product->id, 'stock' => 0]);

            $variant->fill([
                'product_id' => $product->id,
                'label' => $label,
                'option_value_ids' => collect($combination)->pluck('id')->sort()->values()->all(),
                'sku' => filled($input['sku'] ?? null) ? $input['sku'] : ($variant->sku ?? $this->variantSku($product, $combination)),
                'price' => filled($input['price'] ?? null) ? (float) $input['price'] : ($variant->exists && ! array_key_exists('price', $input) ? $variant->price : null),
                'sale_price' => filled($input['sale_price'] ?? null) ? (float) $input['sale_price'] : null,
                'weight' => filled($input['weight'] ?? null) ? (int) $input['weight'] : $variant->weight,
                'is_active' => ! array_key_exists('is_active', $input) || (bool) $input['is_active'],
                'sort_order' => $sort,
            ])->save();

            if (array_key_exists('stock', $input) && is_numeric($input['stock'])) {
                $delta = (int) $input['stock'] - (int) $variant->stock;
                if ($delta !== 0) {
                    $this->inventory->adjust($product, $variant, $delta, 'Pengaturan stok varian', $user->id);
                }
            }

            $keep[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keep)->delete();
    }

    public function reorderImages(Product $product, array $ids): void
    {
        foreach (array_values($ids) as $i => $id) {
            $product->images()->whereKey($id)->update(['sort_order' => $i]);
        }
    }

    public function duplicate(Product $product): Product
    {
        return DB::transaction(function () use ($product) {
            $copy = $product->replicate(['slug', 'sold_count', 'view_count', 'rating_avg', 'rating_count', 'stock', 'reserved_stock']);
            $copy->name = $product->name.' (Copy)';
            $copy->slug = $this->uniqueSlug($product->companyProfile, $copy->name);
            $copy->status = Product::STATUS_DRAFT;
            $copy->stock = 0;
            $copy->save();

            foreach ($product->images as $image) {
                $copy->images()->create($image->only(['image', 'alt', 'sort_order']));
            }
            $copy->tags()->sync($product->tags->pluck('id'));

            return $copy;
        });
    }

    public function uniqueSlug(CompanyProfile $company, string $value, ?int $ignoreId = null): string
    {
        $base = Str::limit(Str::slug($value), 80, '') ?: 'produk';
        $slug = $base;
        $i = 2;

        while ($company->shopProducts()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function uniqueCategorySlug(CompanyProfile $company, string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'kategori';
        $slug = $base;
        $i = 2;

        while (ProductCategory::query()->where('company_profile_id', $company->id)->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function variantSku(Product $product, array $combination): string
    {
        $base = $product->sku ?: Str::upper(Str::substr(Str::slug($product->name, ''), 0, 6));

        return Str::upper($base.'-'.collect($combination)->map(fn ($v) => Str::substr(Str::slug($v->value, ''), 0, 3))->implode('-'));
    }
}
