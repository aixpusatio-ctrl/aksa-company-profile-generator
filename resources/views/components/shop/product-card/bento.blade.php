{{-- Product card: Bento — large rounded tile on a tinted surface, badges and a pill CTA. --}}
@php($url = $site->shop('product/'.$product->slug))
<article class="group relative flex h-full flex-col overflow-hidden rounded-[calc(var(--brand-radius)*2+0.5rem)] bg-surface-alt p-2 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-black/5">
    <div class="relative overflow-hidden rounded-[calc(var(--brand-radius)*2)] bg-card">
        <a href="{{ $url }}" class="block aspect-square">
            <x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover transition duration-500 group-hover:scale-105 {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" />
        </a>
        @include('websites.shop.partials.badges', ['badgeMax' => 2, 'badgeClass' => 'pointer-events-none absolute top-3 left-3 z-10 flex flex-wrap gap-1'])
        @include('websites.shop.partials.wishlist-button', ['wishWrap' => 'absolute top-3 right-3 z-10'])
    </div>
    <div class="flex flex-1 flex-col px-2.5 pt-3 pb-2">
        <div class="flex items-center justify-between gap-2">
            @if ($product->category)
                <span class="truncate rounded-full bg-card px-2.5 py-0.5 text-[11px] font-medium text-muted">{{ $product->category->name }}</span>
            @endif
            @include('websites.shop.partials.rating')
        </div>
        <h3 class="mt-2 line-clamp-2 font-heading text-[0.95rem] leading-snug font-bold text-ink sm:text-base"><a href="{{ $url }}">{{ $product->name }}</a></h3>
        <div class="mt-auto flex items-end justify-between gap-2 pt-3">
            @include('websites.shop.partials.price', ['priceClass' => 'flex min-w-0 flex-col'])
            @include('websites.shop.partials.quick-add', ['qaStyle' => 'icon', 'qaClass' => 'inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-ink text-surface transition group-hover:bg-primary group-hover:text-on-primary'])
        </div>
    </div>
</article>
