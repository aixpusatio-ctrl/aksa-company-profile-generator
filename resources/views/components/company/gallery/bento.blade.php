{{-- Gallery: Bento — asymmetric bento grid with one large feature tile and a lightbox. --}}
@php
    $items = $company->gallery->take(8)->values();
    $n = $items->count();
    // Wide tiles at the end so the 4-column (md) and 2-column (mobile) grids are always filled without holes.
    $extra = $n > 2 ? (4 - ($n + 3) % 4) % 4 : 0;
@endphp
<section id="gallery" class="{{ $ds->section($tone) }}" x-data="lightbox">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Galeri', 'title' => $section->title ?: 'Galeri Perusahaan', 'subtitle' => $section->subtitle, 'number' => $index])
        <div class="mt-12 grid grid-flow-dense auto-rows-[9.5rem] grid-cols-2 gap-3 sm:auto-rows-[12rem] md:grid-cols-4 md:gap-4 lg:auto-rows-[14rem]">
            @foreach ($items as $item)
                @php
                    $mdWide = $loop->index >= $n - $extra;
                    $mobileWide = $loop->last && $n % 2 === 0;
                    $span = match (true) {
                        $loop->first, $n === 2 => 'col-span-2 row-span-2',
                        $mdWide && $mobileWide => 'col-span-2',
                        $mdWide => 'md:col-span-2',
                        $mobileWide => 'col-span-2 md:col-span-1',
                        default => '',
                    };
                @endphp
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative overflow-hidden {{ $ds->img() }} {{ $span }}" aria-label="Lihat {{ $item->title ?: 'foto' }}" {!! $ds->reveal($loop->index) !!}>
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover transition duration-700 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/0 to-transparent {{ $loop->first ? '' : 'opacity-0 transition group-hover:opacity-100' }}"></span>
                    @if ($item->title)
                        <span class="absolute inset-x-0 bottom-0 p-4 text-left text-white sm:p-5 {{ $loop->first ? '' : 'translate-y-2 opacity-0 transition duration-500 group-hover:translate-y-0 group-hover:opacity-100' }}">
                            @if ($loop->first && $item->category)<span class="block text-[0.65rem] font-semibold tracking-[0.2em] text-white/70 uppercase">{{ $item->category }}</span>@endif
                            <span class="block font-medium {{ $loop->first ? 'heading mt-1 text-xl sm:text-2xl' : 'text-sm' }}">{{ $item->title }}</span>
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    @include('components.company.partials.lightbox')
</section>
