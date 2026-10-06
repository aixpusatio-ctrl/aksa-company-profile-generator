{{-- Hero: Product — featured product on a soft pedestal with name/category/price chips and a spec strip of products. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $featured = $company->products->first();
    $lineup = $company->products->take(3);
@endphp
<section id="hero" class="relative isolate overflow-hidden bg-surface">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-surface-alt to-surface" aria-hidden="true"></div>
    <div class="{{ $ds->container('wide') }} pt-28 pb-16 lg:pt-36 lg:pb-24">
        <div class="grid items-center gap-14 lg:grid-cols-12 lg:gap-10">
            <div class="lg:col-span-6">
                <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($featured?->category ?: 'Produk Unggulan') !!}</div>
                <h1 class="heading mt-6 text-[min(var(--display),4rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
                @if ($lead)
                    <p class="mt-6 max-w-xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
                @endif
                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                    @include('components.company.partials.button', ['href' => $site->anchor('products'), 'label' => 'Lihat Produk', 'kind' => 'primary'])
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Minta Penawaran', 'kind' => 'secondary'])
                </div>
            </div>

            <div class="relative lg:col-span-6" {!! $ds->reveal(2, 'right') !!}>
                <div class="relative mx-auto aspect-square w-full max-w-[36rem]">
                    <div class="absolute inset-[6%] rounded-full bg-gradient-to-br from-primary/20 via-primary/5 to-secondary/20"></div>
                    <div class="absolute inset-[16%] rounded-full border border-primary/15"></div>
                    <div class="absolute inset-x-[18%] bottom-[9%] h-[8%] rounded-[50%] bg-ink/15 blur-xl"></div>
                    <div class="absolute inset-[14%] flex items-center justify-center">
                        <div class="w-full animate-float">
                            <x-site.img :src="$featured?->url('image') ?: $company->url('hero_image')" :alt="$featured?->name ?: $company->name" icon="cube" class="{{ $ds->img('aspect-[4/3] w-full object-cover shadow-2xl shadow-black/25') }}" />
                        </div>
                    </div>
                    @if ($featured)
                        <div class="absolute top-[8%] left-0 max-w-[60%] rounded-full border border-line bg-card/90 px-4 py-2 shadow-lg backdrop-blur sm:left-[2%]">
                            <p class="truncate text-sm font-semibold text-ink">{{ $featured->name }}</p>
                        </div>
                        @if ($featured->category)
                            <span class="absolute top-[24%] right-0 rounded-full bg-primary px-3.5 py-1.5 text-xs font-semibold text-on-primary shadow-lg sm:right-[4%]">{{ $featured->category }}</span>
                        @endif
                        @if ($featured->formattedPrice())
                            <div class="absolute right-[2%] bottom-[6%] rounded-brand border border-line bg-card px-4 py-2.5 shadow-lg sm:right-[8%]">
                                <p class="text-[10px] font-semibold tracking-[0.18em] text-muted uppercase">Mulai dari</p>
                                <p class="heading text-lg text-ink">{{ $featured->formattedPrice() }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        @if ($lineup->isNotEmpty())
            <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:mt-16 lg:grid-cols-3">
                @foreach ($lineup as $product)
                    <a href="{{ $site->anchor('products') }}" class="{{ $ds->card('group flex items-center gap-4 p-3 pr-5') }}" {!! $ds->reveal($loop->index + 4) !!}>
                        <div class="size-20 shrink-0 overflow-hidden rounded-[calc(var(--brand-radius)*.8)] bg-surface-alt">
                            <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="size-full object-cover transition duration-500 group-hover:scale-110" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-mono text-[10px] tracking-[0.18em] text-muted uppercase">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}{{ $product->category ? ' · '.$product->category : '' }}</p>
                            <p class="mt-0.5 truncate font-semibold text-ink">{{ $product->name }}</p>
                            @if ($product->formattedPrice())<p class="text-sm text-primary">{{ $product->formattedPrice() }}</p>@endif
                        </div>
                        <x-icon name="arrow-up-right" class="size-4 shrink-0 text-muted transition group-hover:text-primary" />
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
