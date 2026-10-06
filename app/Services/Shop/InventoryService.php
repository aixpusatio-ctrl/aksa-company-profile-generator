<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\InventoryMovement;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\Shop\ProductVariant;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Stock = units on hand, reserved = units held by open orders,
 * available = stock - reserved.
 *
 * Lifecycle: order placed → reserve; shipped/completed → sale (stock and
 * reservation both decrease); cancelled before shipping → release;
 * cancelled/refunded after shipping → return (stock increases).
 * All changes are atomic SQL updates guarded by conditions, so concurrent
 * checkouts can never oversell.
 */
class InventoryService
{
    /**
     * Manual stock adjustment (+/-) with a reason.
     */
    public function adjust(Product $product, ?ProductVariant $variant, int $quantity, string $reason, ?int $userId = null): InventoryMovement
    {
        return DB::transaction(function () use ($product, $variant, $quantity, $reason, $userId) {
            $model = $variant ?? $product;
            $model->refresh();

            if ($model->stock + $quantity < $model->reserved_stock) {
                throw ValidationException::withMessages(['quantity' => 'Stok tidak boleh lebih kecil dari stok yang sedang dipesan ('.$model->reserved_stock.').']);
            }

            $model->increment('stock', $quantity);
            $this->syncStockStatus($product);

            return $this->log($product, $variant, 'adjustment', $quantity, $reason, null, $userId);
        });
    }

    /**
     * Reserve stock for every line of an order. Throws when not enough stock.
     */
    public function reserve(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->track_stock) {
                continue;
            }

            $variant = $item->product_variant_id ? ProductVariant::query()->find($item->product_variant_id) : null;
            $model = $variant ?? $product;
            $allowBackorder = $product->stock_status === 'backorder';

            $updated = $model->newQuery()->whereKey($model->getKey())
                ->when(! $allowBackorder, fn ($q) => $q->whereRaw('stock - reserved_stock >= ?', [$item->quantity]))
                ->increment('reserved_stock', $item->quantity);

            if (! $updated) {
                throw ValidationException::withMessages([
                    'cart' => "Stok {$item->product_name}".($item->variant_label ? " ({$item->variant_label})" : '').' tidak mencukupi.',
                ]);
            }

            $this->log($product, $variant, 'reserve', -$item->quantity, 'Pesanan dibuat', $order->order_number);
        }
    }

    /** Release reservations of an order that will not be fulfilled. */
    public function release(Order $order, ?int $userId = null): void
    {
        $this->eachTracked($order, function ($model, $product, $variant, $item) use ($order, $userId) {
            $model->newQuery()->whereKey($model->getKey())->update(['reserved_stock' => $this->decrementReserved((int) $item->quantity)]);
            $this->log($product, $variant, 'release', $item->quantity, 'Pesanan dibatalkan', $order->order_number, $userId);
        });
    }

    /** Convert reservations into a sale (stock leaves the warehouse). */
    public function commit(Order $order, ?int $userId = null): void
    {
        $this->eachTracked($order, function ($model, $product, $variant, $item) use ($order, $userId) {
            $model->newQuery()->whereKey($model->getKey())->update([
                'stock' => DB::raw('stock - '.(int) $item->quantity),
                'reserved_stock' => $this->decrementReserved((int) $item->quantity),
            ]);
            $this->log($product, $variant, 'sale', -$item->quantity, 'Pesanan dikirim', $order->order_number, $userId);
        });

        foreach ($order->items as $item) {
            $item->product?->increment('sold_count', $item->quantity);
        }
    }

    /** Put shipped units back into stock (cancel/refund after shipping). */
    public function restock(Order $order, ?int $userId = null): void
    {
        $this->eachTracked($order, function ($model, $product, $variant, $item) use ($order, $userId) {
            $model->newQuery()->whereKey($model->getKey())->increment('stock', $item->quantity);
            $this->log($product, $variant, 'return', $item->quantity, 'Pesanan dibatalkan/refund', $order->order_number, $userId);
        });
    }

    /**
     * Products whose available stock is at or below their threshold.
     */
    public function lowStock(CompanyProfile $company): Collection
    {
        $threshold = $company->shopSetting?->low_stock_threshold ?? 5;

        return $company->shopProducts()->with('variants')->where('track_stock', true)->where('status', '!=', Product::STATUS_ARCHIVED)->get()
            ->filter(fn (Product $p) => $p->availableStock() <= ($p->low_stock_threshold ?? $threshold))
            ->values();
    }

    private function eachTracked(Order $order, callable $callback): void
    {
        DB::transaction(function () use ($order, $callback) {
            foreach ($order->items()->with('product')->get() as $item) {
                $product = $item->product;
                if (! $product || ! $product->track_stock) {
                    continue;
                }
                $variant = $item->product_variant_id ? ProductVariant::query()->find($item->product_variant_id) : null;
                $callback($variant ?? $product, $product, $variant, $item);
                $this->syncStockStatus($product);
            }
        });
    }

    /** Portable "reserved_stock - n, but never below zero". */
    private function decrementReserved(int $quantity): Expression
    {
        return DB::raw("CASE WHEN reserved_stock >= {$quantity} THEN reserved_stock - {$quantity} ELSE 0 END");
    }

    private function syncStockStatus(Product $product): void
    {
        $product->refresh()->load('variants');

        if ($product->stock_status === 'backorder' || ! $product->track_stock) {
            return;
        }

        $product->forceFill(['stock_status' => $product->availableStock() > 0 ? 'in_stock' : 'out_of_stock'])->save();
    }

    private function log(Product $product, ?ProductVariant $variant, string $type, int $quantity, ?string $reason, ?string $reference, ?int $userId = null): InventoryMovement
    {
        $model = ($variant ?? $product)->fresh();

        return InventoryMovement::query()->create([
            'company_profile_id' => $product->company_profile_id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'type' => $type,
            'quantity' => $quantity,
            'stock_after' => $model->stock,
            'reserved_after' => $model->reserved_stock,
            'reason' => $reason,
            'reference' => $reference,
            'user_id' => $userId ?? auth('web')->id(),
        ]);
    }
}
