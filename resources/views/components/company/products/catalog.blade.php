{{-- Products: Catalog — category tabs ("Semua" + categories) filtering a product card grid with prices and enquiry links. --}}
@php
    $products = $company->products;
    $categories = $products->pluck('category')->filter()->unique()->values();
@endphp
<section id="products" class="{{ $ds->section($tone) }}" x-data="{ tab: 'all' }">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Katalog Produk', 'title' => $section->title ?: 'Jelajahi Produk Kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <p class="shrink-0 text-sm text-muted" {!! $ds->reveal(1) !!}>
                <span class="font-semibold text-ink">{{ $products->count() }}</span> produk
                @if ($categories->isNotEmpty()) &middot; <span class="font-semibold text-ink">{{ $categories->count() }}</span> kategori @endif
            </p>
        </div>

        @if ($categories->count() > 1)
            <div class="-mx-5 mt-10 overflow-x-auto px-5 [scrollbar-width:none] sm:-mx-8 sm:px-8 [&::-webkit-scrollbar]:hidden" {!! $ds->reveal(2) !!}>
                <div class="flex w-max gap-1 rounded-btn border border-line bg-card p-1" role="tablist" aria-label="Kategori produk">
                    <button type="button" role="tab" @click="tab = 'all'" :aria-selected="tab === 'all'" class="min-h-11 rounded-btn px-5 text-sm font-medium whitespace-nowrap transition" :class="tab === 'all' ? 'bg-primary text-on-primary shadow-sm' : 'text-muted hover:text-ink'">Semua</button>
                    @foreach ($categories as $category)
                        <button type="button" role="tab" @click="tab = @js($category)" :aria-selected="tab === @js($category)" class="min-h-11 rounded-btn px-5 text-sm font-medium whitespace-nowrap transition" :class="tab === @js($category) ? 'bg-primary text-on-primary shadow-sm' : 'text-muted hover:text-ink'">{{ $category }}</button>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:gap-6">
            @foreach ($products as $product)
                <article x-show="tab === 'all' || tab === @js($product->category)" x-transition.opacity.duration.300ms class="{{ $ds->card('group flex flex-col overflow-hidden') }}" {!! $ds->reveal($loop->index) !!}>
                    <div class="relative overflow-hidden">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105" />
                        @if ($product->category)
                            <span class="absolute top-4 left-4 rounded-full bg-black/55 px-3 py-1 text-xs font-medium text-white backdrop-blur">{{ $product->category }}</span>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="heading text-h3 break-words">{{ $product->name }}</h3>
                        @if ($product->description)
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted">{{ $product->description }}</p>
                        @endif
                        <div class="mt-auto pt-6"><div class="flex items-end justify-between gap-4 border-t border-line pt-5">
                            <div class="min-w-0">
                                <p class="text-[11px] font-medium tracking-[0.14em] text-muted uppercase">Harga</p>
                                <p class="mt-0.5 font-semibold text-ink">{{ $product->formattedPrice() ?? 'Hubungi kami' }}</p>
                            </div>
                            <a href="{{ $site->anchor('contact') }}" class="inline-flex min-h-11 shrink-0 items-center gap-1.5 text-sm font-semibold text-primary transition hover:gap-2.5">
                                Tanya produk <x-icon name="arrow-right" class="size-4" />
                            </a>
                        </div></div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
