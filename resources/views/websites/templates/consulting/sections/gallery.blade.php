{{-- Consulting gallery: calm editorial mosaic with generous whitespace. --}}
<section id="gallery" class="border-t border-stone-200 bg-white py-24 lg:py-32" x-data="lightbox">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Galeri</p>
                <h2 class="mt-6 font-heading text-4xl leading-tight text-stone-900 md:text-5xl">{{ $section->title ?: 'Di balik layar' }}</h2>
            </div>
            @if ($section->subtitle)<p class="text-stone-500 lg:col-span-4 lg:col-start-9">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-16 columns-1 gap-8 sm:columns-2 lg:columns-3">
            @foreach ($company->gallery as $item)
                <figure class="mb-8 break-inside-avoid">
                    <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group block w-full overflow-hidden">
                        <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="{{ ['aspect-[4/5]', 'aspect-[4/3]', 'aspect-square'][$loop->index % 3] }} w-full object-cover grayscale-[50%] transition duration-700 group-hover:scale-[1.02] group-hover:grayscale-0">
                    </button>
                    @if ($item->title)
                        <figcaption class="mt-3 flex items-baseline justify-between gap-4 text-sm">
                            <span class="font-heading text-stone-900 italic">{{ $item->title }}</span>
                            @if ($item->category)<span class="text-[11px] tracking-[0.2em] text-stone-400 uppercase">{{ $item->category }}</span>@endif
                        </figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-50/95 p-6">
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] object-contain shadow-2xl">
            <figcaption class="mt-4 text-center font-heading text-stone-700 italic" x-text="current?.title"></figcaption>
        </figure>
        <button type="button" @click="hide()" class="absolute top-6 right-6 text-stone-600" aria-label="Tutup"><x-icon name="x" class="size-6" /></button>
    </div>
</section>
