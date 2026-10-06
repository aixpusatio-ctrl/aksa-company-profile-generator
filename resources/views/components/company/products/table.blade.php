{{-- Products: Table — specification / price table (product, category, description, price); rows collapse into cards on mobile. --}}
<section id="products" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Produk & Harga', 'title' => $section->title ?: 'Daftar Produk', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <div class="shrink-0" {!! $ds->reveal(1) !!}>@include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Minta penawaran resmi', 'kind' => 'secondary'])</div>
        </div>

        <div class="mt-12 {{ $ds->card('overflow-hidden', false) }}" role="table" aria-label="Daftar produk dan harga" {!! $ds->reveal(1) !!}>
            <div role="row" class="hidden grid-cols-[minmax(0,2.3fr)_minmax(0,1fr)_minmax(0,2.6fr)_minmax(0,1.1fr)] gap-6 border-b border-line bg-surface-alt px-6 py-4 text-[11px] font-semibold tracking-[0.16em] text-muted uppercase md:grid">
                <span role="columnheader">Produk</span>
                <span role="columnheader">Kategori</span>
                <span role="columnheader">Keterangan</span>
                <span role="columnheader" class="text-right">Harga</span>
            </div>
            <div class="divide-y divide-line">
                @foreach ($company->products as $product)
                    <div role="row" class="group grid gap-x-6 gap-y-3 px-5 py-5 transition hover:bg-primary/[0.04] sm:px-6 md:grid-cols-[minmax(0,2.3fr)_minmax(0,1fr)_minmax(0,2.6fr)_minmax(0,1.1fr)] md:items-center">
                        <div role="cell" class="flex min-w-0 items-center gap-4">
                            <div class="shrink-0 overflow-hidden rounded-brand">
                                <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="size-14 object-cover transition duration-500 group-hover:scale-110" />
                            </div>
                            <div class="min-w-0">
                                <h3 class="heading text-base leading-snug break-words sm:text-lg">{{ $product->name }}</h3>
                                @if ($product->category)<span class="mt-1 inline-block text-xs font-medium text-primary md:hidden">{{ $product->category }}</span>@endif
                            </div>
                        </div>
                        <div role="cell" class="hidden md:block">
                            @if ($product->category)
                                <span class="inline-flex rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">{{ $product->category }}</span>
                            @else
                                <span class="text-muted">&mdash;</span>
                            @endif
                        </div>
                        <p role="cell" class="line-clamp-3 text-sm leading-relaxed text-muted md:line-clamp-2">{{ $product->description ?: '—' }}</p>
                        <div role="cell" class="flex items-center justify-between gap-4 border-t border-dashed border-line pt-3 md:block md:border-0 md:pt-0 md:text-right">
                            <span class="text-xs tracking-[0.14em] text-muted uppercase md:hidden">Harga</span>
                            <span class="font-semibold whitespace-nowrap text-ink tabular-nums">{{ $product->formattedPrice() ?? 'Atas permintaan' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <p class="mt-4 text-xs text-muted" {!! $ds->reveal(2) !!}>Harga dapat berubah sewaktu-waktu. <a href="{{ $site->anchor('contact') }}" class="font-medium text-primary underline-offset-4 hover:underline">Hubungi kami</a> untuk informasi terbaru.</p>
    </div>
</section>
