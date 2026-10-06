<?php

namespace App\Notifications\Shop;

use App\Models\Shop\Order;
use App\Support\Shop\Money;
use Illuminate\Notifications\Notification;

/**
 * Seller notification (dashboard bell) for a new order.
 */
class NewOrderNotification extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'banknotes',
            'title' => 'Pesanan baru #'.$this->order->order_number,
            'message' => $this->order->customer_name.' · '.Money::format($this->order->total, $this->order->currency),
            'url' => central_url(route('websites.shop.orders.show', [$this->order->company_profile_id, $this->order], absolute: false)),
        ];
    }
}
