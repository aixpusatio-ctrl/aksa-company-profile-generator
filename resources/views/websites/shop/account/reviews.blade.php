@php($accountTitle = 'Ulasan Saya')
@extends('websites.shop.account.layout')

@section('account')
    @if ($reviews->isEmpty())
        @include('websites.shop.partials.empty', ['icon' => 'star', 'emptyTitle' => 'Belum ada ulasan', 'emptyText' => 'Bagikan pengalaman Anda dari halaman produk yang sudah dibeli.', 'actionUrl' => $site->account('orders'), 'actionLabel' => 'Lihat pesanan'])
    @else
        <ul class="space-y-3">
            @foreach ($reviews as $review)
                <li class="{{ $ds->card('flex gap-4 p-4 sm:p-5', false) }} border border-line">
                    @if ($review->product)
                        <a href="{{ $site->shop('product/'.$review->product->slug) }}" class="size-16 shrink-0 overflow-hidden rounded-brand bg-surface-alt">
                            <x-site.img :src="$review->product->mainImage()" :alt="$review->product->name" icon="cube" class="size-full object-cover" />
                        </a>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            @if ($review->product)
                                <a href="{{ $site->shop('product/'.$review->product->slug) }}" class="truncate font-semibold text-ink hover:text-primary">{{ $review->product->name }}</a>
                            @else
                                <span class="text-muted">Produk tidak tersedia</span>
                            @endif
                            @include('websites.shop.partials.status-badge', ['status' => $review->status])
                        </div>
                        <div class="mt-1 flex items-center gap-2 text-xs text-muted"><x-site.stars :rating="$review->rating" class="text-ink" /> {{ $review->created_at->translatedFormat('d M Y') }}</div>
                        @if ($review->title)<p class="mt-2 text-sm font-semibold text-ink">{{ $review->title }}</p>@endif
                        @if ($review->body)<p class="mt-1 text-sm whitespace-pre-line text-muted">{{ $review->body }}</p>@endif
                    </div>
                </li>
            @endforeach
        </ul>
        @include('websites.shop.partials.pagination', ['paginator' => $reviews])
    @endif
@endsection
