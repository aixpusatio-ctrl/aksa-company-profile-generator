<?php

namespace App\Notifications\Shop;

use App\Models\Shop\Order;
use App\Services\Shop\OrderService;
use App\Support\Shop\Money;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * E-mail to the customer when an order is placed or its status changes.
 */
class OrderStatusUpdatedNotification extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;
        $company = $order->companyProfile;
        $url = rtrim($company->publicUrl(), '/').'/shop/order/'.$order->order_number.'?token='.$order->access_token;

        return (new MailMessage)
            ->subject('['.$company->name.'] '.(OrderService::LABELS[$order->status] ?? 'Pesanan').' #'.$order->order_number)
            ->greeting('Halo '.$order->customer_name.',')
            ->line((OrderService::LABELS[$order->status] ?? 'Status pesanan diperbarui').' — #'.$order->order_number.'.')
            ->line('Total: '.Money::format($order->total, $order->currency))
            ->action('Lihat pesanan', $url)
            ->salutation('Terima kasih, '.$company->name);
    }
}
