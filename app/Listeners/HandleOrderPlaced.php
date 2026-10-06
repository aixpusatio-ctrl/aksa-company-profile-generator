<?php

namespace App\Listeners;

use App\Events\Shop\OrderPlaced;
use App\Notifications\Shop\NewOrderNotification;
use App\Notifications\Shop\OrderStatusUpdatedNotification;
use App\Support\Activity;

class HandleOrderPlaced
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $company = $order->companyProfile;

        Activity::log('shop.order_placed', "Order {$order->order_number} di {$company?->name}", $order, ['total' => (float) $order->total], $company?->user_id);

        $company?->user?->notify(new NewOrderNotification($order));
        $order->customer?->notify(new OrderStatusUpdatedNotification($order));
    }
}
