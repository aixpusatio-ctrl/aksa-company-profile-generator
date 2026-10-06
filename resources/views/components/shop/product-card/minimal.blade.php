{{-- Product card: Minimal — borderless image, quiet text underneath, add on hover. --}}
@php($url = $site->shop('product/'.$product->slug))
<article class="group relative flex h-full flex-col">
    <div class="relative overflow-hidden {{ $ds->img() }} bg-surface-alt">
        <a href="{{ $url }}" class="block aspect-[4/5]">
            <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover transition duration-700 group-hover:scale-[1.03] {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
        </a>
        @include('websites.shop.partials.badges', ['badgeMax' => 0])
        @include('websites.shop.partials.wishlist-button', ['wishClass' => 'inline-flex size-8 items-center justify-center rounded-full bg-white/80 text-neutral-900 backdrop-blur transition hover:text-rose-500'])
        @include('websites.shop.partials.quick-add', ['qaWrap' => 'absolute inset-x-2 bottom-2 z-10 translate-y-2 opacity-0 transition duration-300 group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:translate-y-0 group-focus-within:opacity-100 max-lg:translate-y-0 max-lg:opacity-100 max-lg:inset-x-auto max-lg:right-2', 'qaStyle' => 'icon', 'qaClass' => 'inline-flex h-9 w-full min-w-9 items-center justify-center gap-1 rounded-btn bg-white/95 px-2 text-xs font-semibold text-neutral-900 shadow backdrop-blur transition hover:bg-white'])
    </div>
    <div class="mt-3 flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate text-sm text-ink"><a href="{{ $url }}" class="hover:underline hover:underline-offset-4">{{ $product->name }}</a></h3>
            @if ($product->brand)
                <p class="truncate text-xs text-muted">{{ $product->brand }}</p>
            @endif
        </div>
        @include('websites.shop.partials.price', ['priceSize' => 'sm', 'priceClass' => 'flex shrink-0 flex-col items-end'])
    </div>
    @include('websites.shop.partials.rating')
</article>
