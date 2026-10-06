<?php

namespace App\Http\Controllers\Shop;

use App\Services\Shop\CartService;
use App\Services\Shop\CatalogService;
use App\Services\Shop\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends ShopController
{
    public function __construct(private readonly WishlistService $wishlists) {}

    public function index(Request $request, CatalogService $catalog)
    {
        $company = $this->company();
        $ids = $this->wishlists->ids($company);

        return $this->shopView($request, 'websites.shop.wishlist', [
            'products' => $ids ? $catalog->baseQuery($company)->whereIn('id', $ids)->get() : collect(),
        ], ['title' => 'Wishlist', 'robots' => 'noindex,follow']);
    }

    public function toggle(Request $request, int $product)
    {
        $added = $this->wishlists->toggle($this->company(), $product);

        return $this->respond($request, ['in_wishlist' => $added, 'count' => count($this->wishlists->ids($this->company()))],
            $added ? 'Ditambahkan ke wishlist.' : 'Dihapus dari wishlist.');
    }

    public function moveToCart(Request $request, int $product, CartService $carts)
    {
        $company = $this->company();
        [$item] = $carts->resolvePurchasable($company, $product, $request->integer('variant_id') ?: null);
        $carts->add($company, $item->id, $request->integer('variant_id') ?: null, 1);
        $this->wishlists->remove($company, $product);

        return $this->respond($request, ['count' => $carts->count($company)], 'Produk dipindahkan ke keranjang.');
    }
}
