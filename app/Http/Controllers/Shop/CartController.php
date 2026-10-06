<?php

namespace App\Http\Controllers\Shop;

use App\Services\Shop\CartService;
use App\Support\Shop\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends ShopController
{
    public function __construct(private readonly CartService $carts) {}

    public function show(Request $request)
    {
        $company = $this->company();

        return $this->shopView($request, 'websites.shop.cart', [
            'summary' => $this->carts->summary($company, $this->carts->current($company)),
        ], ['title' => 'Keranjang', 'robots' => 'noindex,follow']);
    }

    /** Cart contents as JSON (mini cart / drawer). */
    public function summary(Request $request): JsonResponse
    {
        return response()->json($this->payload($request));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'variant_id' => ['nullable', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.CartService::MAX_QUANTITY],
            'buy_now' => ['nullable', 'boolean'],
        ]);

        $this->carts->add($this->company(), (int) $data['product_id'], isset($data['variant_id']) ? (int) $data['variant_id'] : null, (int) ($data['quantity'] ?? 1));

        if ($request->boolean('buy_now')) {
            $url = $this->site($request)->shop('checkout');

            return $request->expectsJson() ? response()->json(['redirect' => $url]) : redirect()->to($url);
        }

        return $this->respond($request, $this->payload($request), 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $item)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:'.CartService::MAX_QUANTITY]]);
        $this->carts->update($this->company(), $item, (int) $data['quantity']);

        return $this->respond($request, $this->payload($request), 'Keranjang diperbarui.');
    }

    public function remove(Request $request, int $item)
    {
        $this->carts->remove($this->company(), $item);

        return $this->respond($request, $this->payload($request), 'Produk dihapus dari keranjang.');
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:40']]);
        $this->carts->applyCoupon($this->company(), $data['code']);

        return $this->respond($request, $this->payload($request), 'Kupon berhasil dipakai.');
    }

    public function removeCoupon(Request $request)
    {
        $this->carts->removeCoupon($this->company());

        return $this->respond($request, $this->payload($request), 'Kupon dihapus.');
    }

    private function payload(Request $request): array
    {
        $company = $this->company();
        $summary = $this->carts->summary($company, $this->carts->current($company));
        $site = $this->site($request);

        return [
            'count' => $summary['count'],
            'items' => collect($summary['lines'])->map(fn ($l) => [
                'id' => $l['id'],
                'name' => $l['name'],
                'variant' => $l['variant_label'],
                'url' => $site->shop('product/'.$l['product']->slug),
                'image' => $l['image'],
                'quantity' => $l['quantity'],
                'max' => $l['available'] ?? CartService::MAX_QUANTITY,
                'unit_price' => Money::format($l['unit_price']),
                'line_total' => Money::format($l['line_total']),
                'error' => $l['error'],
            ])->values(),
            'subtotal' => Money::format($summary['subtotal']),
            'discount' => $summary['discount'] > 0 ? Money::format($summary['discount']) : null,
            'coupon' => $summary['coupon']?->code,
            'total' => Money::format($summary['total']),
            'errors' => $summary['errors'],
            'cart_url' => $site->shop('cart'),
            'checkout_url' => $site->shop('checkout'),
        ];
    }
}
