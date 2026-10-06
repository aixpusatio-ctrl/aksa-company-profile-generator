<?php

namespace App\Services\Shop\Payment;

use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Models\Shop\PaymentMethod;

/**
 * Manual bank transfer and cash on delivery. The seller confirms payment
 * from the dashboard (Orders → Mark as paid).
 */
class ManualPaymentProvider implements PaymentProviderInterface
{
    public function key(): string
    {
        return 'manual';
    }

    public function types(): array
    {
        return ['bank_transfer', 'cod'];
    }

    public function initiate(Order $order, PaymentMethod $method): Payment
    {
        $config = $method->config ?? [];

        return $order->payment()->create([
            'provider' => $this->key(),
            'method' => $method->type,
            'amount' => $order->total,
            'status' => 'pending',
            'instructions' => array_filter([
                'title' => $method->name,
                'text' => $method->instructions,
                'bank' => $config['bank'] ?? null,
                'account_number' => $config['account_number'] ?? null,
                'account_name' => $config['account_name'] ?? null,
                'amount' => (float) $order->total,
            ], fn ($v) => $v !== null && $v !== ''),
        ]);
    }
}
