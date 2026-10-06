{{-- Gallery: Strip — horizontal snap strip of tall images bleeding to the right edge, with titles and arrows. --}}
<section id="gallery" class="{{ $ds->section($tone, 'overflow-hidden') }}" x-data="lightbox">
    <div class="{{ $ds->container() }}" x-data="{ go(dir) { const t = $refs.strip; t.scrollBy({ left: dir * Math.max(t.clientWidth * 0.6, 260), behavior: 'smooth' }) } }">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            @include('components.company.partials.heading', ['eyebrow' => 'Galeri', 'title' => $section->title ?: 'Di Balik Layar', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            @if ($company->gallery->count() > 2)
                <div class="flex shrink-0 gap-2" {!! $ds->reveal(1) !!}>
                    <button type="button" @click="go(-1)" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary" aria-label="Geser ke kiri"><x-icon name="arrow-left" class="size-5" /></button>
                    <button type="button" @click="go(1)" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary" aria-label="Geser ke kanan"><x-icon name="arrow-right" class="size-5" /></button>
                </div>
            @endif
        </div>

        <div x-ref="strip" class="mt-12 mr-[calc(50%-50vw)] flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 [scrollbar-width:none] sm:gap-5 [&::-webkit-scrollbar]:hidden" tabindex="0" aria-label="Galeri foto">
            @foreach ($company->gallery as $item)
                <figure class="group w-[72%] shrink-0 snap-start sm:w-[42%] lg:w-[27%]" {!! $ds->reveal($loop->index) !!}>
                    <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="relative block w-full overflow-hidden {{ $ds->img() }}" aria-label="Lihat {{ $item->title ?: 'foto' }}">
                        <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" decoding="async" class="aspect-[3/4] w-full object-cover transition duration-700 group-hover:scale-105">
                        <span class="absolute inset-0 bg-black/0 transition group-hover:bg-black/10"></span>
                    </button>
                    <figcaption class="mt-4 flex items-baseline gap-3">
                        <span class="font-mono text-xs text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="min-w-0">
                            <span class="block truncate font-medium text-ink">{{ $item->title ?: $company->name }}</span>
                            @if ($item->category)<span class="block text-xs text-muted">{{ $item->category }}</span>@endif
                        </span>
                    </figcaption>
                </figure>
            @endforeach
            <span class="w-1 shrink-0 sm:w-4" aria-hidden="true"></span>
        </div>
    </div>
    @include('components.company.partials.lightbox')
</section>
