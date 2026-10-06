{{-- Status badge for shop records. Usage: @include('admin.shop.partials.badge', ['type' => 'order', 'value' => $order->status]) --}}
@php
    $maps = [
        'order' => [
            'pending' => 'badge-amber', 'confirmed' => 'badge-blue', 'processing' => 'badge-blue', 'packed' => 'badge-violet',
            'shipped' => 'badge-violet', 'completed' => 'badge-green', 'cancelled' => 'badge-red', 'refunded' => 'badge-slate',
        ],
        'payment' => ['pending' => 'badge-amber', 'paid' => 'badge-green', 'failed' => 'badge-red', 'refunded' => 'badge-slate'],
        'shipping' => ['pending' => 'badge-amber', 'ready' => 'badge-blue', 'shipped' => 'badge-violet', 'delivered' => 'badge-green', 'returned' => 'badge-red'],
        'product' => ['draft' => 'badge-slate', 'published' => 'badge-green', 'archived' => 'badge-amber'],
    ];
    $labels = [
        'order' => \App\Models\Shop\Order::STATUSES,
        'payment' => \App\Models\Shop\Order::PAYMENT_STATUSES,
        'shipping' => \App\Models\Shop\Order::SHIPPING_STATUSES,
    ];
    $value = (string) $value;
    $class = $maps[$type][$value] ?? 'badge-slate';
    $label = $labels[$type][$value] ?? ucfirst(str_replace('_', ' ', $value));
@endphp
<span class="badge {{ $class }}"><span class="size-1.5 rounded-full bg-current"></span>{{ $label }}</span>
