{{-- Products: List — elegant menu / price-list: name, dotted leader, price, description, grouped by category. --}}
@php
    $groups = $company->products->groupBy(fn ($p) => $p->category ?: 'Lainnya');
    $showGroupTitles = $groups->count() > 1 || $company->products->pluck('category')->filter()->isNotEmpty();
@endphp
<section id="products" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container('narrow') }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Daftar Produk', 'title' => $section->title ?: 'Pilihan Kami', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 grid gap-x-16 gap-y-14 {{ $groups->count() > 1 ? 'lg:grid-cols-2' : '' }}">
            @foreach ($groups as $category => $items)
                <div {!! $ds->reveal($loop->index) !!}>
                    @if ($showGroupTitles)
                        <div class="flex items-baseline gap-4 border-b border-ink/80 pb-3">
                            <h3 class="heading text-2xl">{{ $category }}</h3>
                            <span class="ml-auto font-mono text-xs text-muted">{{ str_pad($items->count(), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    @endif
                    <ul class="mt-2 divide-y divide-line/60">
                        @foreach ($items as $product)
                            <li class="group py-5">
                                <div class="flex items-baseline gap-3">
                                    <h4 class="heading min-w-0 text-lg leading-snug break-words transition group-hover:text-primary">{{ $product->name }}</h4>
                                    @if ($product->formattedPrice())
                                        <span class="min-w-6 flex-1 translate-y-[-4px] border-b-2 border-dotted border-muted/40" aria-hidden="true"></span>
                                        <span class="shrink-0 font-semibold whitespace-nowrap text-ink tabular-nums">{{ $product->formattedPrice() }}</span>
                                    @endif
                                </div>
                                @if ($product->description)
                                    <p class="mt-1.5 max-w-xl text-sm leading-relaxed text-muted">{{ $product->description }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-14 flex flex-col items-center gap-4 border-t border-line pt-10 text-center sm:flex-row sm:justify-between sm:text-left" {!! $ds->reveal(1) !!}>
            <p class="text-sm text-muted">Ingin memesan atau menanyakan ketersediaan?</p>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi kami', 'kind' => 'primary', 'class' => 'w-full sm:w-auto'])
        </div>
    </div>
</section>
