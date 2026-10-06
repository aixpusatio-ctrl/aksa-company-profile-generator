{{-- Product card: Compact — dense marketplace-style tile for big catalogs. --}}
@php($url = $site->shop('product/'.$product->slug))
<article class="group relative flex h-full flex-col overflow-hidden rounded-md border border-line bg-card transition hover:border-primary/50 hover:shadow-md">
    <a href="{{ $url }}" class="relative block aspect-square overflow-hidden bg-surface-alt">
        <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
    </a>
    @include('websites.shop.partials.badges', ['badgeMax' => 0, 'badgeClass' => 'pointer-events-none absolute top-1.5 left-1.5 z-10 flex flex-col items-start gap-0.5'])
    @include('websites.shop.partials.wishlist-button', ['wishWrap' => 'absolute top-1.5 right-1.5 z-10', 'wishClass' => 'inline-flex size-7 items-center justify-center rounded-full bg-white/90 text-neutral-700 shadow-sm transition hover:text-rose-500'])
    <div class="flex flex-1 flex-col gap-1 p-2">
        <h3 class="line-clamp-2 text-xs leading-snug text-ink sm:text-[0.8rem]"><a href="{{ $url }}" class="hover:text-primary">{{ $product->name }}</a></h3>
        @include('websites.shop.partials.price', ['priceSize' => 'sm', 'priceClass' => 'flex flex-wrap items-baseline gap-x-1.5'])
        <div class="mt-auto flex items-center justify-between gap-1">
            @include('websites.shop.partials.rating')
            @if ($product->isInStock() && $shop->option('show_stock') && $product->isLowStock((int) ($shop->low_stock_threshold ?? 5)))
                <span class="text-[10px] font-semibold text-amber-600">Sisa {{ $product->availableStock() }}</span>
            @endif
        </div>
        @include('websites.shop.partials.quick-add', ['qaClass' => 'inline-flex w-full items-center justify-center gap-1 rounded-sm border border-primary py-1.5 text-[11px] font-semibold text-primary transition hover:bg-primary hover:text-on-primary'])
    </div>
</article>
