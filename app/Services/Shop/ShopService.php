<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\Shop\ProductTag;
use App\Models\Shop\ShopSetting;
use App\Support\Activity;
use App\Support\Shop\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Shop on/off switch, settings, defaults and seller dashboard statistics.
 */
class ShopService
{
    public function settings(CompanyProfile $company): ShopSetting
    {
        $settings = $company->relationLoaded('shopSetting') && $company->shopSetting
            ? $company->shopSetting
            : ShopSetting::query()->firstOrCreate(['company_profile_id' => $company->id], ['name' => $company->name]);

        $company->setRelation('shopSetting', $settings);
        Money::setCurrency($settings->currency);

        return $settings;
    }

    /**
     * Turn the online shop on. The first time, sensible defaults are created
     * (tags, pickup + flat shipping, bank transfer + COD).
     */
    public function enable(CompanyProfile $company): void
    {
        DB::transaction(function () use ($company) {
            $this->settings($company);
            $this->ensureDefaults($company);
            $company->update(['shop_enabled' => true]);
        });

        Activity::log('shop.enabled', "Online shop {$company->name} diaktifkan", $company);
    }

    public function disable(CompanyProfile $company): void
    {
        $company->update(['shop_enabled' => false]);
        Activity::log('shop.disabled', "Online shop {$company->name} dinonaktifkan", $company);
    }

    public function ensureDefaults(CompanyProfile $company): void
    {
        foreach (ProductTag::DEFAULTS as $name => $color) {
            $company->productTags()->firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'color' => $color]);
        }

        if (! $company->shippingMethods()->exists()) {
            $company->shippingMethods()->createMany([
                ['type' => 'pickup', 'name' => 'Ambil di toko', 'description' => $company->fullAddress() ?: 'Ambil langsung di lokasi kami', 'estimate' => 'Hari yang sama', 'cost' => 0, 'sort_order' => 1],
                ['type' => 'flat', 'name' => 'Pengiriman reguler', 'description' => 'Kurir reguler ke seluruh Indonesia', 'estimate' => '2-5 hari kerja', 'cost' => 20000, 'min_order' => 500000, 'sort_order' => 2],
            ]);
        }

        if (! $company->paymentMethods()->exists()) {
            $company->paymentMethods()->createMany([
                ['type' => 'bank_transfer', 'name' => 'Transfer Bank', 'instructions' => 'Transfer sesuai total pesanan, lalu kirim bukti transfer melalui WhatsApp.', 'config' => ['bank' => 'Bank Nusantara', 'account_number' => '1234567890', 'account_name' => $company->name], 'sort_order' => 1],
                ['type' => 'cod', 'name' => 'Bayar di Tempat (COD)', 'instructions' => 'Bayar tunai saat pesanan diterima.', 'sort_order' => 2],
            ]);
        }
    }

    /**
     * Seller dashboard statistics.
     */
    public function stats(CompanyProfile $company, int $days = 30): array
    {
        $orders = Order::query()->where('company_profile_id', $company->id);
        $valid = (clone $orders)->whereNotIn('status', ['cancelled', 'refunded']);
        $from = now()->subDays($days - 1)->startOfDay();

        $daily = (clone $valid)->where('created_at', '>=', $from)->get(['created_at', 'total', 'payment_status'])
            ->groupBy(fn ($o) => Carbon::parse($o->created_at)->toDateString());

        $series = collect(range(0, $days - 1))->map(function ($i) use ($from, $daily) {
            $date = $from->copy()->addDays($i)->toDateString();
            $rows = $daily[$date] ?? collect();

            return ['date' => $date, 'orders' => $rows->count(), 'revenue' => (float) $rows->sum('total')];
        });

        $threshold = $this->settings($company)->low_stock_threshold;

        return [
            'products' => $company->shopProducts()->count(),
            'published_products' => $company->shopProducts()->where('status', Product::STATUS_PUBLISHED)->count(),
            'orders' => (clone $orders)->count(),
            'pending' => (clone $orders)->where('status', 'pending')->count(),
            'completed' => (clone $orders)->where('status', 'completed')->count(),
            'revenue' => (float) (clone $valid)->where('payment_status', 'paid')->sum('total'),
            'customers' => $company->customers()->count(),
            'low_stock' => app(InventoryService::class)->lowStock($company)->count(),
            'sold' => (int) DB::table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.company_profile_id', $company->id)->whereIn('orders.status', ['shipped', 'completed'])->sum('order_items.quantity'),
            'series' => $series,
            'top_products' => DB::table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.company_profile_id', $company->id)->whereNotIn('orders.status', ['cancelled', 'refunded'])
                ->selectRaw('order_items.product_name as name, sum(order_items.quantity) as quantity, sum(order_items.subtotal) as revenue')
                ->groupBy('order_items.product_name')->orderByDesc('quantity')->limit(5)->get(),
            'threshold' => $threshold,
        ];
    }
}
