<?php

namespace App\Http\Controllers\Shop;

use App\Models\Shop\Order;
use App\Services\Shop\CartService;
use App\Services\Shop\CheckoutService;
use App\Services\Shop\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Order success / tracking page. Access requires the secret token from the
 * order link, or being the logged-in customer who owns the order.
 */
class OrderController extends ShopController
{
    public function show(Request $request, string $number, CartService $carts, OrderService $orders, CheckoutService $checkout)
    {
        $company = $this->company();
        $order = Order::query()->where('company_profile_id', $company->id)->where('order_number', $number)
            ->with(['items', 'histories', 'payment', 'shipment'])->firstOrFail();

        $customer = $carts->customer($company);
        $authorized = ($customer && $order->customer_id === $customer->id)
            || hash_equals($order->access_token, (string) $request->query('token'));

        abort_unless($authorized, 404);

        return $this->shopView($request, 'websites.shop.order', [
            'order' => $order,
            'timeline' => $orders->timeline($order),
            'whatsappUrl' => $order->channel === 'whatsapp' || $request->boolean('wa') ? $checkout->whatsappUrl($company, $order) : null,
            'justPlaced' => $order->created_at->gt(now()->subMinutes(30)),
        ], ['title' => 'Pesanan #'.$order->order_number, 'robots' => 'noindex,nofollow']);
    }

    public function track(Request $request)
    {
        return $this->shopView($request, 'websites.shop.track', [], ['title' => 'Lacak Pesanan', 'robots' => 'noindex,follow']);
    }

    public function lookup(Request $request)
    {
        $data = $request->validate(['order_number' => ['required', 'string', 'max:40'], 'email' => ['required', 'email']]);

        $order = Order::query()->where('company_profile_id', $this->company()->id)
            ->where('order_number', Str::upper(trim($data['order_number'])))
            ->where('customer_email', Str::lower(trim($data['email'])))
            ->first();

        if (! $order) {
            throw ValidationException::withMessages(['order_number' => 'Pesanan tidak ditemukan. Periksa nomor pesanan dan email Anda.']);
        }

        return redirect()->to($this->site($request)->shop('order/'.$order->order_number).'?token='.$order->access_token);
    }
}
