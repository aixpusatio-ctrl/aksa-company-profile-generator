{{-- Products: Carousel — horizontal scroll-snap rail of large product cards with prev/next arrows. --}}
@php
    // Rail spans the full section width; its inline padding lines the first card up with the container.
    $railPad = match ($ds->get('container')) {
        'wide' => '[--rail:1.25rem] sm:[--rail:2rem] lg:[--rail:3rem] min-[88rem]:[--rail:calc((100%-88rem)/2+3rem)]',
        'narrow' => '[--rail:1.25rem] sm:[--rail:2rem] min-[64rem]:[--rail:calc((100%-64rem)/2+2rem)]',
        'full' => '[--rail:1.25rem] sm:[--rail:2rem] lg:[--rail:3.5rem]',
        default => '[--rail:1.25rem] sm:[--rail:2rem] min-[80rem]:[--rail:calc((100%-80rem)/2+2rem)]',
    };
@endphp
<section id="products" class="{{ $ds->section($tone, 'overflow-hidden') }}"
    x-data="{ atStart: true, atEnd: false, update() { const r = this.$refs.rail; this.atStart = r.scrollLeft < 8; this.atEnd = r.scrollLeft + r.clientWidth >= r.scrollWidth - 8 }, go(dir) { const r = this.$refs.rail; r.scrollBy({ left: dir * r.clientWidth * 0.85, behavior: 'smooth' }) } }"
    x-init="$nextTick(() => update())">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Produk', 'title' => $section->title ?: 'Koleksi Produk Kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            @if ($company->products->count() > 1)
                <div class="flex shrink-0 gap-2" {!! $ds->reveal(1) !!}>
                    <button type="button" @click="go(-1)" :disabled="atStart" aria-label="Produk sebelumnya" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-30"><x-icon name="arrow-left" class="size-5" /></button>
                    <button type="button" @click="go(1)" :disabled="atEnd" aria-label="Produk berikutnya" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-30"><x-icon name="arrow-right" class="size-5" /></button>
                </div>
            @endif
        </div>

    </div>

        <div x-ref="rail" @scroll.debounce.60ms="update()" tabindex="0" role="region" aria-label="Daftar produk"
            class="{{ $railPad }} mt-12 flex snap-x snap-mandatory scroll-px-[var(--rail)] gap-5 overflow-x-auto px-[var(--rail)] pb-4 [scrollbar-width:none] focus:outline-none lg:gap-7 [&::-webkit-scrollbar]:hidden">
            @foreach ($company->products as $product)
                <article class="group w-[82%] shrink-0 snap-start sm:w-[46%] lg:w-[min(31%,25rem)]" {!! $ds->reveal($loop->index) !!}>
                    <div class="relative overflow-hidden {{ $ds->img() }}">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/5] w-full object-cover transition duration-700 group-hover:scale-105" />
                        @if ($product->formattedPrice())
                            <span class="absolute right-4 bottom-4 rounded-full bg-black/60 px-3.5 py-1.5 text-sm font-semibold text-white backdrop-blur">{{ $product->formattedPrice() }}</span>
                        @endif
                    </div>
                    <div class="mt-5 px-1">
                        @if ($product->category)<p class="text-xs font-semibold tracking-[0.16em] text-primary uppercase">{{ $product->category }}</p>@endif
                        <h3 class="heading mt-1.5 text-h3 break-words">{{ $product->name }}</h3>
                        @if ($product->description)<p class="mt-2 line-clamp-2 text-sm leading-relaxed text-muted">{{ $product->description }}</p>@endif
                    </div>
                </article>
            @endforeach
        </div>

    <div class="{{ $ds->container() }}">
        <div class="mt-8 flex justify-center md:justify-start" {!! $ds->reveal(2) !!}>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Tanyakan produk', 'kind' => 'secondary'])
        </div>
    </div>
</section>
