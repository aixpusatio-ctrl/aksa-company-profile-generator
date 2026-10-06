<?php

namespace App\Services\Shop\Payment;

use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Models\Shop\PaymentMethod;

/**
 * A payment provider creates a payment for an order and returns what the
 * customer needs to complete it (instructions, a redirect URL, ...).
 * Card data is never handled or stored by the application: gateway
 * providers (Midtrans, Xendit, ...) should redirect to the hosted page.
 */
interface PaymentProviderInterface
{
    public function key(): string;

    /** Method types this provider supports (e.g. bank_transfer, cod). */
    public function types(): array;

    public function initiate(Order $order, PaymentMethod $method): Payment;
}
