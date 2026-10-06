{{-- Product card: Horizontal — image left, details and actions right. --}}
@php($url = $site->shop('product/'.$product->slug))
<article class="{{ $ds->card('group relative flex h-full gap-3 overflow-hidden p-2.5 sm:gap-4 sm:p-3') }}">
    <div class="relative w-28 shrink-0 sm:w-36">
        <a href="{{ $url }}" class="block aspect-square overflow-hidden {{ $ds->img() }} bg-surface-alt">
            <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover transition duration-500 group-hover:scale-105 {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
        </a>
        @include('websites.shop.partials.badges', ['badgeMax' => 0, 'badgeClass' => 'pointer-events-none absolute top-1.5 left-1.5 z-10 flex flex-col items-start gap-1'])
    </div>
    <div class="flex min-w-0 flex-1 flex-col py-0.5">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                @if ($product->category)
                    <p class="truncate text-[11px] font-medium tracking-wide text-muted uppercase">{{ $product->category->name }}</p>
                @endif
                <h3 class="mt-0.5 line-clamp-2 text-sm leading-snug font-semibold text-ink sm:text-base"><a href="{{ $url }}" class="hover:text-primary">{{ $product->name }}</a></h3>
            </div>
            @include('websites.shop.partials.wishlist-button', ['wishWrap' => 'shrink-0', 'wishClass' => 'inline-flex size-8 items-center justify-center rounded-full border border-line text-muted transition hover:text-rose-500'])
        </div>
        @if ($product->short_description)
            <p class="mt-1 line-clamp-2 hidden text-xs text-muted sm:block">{{ $product->short_description }}</p>
        @endif
        <div class="mt-1">@include('websites.shop.partials.rating')</div>
        <div class="mt-auto flex flex-wrap items-end justify-between gap-2 pt-2">
            @include('websites.shop.partials.price')
            @include('websites.shop.partials.quick-add', ['qaClass' => $ds->btn('primary', '!px-3.5 !py-2 !text-xs'), 'qaWrap' => 'shrink-0'])
        </div>
    </div>
</article>
