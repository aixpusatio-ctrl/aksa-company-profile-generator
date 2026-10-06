{{-- Product card: Luxury — tall image that swaps to the second photo on hover, centered serif text. --}}
@php($url = $site->shop('product/'.$product->slug))
@php($second = $product->secondImage())
<article class="group relative flex h-full flex-col text-center">
    <div class="relative overflow-hidden bg-surface-alt">
        <a href="{{ $url }}" class="relative block aspect-[3/4]">
            <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="absolute inset-0 size-full object-cover transition duration-700 {{ $second ? 'group-hover:opacity-0' : 'group-hover:scale-105' }} {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
            @if ($second)
                <img src="{{ $second }}" alt="" loading="lazy" class="absolute inset-0 size-full scale-105 object-cover opacity-0 transition duration-700 group-hover:scale-100 group-hover:opacity-100">
            @endif
        </a>
        @include('websites.shop.partials.badges', ['badgeClass' => 'pointer-events-none absolute top-3 left-3 z-10 flex flex-col items-start gap-1'])
        @include('websites.shop.partials.wishlist-button', ['wishWrap' => 'absolute top-3 right-3 z-10', 'wishClass' => 'inline-flex size-9 items-center justify-center text-white drop-shadow transition hover:scale-110'])
        @include('websites.shop.partials.quick-add', ['qaWrap' => 'absolute inset-x-0 bottom-0 z-10 translate-y-full transition duration-500 group-hover:translate-y-0 group-focus-within:translate-y-0 max-lg:translate-y-0', 'qaClass' => 'flex w-full items-center justify-center gap-2 bg-neutral-950/85 py-3 text-[11px] font-medium tracking-[0.25em] text-white uppercase backdrop-blur hover:bg-neutral-950'])
    </div>
    <div class="flex flex-1 flex-col items-center px-2 pt-5">
        @if ($product->brand)
            <p class="text-[10px] tracking-[0.3em] text-muted uppercase">{{ $product->brand }}</p>
        @endif
        <h3 class="mt-1.5 font-heading text-base leading-snug text-ink sm:text-lg"><a href="{{ $url }}" class="hover:text-primary">{{ $product->name }}</a></h3>
        <div class="mt-2">@include('websites.shop.partials.price', ['priceClass' => 'flex flex-wrap items-baseline justify-center gap-x-2 tracking-wide'])</div>
        <div class="mt-1.5">@include('websites.shop.partials.rating')</div>
    </div>
</article>
