{{-- Product card: Classic — framed image, name, rating, price and an add-to-cart button. --}}
@php($url = $site->shop('product/'.$product->slug))
<article class="{{ $ds->card('group relative flex h-full flex-col overflow-hidden') }}">
    <a href="{{ $url }}" class="relative block aspect-square overflow-hidden bg-surface-alt">
        <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover transition duration-500 group-hover:scale-105 {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
    </a>
    @include('websites.shop.partials.badges')
    @include('websites.shop.partials.wishlist-button')
    <div class="flex flex-1 flex-col gap-1.5 p-3 sm:p-4">
        @if ($product->category)
            <p class="truncate text-[11px] font-medium tracking-wide text-muted uppercase">{{ $product->category->name }}</p>
        @endif
        <h3 class="line-clamp-2 text-sm leading-snug font-semibold text-ink sm:text-[0.95rem]"><a href="{{ $url }}" class="hover:text-primary">{{ $product->name }}</a></h3>
        @include('websites.shop.partials.rating')
        <div class="mt-auto pt-1">@include('websites.shop.partials.price')</div>
        <div class="pt-2">@include('websites.shop.partials.quick-add')</div>
    </div>
</article>
