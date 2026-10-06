{{-- Product card: Image — full-bleed photo with text on a gradient overlay. --}}
@php($url = $site->shop('product/'.$product->slug))
<article class="group relative isolate flex aspect-[3/4] flex-col justify-end overflow-hidden {{ $ds->img() }} bg-neutral-900 text-white">
    <a href="{{ $url }}" class="absolute inset-0 -z-10" aria-label="{{ $product->name }}">
        <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover transition duration-700 group-hover:scale-105 {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
        <span class="absolute inset-0 bg-linear-to-t from-black/85 via-black/25 to-transparent"></span>
    </a>
    @include('websites.shop.partials.badges')
    @include('websites.shop.partials.wishlist-button')
    <div class="pointer-events-none p-3 sm:p-4">
        <h3 class="line-clamp-2 font-heading text-sm leading-snug font-bold text-white sm:text-lg"><a href="{{ $url }}" class="pointer-events-auto">{{ $product->name }}</a></h3>
        <div class="mt-1.5 flex items-end justify-between gap-2">
            <div class="min-w-0">
                @php($original = $product->originalPrice())
                <p class="text-sm font-bold text-white sm:text-base">{{ $product->formattedPrice() }}</p>
                @if ($original)
                    <p class="text-xs text-white/60 line-through">{{ \App\Support\Shop\Money::format($original) }}</p>
                @endif
                @if ($product->rating_count > 0)
                    <p class="mt-0.5 flex items-center gap-1 text-xs text-white/80"><x-icon name="star" stroke="0" class="size-3 text-amber-400" /> {{ number_format((float) $product->rating_avg, 1, ',', '.') }} ({{ $product->rating_count }})</p>
                @endif
            </div>
            @include('websites.shop.partials.quick-add', ['qaStyle' => 'icon', 'qaWrap' => 'pointer-events-auto shrink-0', 'qaClass' => 'inline-flex size-9 items-center justify-center rounded-full bg-white text-neutral-900 transition hover:scale-110 sm:size-10'])
        </div>
    </div>
</article>
