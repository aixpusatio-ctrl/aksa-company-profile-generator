<?php

namespace App\Services\Shop;

use App\Models\Shop\Order;
use App\Notifications\Shop\OrderStatusUpdatedNotification;
use App\Support\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Order lifecycle: pending → confirmed → processing → packed → shipped →
 * completed, with cancel/refund branches. Every change is recorded in the
 * order timeline and keeps inventory & coupons consistent.
 */
class OrderService
{
    /** Allowed transitions (from => [to...]). */
    public const TRANSITIONS = [
        'pending' => ['confirmed', 'processing', 'cancelled'],
        'confirmed' => ['processing', 'packed', 'shipped', 'cancelled'],
        'processing' => ['packed', 'shipped', 'cancelled'],
        'packed' => ['shipped', 'cancelled'],
        'shipped' => ['completed', 'refunded'],
        'completed' => ['refunded'],
        'cancelled' => [],
        'refunded' => [],
    ];

    public const LABELS = [
        'pending' => 'Pesanan dibuat',
        'confirmed' => 'Pesanan dikonfirmasi',
        'processing' => 'Pesanan diproses',
        'packed' => 'Pesanan dikemas',
        'shipped' => 'Pesanan dikirim',
        'completed' => 'Pesanan selesai',
        'cancelled' => 'Pesanan dibatalkan',
        'refunded' => 'Dana dikembalikan',
    ];

    public function __construct(
        private readonly InventoryService $inventory,
        private readonly CouponService $coupons,
    ) {}

    public function allowedTransitions(Order $order): array
    {
        return self::TRANSITIONS[$order->status] ?? [];
    }

    public function updateStatus(Order $order, string $status, ?string $note = null, ?int $userId = null): Order
    {
        if ($status === $order->status) {
            return $order;
        }

        if (! in_array($status, $this->allowedTransitions($order), true)) {
            throw ValidationException::withMessages(['status' => "Status tidak dapat diubah dari {$order->statusLabel()} ke ".(Order::STATUSES[$status] ?? $status).'.']);
        }

        return DB::transaction(function () use ($order, $status, $note, $userId) {
            $previous = $order->status;
            $shippedBefore = in_array($previous, ['shipped', 'completed'], true);
            $updates = ['status' => $status];

            switch ($status) {
                case 'shipped':
                    $this->inventory->commit($order, $userId);
                    $updates += ['shipping_status' => 'shipped', 'shipped_at' => now()];
                    $order->shipment?->update(['status' => 'shipped', 'shipped_at' => now()]);
                    break;
                case 'completed':
                    $updates += ['shipping_status' => 'delivered', 'completed_at' => now()];
                    $order->shipment?->update(['status' => 'delivered', 'delivered_at' => now()]);
                    if ($order->payment_status === 'pending' && $order->payment?->method === 'cod') {
                        $this->markPaid($order, 'Pembayaran COD diterima', $userId);
                    }
                    break;
                case 'cancelled':
                    $shippedBefore ? $this->inventory->restock($order, $userId) : $this->inventory->release($order, $userId);
                    $this->coupons->revoke($order);
                    $updates += ['cancelled_at' => now()];
                    break;
                case 'refunded':
                    $this->inventory->restock($order, $userId);
                    $updates += ['payment_status' => $order->payment_status === 'paid' ? 'refunded' : $order->payment_status, 'shipping_status' => 'returned'];
                    $order->payment?->update(['status' => 'refunded']);
                    break;
            }

            $order->update($updates);
            $order->histories()->create(['type' => 'order', 'status' => $status, 'note' => $note ?: self::LABELS[$status] ?? null, 'user_id' => $userId]);

            Activity::log('shop.order_status', "Order {$order->order_number}: {$previous} → {$status}", $order, [], $userId);
            $order->customer?->notify(new OrderStatusUpdatedNotification($order));

            return $order->fresh(['items', 'histories', 'payment', 'shipment']);
        });
    }

    public function markPaid(Order $order, ?string $note = null, ?int $userId = null): Order
    {
        if ($order->payment_status === 'paid') {
            return $order;
        }

        $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
        $order->payment?->update(['status' => 'paid', 'paid_at' => now()]);
        $order->histories()->create(['type' => 'payment', 'status' => 'paid', 'note' => $note ?: 'Pembayaran diterima', 'user_id' => $userId]);

        if ($order->status === 'pending') {
            $this->updateStatus($order, 'confirmed', 'Dikonfirmasi otomatis setelah pembayaran', $userId);
        }

        return $order->fresh();
    }

    public function setTracking(Order $order, ?string $courier, string $trackingNumber, ?int $userId = null): Order
    {
        $shipment = $order->shipment ?? $order->shipment()->create(['provider' => 'manual', 'method' => $order->shipping_method ?? 'Manual', 'cost' => $order->shipping_cost]);
        $shipment->update(['courier' => $courier, 'tracking_number' => $trackingNumber]);
        $order->histories()->create(['type' => 'shipping', 'status' => 'tracking', 'note' => trim(($courier ? $courier.' — ' : '').'Resi '.$trackingNumber), 'user_id' => $userId]);

        return $order->fresh(['shipment']);
    }

    /**
     * Timeline for display: [label, done, at].
     */
    public function timeline(Order $order): array
    {
        $histories = $order->histories;
        $at = fn (string $status, string $type = 'order') => $histories->where('type', $type)->firstWhere('status', $status)?->created_at;

        if (in_array($order->status, ['cancelled', 'refunded'], true)) {
            return [
                ['label' => 'Pesanan dibuat', 'done' => true, 'at' => $order->created_at],
                ['label' => self::LABELS[$order->status], 'done' => true, 'at' => $at($order->status), 'danger' => true],
            ];
        }

        $rank = array_search($order->status, ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'completed'], true);

        return [
            ['label' => 'Pesanan dibuat', 'done' => true, 'at' => $order->created_at],
            ['label' => 'Pembayaran diterima', 'done' => $order->payment_status === 'paid', 'at' => $order->paid_at],
            ['label' => 'Diproses', 'done' => $rank >= 2, 'at' => $at('processing') ?? $at('packed')],
            ['label' => 'Dikirim', 'done' => $rank >= 4, 'at' => $order->shipped_at],
            ['label' => 'Selesai', 'done' => $rank >= 5, 'at' => $order->completed_at],
        ];
    }
}
