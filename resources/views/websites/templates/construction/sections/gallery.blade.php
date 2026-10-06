{{-- Construction gallery: tight edge-to-edge strip grid on black, lightbox. --}}
<section id="gallery" class="bg-stone-950 pt-20 lg:pt-28" x-data="lightbox">
    <div class="mx-auto flex max-w-7xl flex-col justify-between gap-6 px-5 sm:px-6 md:flex-row md:items-end">
        <div>
            <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Galeri</p>
            <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight text-white uppercase sm:text-5xl lg:text-6xl">{{ $section->title ?: 'Dari Lapangan' }}</h2>
        </div>
        @if ($section->subtitle)<p class="max-w-md text-stone-400">{{ $section->subtitle }}</p>@endif
    </div>
    <div class="mt-12 grid grid-cols-2 gap-1 md:grid-cols-3 lg:grid-cols-6">
        @foreach ($company->gallery as $item)
            <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative overflow-hidden bg-stone-900">
                <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="aspect-[3/4] w-full object-cover grayscale-[40%] transition duration-700 group-hover:scale-110 group-hover:grayscale-0">
                <span class="absolute inset-0 flex flex-col items-start justify-end bg-stone-950/0 p-4 text-left transition group-hover:bg-stone-950/60">
                    <span class="inline-flex size-10 translate-y-4 items-center justify-center bg-primary text-on-primary opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100"><x-icon name="plus" class="size-5" /></span>
                    @if ($item->title)
                        <span class="mt-3 translate-y-4 font-heading text-sm font-bold tracking-wider text-white uppercase opacity-0 transition delay-75 group-hover:translate-y-0 group-hover:opacity-100">{{ $item->title }}</span>
                    @endif
                </span>
            </button>
        @endforeach
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/95 p-6">
        <figure class="max-w-5xl border-t-4 border-primary" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] object-contain">
            <figcaption class="mt-3 text-center font-heading text-sm tracking-widest text-white uppercase" x-text="current?.title"></figcaption>
        </figure>
    </div>
</section>
