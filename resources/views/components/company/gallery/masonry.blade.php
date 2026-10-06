{{-- Gallery: Masonry — CSS-columns masonry of mixed aspect ratios with captions on hover and a lightbox. --}}
@php($ratios = ['aspect-[4/5]', 'aspect-[4/3]', 'aspect-[3/4]', 'aspect-square', 'aspect-[2/3]', 'aspect-[5/4]'])
<section id="gallery" class="{{ $ds->section($tone) }}" x-data="lightbox">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Galeri', 'title' => $section->title ?: 'Galeri Perusahaan', 'subtitle' => $section->subtitle, 'number' => $index])
        <div class="mt-12 columns-2 gap-3 md:columns-3 md:gap-4 [&>*]:mb-3 md:[&>*]:mb-4">
            @foreach ($company->gallery as $item)
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative block w-full break-inside-avoid overflow-hidden {{ $ds->img() }}" aria-label="Lihat {{ $item->title ?: 'foto' }}" {!! $ds->reveal($loop->index) !!}>
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" decoding="async" class="{{ $ratios[$loop->index % count($ratios)] }} w-full object-cover transition duration-700 group-hover:scale-105">
                    <span class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/75 via-black/10 to-transparent p-4 text-left opacity-0 transition duration-500 group-hover:opacity-100 group-focus-visible:opacity-100 sm:p-5">
                        @if ($item->category)<span class="text-[0.65rem] font-semibold tracking-[0.2em] text-white/70 uppercase">{{ $item->category }}</span>@endif
                        @if ($item->title)<span class="mt-1 text-sm font-medium text-white sm:text-base">{{ $item->title }}</span>@endif
                    </span>
                </button>
            @endforeach
        </div>
    </div>
    @include('components.company.partials.lightbox')
</section>
