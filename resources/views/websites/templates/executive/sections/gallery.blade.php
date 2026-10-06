{{-- Executive gallery: wide cinematic strip mosaic with lightbox. --}}
<section id="gallery" class="relative bg-secondary pb-24 lg:pb-32" x-data="lightbox">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col items-start justify-between gap-6 border-t border-primary/20 pt-20 md:flex-row md:items-end">
            <div>
                <p class="flex items-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary"></span> Galeri</p>
                <h2 class="mt-6 font-heading text-4xl leading-[1.1] font-medium text-on-secondary md:text-5xl">{{ $section->title ?: 'Dalam Bingkai' }}</h2>
            </div>
            @if ($section->subtitle)<p class="max-w-md text-on-secondary/60">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid grid-cols-2 gap-3 md:auto-rows-[15rem] md:grid-cols-6 md:gap-4">
            @foreach ($company->gallery as $item)
                @php($span = match (true) { $loop->index % 5 === 0 && $loop->last => 'col-span-2 aspect-[16/9] md:col-span-6 md:row-span-2 md:aspect-auto', $loop->index % 5 === 0 => 'col-span-2 aspect-[16/9] md:col-span-4 md:row-span-2 md:aspect-auto', in_array($loop->index % 5, [1, 2]) => 'aspect-[4/3] md:col-span-2 md:aspect-auto', default => 'aspect-[4/3] md:col-span-3 md:aspect-auto' })
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative overflow-hidden bg-white/5 {{ $span }}">
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-[1200ms] group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-secondary/80 via-transparent to-transparent opacity-60 transition group-hover:opacity-100"></span>
                    <span class="pointer-events-none absolute inset-2 border border-primary/0 transition duration-500 group-hover:border-primary/60"></span>
                    @if ($item->title)
                        <span class="absolute bottom-4 left-5 text-left font-heading text-lg text-on-secondary opacity-0 transition group-hover:opacity-100">{{ $item->title }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-secondary/95 p-6">
        <figure class="max-w-5xl border border-primary/30 p-3" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[78vh] object-contain">
            <figcaption class="mt-3 text-center font-heading text-lg text-on-secondary/80 italic" x-text="current?.title"></figcaption>
        </figure>
    </div>
</section>
