{{-- Gallery: Grid — uniform grid with lightbox. --}}
<section id="gallery" class="{{ $ds->section($tone) }}" x-data="lightbox">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Galeri', 'title' => $section->title ?: 'Galeri Perusahaan', 'subtitle' => $section->subtitle, 'number' => $index])
        <div class="mt-12 grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4">
            @foreach ($company->gallery as $item)
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative overflow-hidden {{ $ds->img() }}" {!! $ds->reveal($loop->index) !!}>
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105">
                    @if ($item->title)<span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4 text-left text-sm font-medium text-white opacity-0 transition group-hover:opacity-100">{{ $item->title }}</span>@endif
                </button>
            @endforeach
        </div>
    </div>
    @include('components.company.partials.lightbox')
</section>
