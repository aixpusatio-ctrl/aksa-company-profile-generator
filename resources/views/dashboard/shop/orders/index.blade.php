@php
    use App\Support\Shop\Money;

    $filters = [
        '' => ['Semua', $counts->sum()],
        'pending' => ['Pending', $counts['pending'] ?? 0],
        'processing' => ['Diproses', $counts['processing'] ?? 0],
        'shipped' => ['Dikirim', $counts['shipped'] ?? 0],
        'completed' => ['Selesai', $counts['completed'] ?? 0],
        'cancelled' => ['Dibatalkan', $counts['cancelled'] ?? 0],
        'paid' => ['Lunas', null],
    ];
@endphp
<x-website-layout :company="$company" title="Pesanan">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="-mx-1 flex gap-1 overflow-x-auto px-1">
                @foreach ($filters as $key => [$label, $count])
                    <a href="{{ route('websites.shop.orders.index', array_filter([$company, 'filter' => $key, 'q' => $search])) }}"
                       class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap {{ $filter === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $label }} @if ($count !== null)<span class="rounded-full px-1.5 text-[11px] {{ $filter === $key ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}">{{ $count }}</span>@endif
                    </a>
                @endforeach
            </div>
            <form method="GET" class="flex gap-2">
                @if ($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
                <div class="relative flex-1 lg:w-64">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="No. order, nama, email…" class="form-input pl-9">
                </div>
                <button class="btn btn-secondary">Cari</button>
            </form>
        </div>

        @if ($orders->isEmpty())
            <div class="p-6"><x-empty-state title="Belum ada pesanan" description="Pesanan dari halaman checkout dan WhatsApp akan muncul di sini." icon="receipt" /></div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Order</th><th>Pelanggan</th><th>Item</th><th>Total</th><th>Status</th><th>Pembayaran</th><th>Pengiriman</th><th></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <a href="{{ route('websites.shop.orders.show', [$company, $order]) }}" class="font-semibold text-brand-600 hover:underline">{{ $order->order_number }}</a>
                                    <p class="text-xs text-slate-400">{{ $order->created_at->format('d M Y H:i') }} @if ($order->channel === 'whatsapp') · WA @endif</p>
                                </td>
                                <td class="max-w-[14rem]">
                                    <p class="truncate font-medium text-slate-800">{{ $order->customer_name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $order->customer_email ?: $order->customer_phone }}</p>
                                </td>
                                <td class="text-slate-500">{{ $order->items_count }}</td>
                                <td class="whitespace-nowrap font-semibold text-slate-900">{{ Money::format($order->total, $order->currency) }}</td>
                                <td><x-status-badge :status="$order->status" /></td>
                                <td><x-status-badge :status="$order->payment_status" /><p class="mt-0.5 text-[11px] text-slate-400">{{ $order->payment_method }}</p></td>
                                <td class="text-xs whitespace-nowrap text-slate-500">{{ $order->shipping_method ?: '—' }}</td>
                                <td class="text-right"><a href="{{ route('websites.shop.orders.show', [$company, $order]) }}" class="btn btn-secondary btn-sm">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $orders->links() }}</div>
            @endif
        @endif
    </div>
</x-website-layout>
