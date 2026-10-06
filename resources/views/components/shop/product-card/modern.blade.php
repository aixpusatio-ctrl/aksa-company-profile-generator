{{-- Product card: Modern — glassy rounded card with a floating add button over the image. --}}
@php($url = $site->shop('product/'.$product->slug))
<article class="group relative flex h-full flex-col rounded-[calc(var(--brand-radius)*1.75)] border border-line bg-card/70 p-2 shadow-[0_1px_2px_rgb(0_0_0/.04),0_18px_40px_-24px_rgb(0_0_0/.35)] backdrop-blur-xl transition duration-300 hover:-translate-y-1 hover:shadow-[0_2px_4px_rgb(0_0_0/.05),0_28px_56px_-24px_rgb(0_0_0/.45)]">
    <div class="relative">
        <a href="{{ $url }}" class="block aspect-[4/5] overflow-hidden rounded-[calc(var(--brand-radius)*1.25)] bg-surface-alt">
            <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover transition duration-500 group-hover:scale-105 {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
        </a>
        @include('websites.shop.partials.badges', ['badgeClass' => 'pointer-events-none absolute top-2.5 left-2.5 z-10 flex flex-col items-start gap-1'])
        @include('websites.shop.partials.wishlist-button', ['wishClass' => 'inline-flex size-9 items-center justify-center rounded-full bg-white/70 text-neutral-900 ring-1 ring-white/60 backdrop-blur-md transition hover:text-rose-500'])
        @include('websites.shop.partials.quick-add', ['qaStyle' => 'icon', 'qaWrap' => 'absolute -bottom-5 right-3 z-10', 'qaClass' => 'inline-flex size-11 items-center justify-center rounded-full bg-primary text-on-primary shadow-lg shadow-primary/40 ring-4 ring-card transition hover:scale-110'])
    </div>
    <div class="flex flex-1 flex-col px-2 pt-4 pb-2">
        @include('websites.shop.partials.rating')
        <h3 class="mt-1 line-clamp-2 pr-10 text-sm leading-snug font-semibold text-ink sm:text-[0.95rem]"><a href="{{ $url }}" class="hover:text-primary">{{ $product->name }}</a></h3>
        <div class="mt-auto pt-2">@include('websites.shop.partials.price')</div>
    </div>
</article>
