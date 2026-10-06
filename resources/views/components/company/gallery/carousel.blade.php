{{-- Gallery: Carousel — large single-image stage with crossfade, arrows, counter and a thumbnail rail. --}}
@php($items = $company->gallery->values())
<section id="gallery" class="{{ $ds->section($tone) }}" x-data="lightbox">
    <div class="{{ $ds->container() }}"
        x-data="{ i: 0, n: {{ max(1, $items->count()) }}, go(v) { this.i = (v + this.n) % this.n; this.$refs['t' + this.i]?.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' }) } }"
        @keydown.left="go(i - 1)" @keydown.right="go(i + 1)">
        @include('components.company.partials.heading', ['eyebrow' => 'Galeri', 'title' => $section->title ?: 'Galeri Perusahaan', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="relative mt-12 overflow-hidden bg-surface-alt {{ $ds->img() }}" {!! $ds->reveal(1) !!}>
            <div class="relative aspect-[4/3] sm:aspect-[16/9] lg:aspect-[21/9]">
                @foreach ($items as $item)
                    <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="absolute inset-0 transition-opacity duration-700"
                        :class="i === {{ $loop->index }} ? 'opacity-100' : 'pointer-events-none opacity-0'" @unless ($loop->first) x-cloak @endunless
                        :tabindex="i === {{ $loop->index }} ? 0 : -1" aria-label="Perbesar {{ $item->title ?: 'foto' }}">
                        <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" @unless ($loop->first) loading="lazy" @endunless decoding="async" class="size-full object-cover">
                    </button>
                @endforeach
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent"></div>
            </div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-4 sm:p-6">
                <div class="min-w-0 text-white">
                    <p class="font-mono text-xs text-white/70"><span x-text="String(i + 1).padStart(2, '0')">01</span> / {{ str_pad($items->count(), 2, '0', STR_PAD_LEFT) }}</p>
                    @foreach ($items as $item)
                        <p class="heading mt-1 truncate text-lg sm:text-2xl" x-show="i === {{ $loop->index }}" @unless ($loop->first) x-cloak @endunless>{{ $item->title ?: $company->name }}</p>
                    @endforeach
                </div>
                @if ($items->count() > 1)
                    <div class="pointer-events-auto flex shrink-0 gap-2">
                        <button type="button" @click="go(i - 1)" class="inline-flex size-11 items-center justify-center rounded-full bg-white/90 text-[#0f172a] backdrop-blur transition hover:bg-white" aria-label="Foto sebelumnya"><x-icon name="arrow-left" class="size-5" /></button>
                        <button type="button" @click="go(i + 1)" class="inline-flex size-11 items-center justify-center rounded-full bg-white/90 text-[#0f172a] backdrop-blur transition hover:bg-white" aria-label="Foto berikutnya"><x-icon name="arrow-right" class="size-5" /></button>
                    </div>
                @endif
            </div>
        </div>

        @if ($items->count() > 1)
            <div class="mt-3 flex snap-x gap-2 overflow-x-auto p-1 pb-2 [scrollbar-width:none] sm:gap-3 [&::-webkit-scrollbar]:hidden" {!! $ds->reveal(2) !!}>
                @foreach ($items as $item)
                    <button type="button" x-ref="t{{ $loop->index }}" @click="go({{ $loop->index }})" class="relative w-24 shrink-0 snap-start overflow-hidden rounded-brand outline-2 outline-offset-2 transition sm:w-32"
                        :class="i === {{ $loop->index }} ? 'opacity-100 outline-primary' : 'opacity-50 outline-transparent hover:opacity-80'" aria-label="Tampilkan {{ $item->title ?: 'foto '.$loop->iteration }}">
                        <img src="{{ $item->url('image') }}" alt="" loading="lazy" decoding="async" class="aspect-[4/3] w-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
    @include('components.company.partials.lightbox')
</section>
