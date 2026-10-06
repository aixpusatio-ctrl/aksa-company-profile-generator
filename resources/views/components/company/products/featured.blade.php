{{-- Products: Featured — first product as a large spotlight block, remaining products as a compact list beside it. --}}
@php
    $products = $company->products;
    $hero = $products->first();
    $rest = $products->slice(1);
@endphp
<section id="products" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Produk', 'title' => $section->title ?: 'Produk Andalan Kami', 'subtitle' => $section->subtitle, 'number' => $index])

        @if ($hero)
            <div class="mt-14 grid gap-8 lg:grid-cols-12 lg:gap-10">
                <article class="{{ $ds->card('group overflow-hidden', false) }} {{ $rest->isEmpty() ? 'lg:col-span-12 lg:grid lg:grid-cols-2' : 'lg:col-span-7' }}" {!! $ds->reveal(0) !!}>
                    <div class="relative overflow-hidden">
                        <x-site.img :src="$hero->url('image')" :alt="$hero->name" icon="cube" class="aspect-[16/11] h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]" />
                        <span class="absolute top-5 left-5 inline-flex items-center gap-1.5 rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white backdrop-blur"><x-icon name="star" class="size-3.5" /> Unggulan</span>
                    </div>
                    <div class="flex flex-col p-7 sm:p-10">
                        @if ($hero->category)<p class="text-xs font-semibold tracking-[0.18em] text-primary uppercase">{{ $hero->category }}</p>@endif
                        <h3 class="heading mt-3 text-3xl break-words sm:text-4xl">{{ $hero->name }}</h3>
                        @if ($hero->description)<p class="mt-4 text-lead text-muted">{{ $hero->description }}</p>@endif
                        <div class="mt-8 flex flex-wrap items-center justify-between gap-5 border-t border-line pt-6">
                            @if ($hero->formattedPrice())
                                <p><span class="block text-xs tracking-[0.14em] text-muted uppercase">Mulai dari</span><span class="heading text-2xl">{{ $hero->formattedPrice() }}</span></p>
                            @endif
                            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Minta penawaran', 'kind' => 'primary', 'class' => 'w-full sm:w-auto'])
                        </div>
                    </div>
                </article>

                @if ($rest->isNotEmpty())
                    <div class="lg:col-span-5">
                        <p class="text-xs font-semibold tracking-[0.18em] text-muted uppercase" {!! $ds->reveal(1) !!}>Produk lainnya</p>
                        <ul class="mt-4 divide-y divide-line border-y border-line">
                            @foreach ($rest as $product)
                                <li class="group flex items-center gap-4 py-4 sm:gap-5" {!! $ds->reveal($loop->iteration) !!}>
                                    <div class="shrink-0 overflow-hidden {{ $ds->img() }}">
                                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="size-20 object-cover transition duration-500 group-hover:scale-110 sm:size-24" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        @if ($product->category)<p class="truncate text-[11px] font-semibold tracking-[0.14em] text-primary uppercase">{{ $product->category }}</p>@endif
                                        <h4 class="heading mt-0.5 text-lg leading-snug break-words">{{ $product->name }}</h4>
                                        @if ($product->description)<p class="mt-1 line-clamp-1 text-sm text-muted">{{ $product->description }}</p>@endif
                                        @if ($product->formattedPrice())<p class="mt-1 text-sm font-semibold text-ink sm:hidden">{{ $product->formattedPrice() }}</p>@endif
                                    </div>
                                    @if ($product->formattedPrice())
                                        <p class="hidden shrink-0 text-right text-sm font-semibold text-ink sm:block">{{ $product->formattedPrice() }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-6" {!! $ds->reveal(2) !!}>
                            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Konsultasikan kebutuhan Anda', 'kind' => 'link'])
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
