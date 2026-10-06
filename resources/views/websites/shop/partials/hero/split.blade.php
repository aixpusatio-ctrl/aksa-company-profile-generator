{{-- Shop hero: Split — copy left, image collage right. --}}
@php($heroProducts = $featured->concat($newArrivals)->unique('id')->filter(fn ($p) => $p->mainImage())->take(2)->values())
<section class="relative overflow-hidden py-10 sm:py-16">
    <div class="pointer-events-none absolute -top-32 -left-32 size-96 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="{{ $ds->container() }} relative grid items-center gap-10 lg:grid-cols-2">
        <div>
            {!! $ds->eyebrow($shop->displayName()) !!}
            <h1 class="heading mt-4 text-display text-ink">{{ $shopTitle }}</h1>
            <p class="mt-5 max-w-xl text-lead text-muted">{{ $shopSubtitle }}</p>
            @include('websites.shop.partials.hero._actions')
            @if ($productCount)
                <dl class="mt-8 flex gap-8 text-sm">
                    <div><dt class="text-muted">Produk</dt><dd class="font-heading text-2xl font-bold text-ink">{{ $productCount }}+</dd></div>
                    <div><dt class="text-muted">Kategori</dt><dd class="font-heading text-2xl font-bold text-ink">{{ $categoriesTree->count() }}</dd></div>
                </dl>
            @endif
        </div>
        <div class="relative grid grid-cols-5 gap-3 sm:gap-4">
            <div class="col-span-3 aspect-[4/5] overflow-hidden {{ $ds->img() }} bg-surface-alt">
                <x-site.img :src="$banner ?? $heroProducts->get(0)?->mainImage()" alt="" class="size-full object-cover" />
            </div>
            <div class="col-span-2 flex flex-col gap-3 pt-10 sm:gap-4">
                @foreach ($heroProducts as $hp)
                    <a href="{{ $site->shop('product/'.$hp->slug) }}" class="group relative block aspect-square overflow-hidden {{ $ds->img() }} bg-surface-alt">
                        <img src="{{ $hp->mainImage() }}" alt="{{ $hp->name }}" class="size-full object-cover transition duration-500 group-hover:scale-105">
                        <span class="absolute inset-x-2 bottom-2 truncate rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-neutral-900 backdrop-blur">{{ $hp->formattedPrice() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="{{ $ds->container() }}">@include('websites.shop.partials.hero._perks')</div>
</section>
