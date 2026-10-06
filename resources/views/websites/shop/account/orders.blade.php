@php($accountTitle = 'Pesanan Saya')
@extends('websites.shop.account.layout')

@section('account')
    @if ($orders->isEmpty())
        @include('websites.shop.partials.empty', ['icon' => 'receipt', 'emptyTitle' => 'Belum ada pesanan', 'emptyText' => 'Pesanan yang Anda buat akan muncul di sini.', 'actionUrl' => $site->shop('products')])
    @else
        <ul class="space-y-3">
            @foreach ($orders as $o)
                <li>
                    <a href="{{ $site->account('orders/'.$o->order_number) }}" class="{{ $ds->card('flex flex-wrap items-center justify-between gap-3 p-4 sm:p-5', false) }} border border-line transition hover:border-primary">
                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-heading font-bold text-ink">#{{ $o->order_number }}</span>
                                @include('websites.shop.partials.status-badge', ['status' => $o->status])
                                @if ($o->payment_status === 'paid')
                                    @include('websites.shop.partials.status-badge', ['status' => 'paid'])
                                @endif
                            </span>
                            <span class="mt-1 block text-xs text-muted">{{ $o->created_at->translatedFormat('d M Y, H:i') }} · {{ $o->items_count }} produk · {{ $o->shipping_method ?: '—' }}</span>
                        </span>
                        <span class="flex items-center gap-3">
                            <span class="font-heading text-lg font-bold text-ink">{{ \App\Support\Shop\Money::format($o->total, $o->currency) }}</span>
                            <x-icon name="chevron-right" class="size-4 text-muted" />
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
        @include('websites.shop.partials.pagination', ['paginator' => $orders])
    @endif
@endsection
