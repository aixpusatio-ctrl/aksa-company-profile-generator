@extends('websites.shop.layout')

@php
    $shopTitle = $shop->option('shop_title') ?: $shop->displayName();
    $shopSubtitle = $shop->option('shop_subtitle') ?: ($shop->description ?: 'Belanja produk pilihan '.$company->name.' dengan mudah dan aman.');
    $banner = \App\Support\MediaUrl::resolve($shop->option('banner_image'));
    $heroVariant = $ds->get('shop_hero');
    $sectionKeys = $shop->enabledSections();
    $productCount = $categoriesTree->sum('products_count') + $categoriesTree->flatMap->children->sum('products_count');
    $n = 0;
@endphp

@section('shop')
    @foreach ($sectionKeys as $key)
        @switch($key)
            @case('hero')
                @include('websites.shop.partials.hero.'.$heroVariant)
                @break

            @case('featured')
                @if ($featured->isNotEmpty())
                    <section class="py-12 sm:py-16">
                        <div class="{{ $ds->container() }}">
                            @include('websites.shop.partials.section-heading', ['eyebrow' => 'Pilihan', 'heading' => 'Produk Unggulan', 'more' => $site->shop('products'), 'n' => ++$n])
                            @include('websites.shop.partials.grid', ['products' => $featured])
                        </div>
                    </section>
                @endif
                @break

            @case('categories')
                @if ($categoriesTree->isNotEmpty())
                    <section class="bg-surface-alt py-12 sm:py-16">
                        <div class="{{ $ds->container() }}">
                            @include('websites.shop.partials.section-heading', ['eyebrow' => 'Kategori', 'heading' => 'Belanja per Kategori', 'more' => null, 'n' => ++$n])
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 {{ [3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4', 5 => 'lg:grid-cols-5'][min(5, max(3, $categoriesTree->count()))] }}">
                                @foreach ($categoriesTree as $cat)
                                    @php($catImage = $cat->url('image'))
                                    <a href="{{ $site->shop('category/'.$cat->slug) }}" class="group relative isolate flex aspect-[4/3] flex-col justify-end overflow-hidden {{ $ds->img() }} bg-secondary p-4 text-white" {!! $ds->reveal($loop->index) !!}>
                                        @if ($catImage)
                                            <img src="{{ $catImage }}" alt="" loading="lazy" class="absolute inset-0 -z-10 size-full object-cover transition duration-700 group-hover:scale-105">
                                        @else
                                            <span class="absolute inset-0 -z-10 bg-linear-to-br from-primary to-secondary"></span>
                                        @endif
                                        <span class="absolute inset-0 -z-10 bg-linear-to-t from-black/70 via-black/20 to-transparent"></span>
                                        <span class="font-heading text-base font-bold sm:text-lg">{{ $cat->name }}</span>
                                        <span class="mt-0.5 flex items-center gap-1 text-xs text-white/80">
                                            {{ $cat->products_count + $cat->children->sum('products_count') }} produk
                                            <x-icon name="arrow-right" class="size-3.5 transition group-hover:translate-x-1" />
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
                @break

            @case('best_sellers')
                @if ($bestSellers->isNotEmpty())
                    <section class="py-12 sm:py-16">
                        <div class="{{ $ds->container() }}">
                            @include('websites.shop.partials.section-heading', ['eyebrow' => 'Terlaris', 'heading' => 'Paling Banyak Dibeli', 'more' => $site->shop('products').'?sort=popular', 'n' => ++$n])
                            @include('websites.shop.partials.grid', ['products' => $bestSellers])
                        </div>
                    </section>
                @endif
                @break

            @case('new')
                @if ($newArrivals->isNotEmpty())
                    <section class="py-12 sm:py-16">
                        <div class="{{ $ds->container() }}">
                            @include('websites.shop.partials.section-heading', ['eyebrow' => 'Baru', 'heading' => 'Produk Terbaru', 'more' => $site->shop('products').'?sort=newest', 'n' => ++$n])
                            @include('websites.shop.partials.grid', ['products' => $newArrivals])
                        </div>
                    </section>
                @endif
                @break

            @case('sale')
                @if ($saleProducts->isNotEmpty())
                    <section class="relative overflow-hidden py-12 sm:py-16">
                        <div class="absolute inset-0 -z-0 bg-rose-500/[0.06]"></div>
                        <div class="{{ $ds->container() }} relative">
                            @include('websites.shop.partials.section-heading', ['eyebrow' => 'Promo', 'heading' => 'Sedang Diskon', 'more' => $site->shop('products').'?sale=1', 'moreLabel' => 'Semua promo', 'n' => ++$n])
                            @include('websites.shop.partials.grid', ['products' => $saleProducts])
                        </div>
                    </section>
                @endif
                @break

            @case('brands')
                @if ($brands->isNotEmpty())
                    <section class="border-y border-line py-10 sm:py-12">
                        <div class="{{ $ds->container() }}">
                            <p class="text-center text-xs font-semibold tracking-[0.2em] text-muted uppercase">Brand di toko kami</p>
                            <div class="mt-6 flex flex-wrap justify-center gap-2.5 sm:gap-3">
                                @foreach ($brands as $brand)
                                    <a href="{{ $site->shop('products').'?'.http_build_query(['brand' => [$brand]]) }}" class="rounded-full border border-line bg-card px-4 py-2 font-heading text-sm font-semibold text-ink transition hover:border-primary hover:text-primary sm:px-5 sm:text-base">{{ $brand }}</a>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
                @break

            @case('testimonials')
                @if ($testimonials->isNotEmpty())
                    <section class="bg-surface-alt py-12 sm:py-16">
                        <div class="{{ $ds->container() }}">
                            @include('websites.shop.partials.section-heading', ['eyebrow' => 'Testimoni', 'heading' => 'Kata Pelanggan Kami', 'more' => null, 'n' => ++$n])
                            <div class="shop-scroll -mx-5 flex snap-x gap-4 overflow-x-auto px-5 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:overflow-visible sm:px-0 lg:grid-cols-3">
                                @foreach ($testimonials->take(6) as $t)
                                    <figure class="{{ $ds->card('flex w-[82%] shrink-0 snap-start flex-col p-6 sm:w-auto', false) }}">
                                        <x-site.stars :rating="$t->rating ?? 5" class="text-ink" />
                                        <blockquote class="mt-4 flex-1 text-sm leading-relaxed text-ink">“{{ $t->testimonial }}”</blockquote>
                                        <figcaption class="mt-5 flex items-center gap-3 border-t border-line pt-4">
                                            <x-site.img :src="$t->url('photo')" :alt="$t->customer_name" icon="user" class="size-10 rounded-full object-cover" />
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-semibold text-ink">{{ $t->customer_name }}</span>
                                                <span class="block truncate text-xs text-muted">{{ $t->company }}</span>
                                            </span>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
                @break

            @case('newsletter')
                <section class="py-12 sm:py-16">
                    <div class="{{ $ds->container() }}">
                        <div class="relative overflow-hidden rounded-[calc(var(--brand-radius)*2)] bg-primary px-6 py-10 text-on-primary sm:px-12 sm:py-14">
                            <div class="pointer-events-none absolute -right-16 -bottom-24 size-72 rounded-full bg-white/10"></div>
                            <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                                <div class="max-w-xl">
                                    <h2 class="heading text-2xl sm:text-3xl">Jangan lewatkan promo berikutnya</h2>
                                    <p class="mt-2 text-sm text-on-primary/80 sm:text-base">Hubungi kami untuk info produk baru, restock dan penawaran khusus dari {{ $company->name }}.</p>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    @if ($company->whatsappUrl())
                                        <a href="{{ $company->whatsappUrl() }}?text={{ rawurlencode('Halo, saya ingin mendapatkan info promo dari '.$company->name.'.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-btn bg-white px-5 py-3 text-sm font-semibold text-neutral-900 transition hover:bg-white/90">
                                            <x-icon name="whatsapp" class="size-4" /> Info via WhatsApp
                                        </a>
                                    @endif
                                    <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn border border-on-primary/40 px-5 py-3 text-sm font-semibold transition hover:bg-white/10">Hubungi Kami</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @break
        @endswitch
    @endforeach

    @if (! in_array('hero', $sectionKeys, true) && $featured->isEmpty() && $newArrivals->isEmpty())
        <div class="{{ $ds->container() }} py-16">
            @include('websites.shop.partials.empty', ['emptyTitle' => 'Produk segera hadir', 'emptyText' => 'Toko ini sedang menyiapkan katalognya. Kunjungi lagi nanti ya!', 'actionUrl' => $site->home(), 'actionLabel' => 'Kembali ke beranda'])
        </div>
    @endif
@endsection
