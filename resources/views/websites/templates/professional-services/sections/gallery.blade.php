{{-- Professional services gallery: "our office" mosaic with featured tile + lightbox. --}}
<section id="gallery" class="py-20 lg:py-28" x-data="lightbox">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Galeri</p>
                <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900 md:text-4xl">{{ $section->title ?: 'Kantor & Kegiatan Kami' }}</h2>
                @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
            </div>
        </div>
        <div class="mt-12 grid auto-rows-[11rem] grid-cols-2 gap-4 md:auto-rows-[13rem] md:grid-cols-4">
            @foreach ($company->gallery as $item)
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative overflow-hidden rounded-brand {{ $loop->first ? 'col-span-2 row-span-2' : '' }}">
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 bg-primary/0 transition group-hover:bg-primary/40"></span>
                    @if ($item->title)
                        <span class="absolute bottom-3 left-3 rounded-brand bg-white/95 px-3 py-1.5 text-left text-xs font-semibold text-slate-900 opacity-0 transition group-hover:opacity-100">{{ $item->title }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 p-6">
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] rounded-brand object-contain">
            <figcaption class="mt-3 text-center text-sm text-white/80" x-text="current?.title"></figcaption>
        </figure>
        <button type="button" @click="hide()" class="absolute top-5 right-5 inline-flex size-10 items-center justify-center rounded-full bg-white/10 text-white" aria-label="Tutup"><x-icon name="x" class="size-5" /></button>
    </div>
</section>
