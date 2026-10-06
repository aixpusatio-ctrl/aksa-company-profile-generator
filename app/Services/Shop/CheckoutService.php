<?php

namespace App\Services\Shop;

use App\Events\Shop\OrderPlaced;
use App\Models\CompanyProfile;
use App\Models\Shop\Cart;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\PaymentMethod;
use App\Services\Shop\Payment\PaymentService;
use App\Support\Shop\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Turns a cart into an order. Everything is recalculated server-side from
 * the database (prices, discounts, shipping, tax, stock) — values posted by
 * the browser are only used to identify choices (ids, quantities, address).
 */
class CheckoutService
{
    public function __construct(
        private readonly CartService $carts,
        private readonly CouponService $coupons,
        private readonly InventoryService $inventory,
        private readonly PaymentService $payments,
        private readonly ShopService $shop,
    ) {}

    /**
     * @param  array{name: string, email: string, phone?: ?string, whatsapp?: ?string, address?: ?array,
     *               shipping_method_id?: ?int, payment_method_id?: ?int, coupon?: ?string, notes?: ?string}  $data
     */
    public function placeOrder(CompanyProfile $company, Cart $cart, array $data, string $channel = 'web'): Order
    {
        $settings = $this->shop->settings($company);
        $email = Str::lower(trim($data['email']));

        $summary = $this->carts->summary($company, $cart, [
            'coupon' => $data['coupon'] ?? $cart->coupon_code,
            'email' => $email,
            'shipping_method_id' => $data['shipping_method_id'] ?? null,
            'city' => $data['address']['city'] ?? null,
            'province' => $data['address']['province'] ?? null,
            'postal_code' => $data['address']['postal_code'] ?? null,
        ]);

        $this->guard($company, $settings, $summary, $data, $channel);

        $paymentMethod = null;
        if ($channel === 'web') {
            $paymentMethod = $this->payments->methods($company)->firstWhere('id', (int) ($data['payment_method_id'] ?? 0))
                ?? throw ValidationException::withMessages(['payment_method_id' => 'Pilih metode pembayaran.']);
        }

        return DB::transaction(function () use ($company, $cart, $data, $summary, $email, $settings, $paymentMethod, $channel) {
            $customer = $this->resolveCustomer($company, $data, $email);
            $quote = $summary['shipping_quote'];

            $order = Order::query()->create([
                'company_profile_id' => $company->id,
                'customer_id' => $customer->id,
                'order_number' => $this->nextNumber($company, $settings->order_prefix),
                'access_token' => Str::random(48),
                'channel' => $channel,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'currency' => $settings->currency,
                'subtotal' => $summary['subtotal'],
                'discount' => $summary['discount'],
                'shipping_cost' => $summary['shipping'],
                'tax' => $summary['tax'],
                'total' => $summary['total'],
                'tax_name' => $summary['tax_name'],
                'tax_inclusive' => $summary['tax_inclusive'],
                'coupon_code' => $summary['coupon']?->code,
                'customer_name' => trim($data['name']),
                'customer_email' => $email,
                'customer_phone' => $data['phone'] ?? null,
                'customer_whatsapp' => $data['whatsapp'] ?? ($data['phone'] ?? null),
                'shipping_address' => $quote && ! $quote->requiresAddress ? null : ($data['address'] ?? null),
                'shipping_method' => $quote?->name ?? ($channel === 'whatsapp' ? 'Dikonfirmasi via WhatsApp' : null),
                'payment_method' => $paymentMethod?->name ?? ($channel === 'whatsapp' ? 'Dikonfirmasi via WhatsApp' : null),
                'notes' => isset($data['notes']) ? Str::limit(strip_tags($data['notes']), 1000, '') : null,
            ]);

            foreach ($summary['lines'] as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_variant_id' => $line['variant']?->id,
                    'product_name' => $line['name'],
                    'variant_label' => $line['variant_label'],
                    'sku' => $line['sku'],
                    'image' => $line['variant']?->image ?? $line['product']->images->first()?->image,
                    'price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['line_total'],
                    'metadata' => array_filter(['original_price' => $line['original_price'], 'weight' => $line['weight'] ?: null]),
                ]);
            }

            $order->load('items.product');
            $this->inventory->reserve($order);

            if ($summary['coupon']) {
                $this->coupons->redeem($summary['coupon'], $order, $summary['discount']);
            }

            if ($quote) {
                $order->shipment()->create(['provider' => $quote->provider, 'method' => $quote->name, 'cost' => $quote->cost, 'status' => 'pending']);
            }

            if ($paymentMethod instanceof PaymentMethod) {
                $this->payments->initiate($order, $paymentMethod);
            }

            $order->histories()->create(['type' => 'order', 'status' => 'pending', 'note' => $channel === 'whatsapp' ? 'Pesanan dibuat via WhatsApp checkout' : 'Pesanan dibuat']);

            if ($customer->isRegistered() && ! empty($data['address']['address']) && ! empty($data['save_address'])) {
                $customer->addresses()->firstOrCreate(
                    ['address' => $data['address']['address'], 'city' => $data['address']['city']],
                    $data['address'] + ['is_default' => ! $customer->addresses()->exists()]
                );
            }

            $this->carts->clear($cart);

            OrderPlaced::dispatch($order);

            return $order;
        });
    }

    /**
     * WhatsApp message for an order (the customer sends it themselves).
     */
    public function whatsappMessage(Order $order): string
    {
        $lines = ['Halo, saya ingin memesan:', ''];
        foreach ($order->items as $item) {
            $lines[] = $item->product_name.($item->variant_label ? " ({$item->variant_label})" : '');
            $lines[] = 'Qty: '.$item->quantity.' × '.Money::format($item->price, $order->currency);
            $lines[] = '';
        }
        if ((float) $order->discount > 0) {
            $lines[] = 'Diskon: -'.Money::format($order->discount, $order->currency);
        }
        $lines[] = 'Total: '.Money::format($order->total, $order->currency);
        $lines[] = 'Order: #'.$order->order_number;
        $lines[] = '';
        $lines[] = 'Nama: '.$order->customer_name;
        if ($order->shipping_address) {
            $lines[] = 'Alamat: '.collect($order->shipping_address)->only(['address', 'city', 'province', 'postal_code'])->filter()->implode(', ');
        }

        return implode("\n", $lines);
    }

    public function whatsappUrl(CompanyProfile $company, Order $order): ?string
    {
        $base = $company->whatsappUrl();

        return $base ? $base.'?text='.rawurlencode($this->whatsappMessage($order)) : null;
    }

    private function guard(CompanyProfile $company, $settings, array $summary, array $data, string $channel): void
    {
        $fail = fn (string $field, string $message) => throw ValidationException::withMessages([$field => $message]);

        if (! $company->hasShop()) {
            $fail('cart', 'Toko sedang tidak aktif.');
        }
        if (! $summary['lines']) {
            $fail('cart', 'Keranjang Anda kosong.');
        }
        if ($summary['errors']) {
            $fail('cart', $summary['errors'][0]);
        }
        if (! empty($data['coupon']) && ! $summary['coupon']) {
            $fail('coupon', $summary['coupon_error'] ?? 'Kode kupon tidak valid.');
        }
        if (($min = $settings->option('min_order')) && $summary['subtotal'] < (float) $min) {
            $fail('cart', 'Minimal pembelian '.Money::format($min).'.');
        }
        if ($channel === 'web' && ! $summary['shipping_quote']) {
            $fail('shipping_method_id', 'Pilih metode pengiriman yang tersedia.');
        }
        if ($summary['shipping_quote']?->requiresAddress && empty($data['address']['address'])) {
            $fail('address.address', 'Alamat pengiriman wajib diisi.');
        }
        if ($channel === 'whatsapp' && ! $company->whatsappUrl()) {
            $fail('cart', 'Toko belum memiliki nomor WhatsApp.');
        }
    }

    private function resolveCustomer(CompanyProfile $company, array $data, string $email): Customer
    {
        $customer = $this->carts->customer($company);

        if ($customer) {
            $customer->fill(array_filter(['phone' => $data['phone'] ?? null, 'whatsapp' => $data['whatsapp'] ?? null]))->save();

            return $customer;
        }

        // Guest checkout: keep a customer record (without password) for order history & CRM.
        return Customer::query()->firstOrCreate(
            ['company_profile_id' => $company->id, 'email' => $email],
            ['name' => trim($data['name']), 'phone' => $data['phone'] ?? null, 'whatsapp' => $data['whatsapp'] ?? null],
        );
    }

    private function nextNumber(CompanyProfile $company, string $prefix): string
    {
        $prefix = Str::upper(preg_replace('/[^A-Za-z0-9]/', '', $prefix) ?: 'INV');
        $last = Order::query()->where('company_profile_id', $company->id)->lockForUpdate()->count();

        do {
            $number = $prefix.'-'.str_pad((string) (++$last), 5, '0', STR_PAD_LEFT);
        } while (Order::query()->where('company_profile_id', $company->id)->where('order_number', $number)->exists());

        return $number;
    }
}
