<?php

namespace App\Support\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * schema.org JSON-LD for product pages: Product + Offer (+ AggregateRating
 * and Review only when real approved reviews exist) and BreadcrumbList.
 */
class ProductSchema
{
    public static function for(Product $product, Collection $reviews, string $url, CompanyProfile $company): array
    {
        [$min, $max] = $product->priceRange();
        $currency = Money::currency();
        $base = rtrim($company->publicUrl(), '/');

        $offer = $min === $max
            ? ['@type' => 'Offer', 'price' => number_format($min, 2, '.', ''), 'priceCurrency' => $currency]
            : ['@type' => 'AggregateOffer', 'lowPrice' => number_format($min, 2, '.', ''), 'highPrice' => number_format($max, 2, '.', ''), 'priceCurrency' => $currency, 'offerCount' => $product->variants->where('is_active', true)->count()];

        $offer += [
            'availability' => 'https://schema.org/'.($product->isInStock() ? ($product->stock_status === 'backorder' ? 'BackOrder' : 'InStock') : 'OutOfStock'),
            'url' => $url,
            'seller' => ['@type' => 'Organization', 'name' => $company->name],
        ];
        if ($product->isOnSale() && $product->sale_ends_at) {
            $offer['priceValidUntil'] = $product->sale_ends_at->toDateString();
        }

        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => Str::limit(strip_tags((string) ($product->short_description ?: $product->description)), 500) ?: null,
            'image' => $product->images->map(fn ($i) => $i->url('image'))->filter()->values()->all() ?: null,
            'sku' => $product->sku,
            'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand] : null,
            'category' => $product->category?->name,
            'url' => $url,
            'offers' => $offer,
        ], fn ($v) => $v !== null && $v !== '');

        if ($product->rating_count > 0) {
            $schema['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => (string) round((float) $product->rating_avg, 1), 'reviewCount' => $product->rating_count];
            $schema['review'] = $reviews->take(5)->map(fn ($r) => array_filter([
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $r->name],
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $r->rating, 'bestRating' => 5],
                'reviewBody' => $r->body,
                'datePublished' => $r->created_at?->toDateString(),
            ]))->values()->all();
        }

        $crumbs = [['Beranda', $base.'/'], ['Shop', $base.'/shop']];
        if ($product->category?->parent) {
            $crumbs[] = [$product->category->parent->name, $base.'/shop/category/'.$product->category->parent->slug];
        }
        if ($product->category) {
            $crumbs[] = [$product->category->name, $base.'/shop/category/'.$product->category->slug];
        }
        $crumbs[] = [$product->name, $url];

        return [
            $schema,
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($crumbs)->values()->map(fn ($c, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1]])->all(),
            ],
        ];
    }
}
