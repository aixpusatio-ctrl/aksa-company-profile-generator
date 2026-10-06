<?php

namespace App\Http\Controllers\Shop;

use App\Services\Shop\CartService;
use App\Services\Shop\CheckoutService;
use App\Services\Shop\Payment\PaymentService;
use App\Services\Shop\Shipping\ShippingContext;
use App\Services\Shop\Shipping\ShippingService;
use App\Support\Shop\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Checkout: customer information → shipping address → shipping method →
 * payment method → review → place order (or order via WhatsApp).
 */
class CheckoutController extends ShopController
{
    public function __construct(
        private readonly CartService $carts,
        private readonly CheckoutService $checkout,
    ) {}

    public function show(Request $request, PaymentService $payments)
    {
        $company = $this->company();
        $cart = $this->carts->current($company);
        $summary = $this->carts->summary($company, $cart);

        if (! $summary['lines']) {
            return redirect()->to($this->site($request)->shop('cart'))->with('shop_toast', 'Keranjang Anda masih kosong.');
        }

        $customer = $this->carts->customer($company);

        return $this->shopView($request, 'websites.shop.checkout', [
            'summary' => $summary,
            'paymentMethods' => $payments->methods($company),
            'addresses' => $customer?->addresses ?? collect(),
            'prefill' => [
                'name' => old('name', $customer?->name),
                'email' => old('email', $customer?->email),
                'phone' => old('phone', $customer?->phone),
                'whatsapp' => old('whatsapp', $customer?->whatsapp),
            ],
        ], ['title' => 'Checkout', 'robots' => 'noindex,nofollow']);
    }

    /**
     * Shipping options + recalculated totals for the address being entered.
     */
    public function quote(Request $request, ShippingService $shipping): JsonResponse
    {
        $data = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_method_id' => ['nullable', 'integer'],
            'coupon' => ['nullable', 'string', 'max:40'],
        ]);

        $company = $this->company();
        $cart = $this->carts->current($company);
        $base = $this->carts->summary($company, $cart, ['coupon' => $data['coupon'] ?? null]);

        $quotes = $shipping->quotes($company, new ShippingContext(
            subtotal: $base['subtotal'] - $base['discount'],
            weightGrams: $base['weight'],
            quantity: $base['count'],
            city: $data['city'] ?? null,
            province: $data['province'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            freeShippingCoupon: $base['coupon']?->type === 'free_shipping',
        ));

        $summary = $this->carts->summary($company, $cart, $data + ['coupon' => $data['coupon'] ?? null]);

        return response()->json([
            'shipping_methods' => $quotes->map(fn ($q) => $q->toArray() + ['cost_formatted' => $q->cost > 0 ? Money::format($q->cost) : 'Gratis'])->values(),
            'totals' => [
                'subtotal' => Money::format($summary['subtotal']),
                'discount' => $summary['discount'] > 0 ? '-'.Money::format($summary['discount']) : null,
                'coupon' => $summary['coupon']?->code,
                'coupon_error' => $summary['coupon_error'],
                'shipping' => $summary['shipping_quote'] ? ($summary['shipping'] > 0 ? Money::format($summary['shipping']) : 'Gratis') : '—',
                'tax' => $summary['tax'] > 0 ? Money::format($summary['tax']) : null,
                'tax_label' => $summary['tax_name'] ? $summary['tax_name'].($summary['tax_inclusive'] ? ' (termasuk)' : '') : null,
                'total' => Money::format($summary['total']),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, whatsapp: false);
        $company = $this->company();
        $cart = $this->carts->current($company) ?? abort(422);

        $order = $this->checkout->placeOrder($company, $cart, $data);

        return redirect()->to($this->site($request)->shop('order/'.$order->order_number).'?token='.$order->access_token)
            ->with('shop_toast', 'Pesanan berhasil dibuat!');
    }

    /**
     * WhatsApp checkout: the order is created, then the customer is shown a
     * button that opens WhatsApp with a pre-filled message (never sent
     * automatically).
     */
    public function whatsapp(Request $request)
    {
        $company = $this->company();
        abort_unless($company->shopSetting->option('whatsapp_checkout') && $company->whatsappUrl(), 404);

        $data = $this->validated($request, whatsapp: true);
        $cart = $this->carts->current($company) ?? abort(422);
        $order = $this->checkout->placeOrder($company, $cart, $data, 'whatsapp');

        return redirect()->to($this->site($request)->shop('order/'.$order->order_number).'?token='.$order->access_token.'&wa=1');
    }

    private function validated(Request $request, bool $whatsapp): array
    {
        $company = $this->company();
        if (! $company->shopSetting->option('guest_checkout', true) && ! $this->carts->customer($company)) {
            abort(redirect()->to($this->site($request)->account('login'))->with('shop_toast', 'Silakan masuk untuk melanjutkan checkout.'));
        }

        $requirePhone = (bool) $this->company()->shopSetting->option('require_phone');

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => [$requirePhone ? 'required' : 'nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'address' => ['nullable', 'array'],
            'address.name' => ['nullable', 'string', 'max:120'],
            'address.phone' => ['nullable', 'string', 'max:30'],
            'address.address' => ['nullable', 'string', 'max:500'],
            'address.city' => [$whatsapp ? 'nullable' : 'required_with:address.address', 'nullable', 'string', 'max:100'],
            'address.province' => ['nullable', 'string', 'max:100'],
            'address.postal_code' => ['nullable', 'string', 'max:20'],
            'address.country' => ['nullable', 'string', 'max:100'],
            'save_address' => ['nullable', 'boolean'],
            'shipping_method_id' => [$whatsapp ? 'nullable' : 'required', 'nullable', 'integer'],
            'payment_method_id' => [$whatsapp ? 'nullable' : 'required', 'nullable', 'integer'],
            'coupon' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms' => ['accepted'],
        ], [], ['address.address' => 'alamat', 'address.city' => 'kota', 'shipping_method_id' => 'metode pengiriman', 'payment_method_id' => 'metode pembayaran', 'terms' => 'persetujuan']);
    }
}
