{{-- Gallery: Full-bleed — edge-to-edge mosaic with hairline gaps and titles revealed on hover. --}}
@php
    $all = $company->gallery->values();
    $n = $all->count();
    $mosaic = $n >= 5;
    $items = $mosaic ? $all->take(min(9, 1 + intdiv($n - 1, 4) * 4)) : $all;
    $cols = [1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4'][$items->count()] ?? 'md:grid-cols-4';
@endphp
<section id="gallery" class="{{ $ds->section($tone, '!pb-0') }}" x-data="lightbox">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Galeri', 'title' => $section->title ?: 'Galeri Perusahaan', 'subtitle' => $section->subtitle, 'number' => $index])
    </div>
    <div class="mt-12 grid grid-flow-dense grid-cols-2 gap-px bg-line {{ $mosaic ? 'md:grid-cols-4' : $cols }}" {!! $ds->reveal(1) !!}>
        @foreach ($items as $item)
            <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative aspect-square overflow-hidden bg-surface-alt {{ $mosaic && $loop->first ? 'col-span-2 row-span-2' : '' }} {{ ! $mosaic && $items->count() === 1 ? 'col-span-2 aspect-[21/9] md:col-span-1' : '' }}" aria-label="Lihat {{ $item->title ?: 'foto' }}">
                <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover transition duration-1000 group-hover:scale-105">
                <span class="absolute inset-0 flex flex-col items-start justify-end bg-black/45 p-4 text-left opacity-0 transition duration-500 group-hover:opacity-100 group-focus-visible:opacity-100 sm:p-6">
                    @if ($item->category)<span class="text-[0.65rem] font-semibold tracking-[0.22em] text-white/70 uppercase">{{ $item->category }}</span>@endif
                    <span class="heading mt-1 text-lg text-white {{ $mosaic && $loop->first ? 'sm:text-3xl' : 'sm:text-xl' }}">{{ $item->title ?: $company->name }}</span>
                </span>
            </button>
        @endforeach
    </div>
    @include('components.company.partials.lightbox')
</section>
