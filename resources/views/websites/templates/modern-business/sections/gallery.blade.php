{{-- Modern Business gallery: feature tile + rounded grid, lightbox. --}}
@php
    // First image is a 2x2 feature tile; the last tile stretches to fill the final row.
    $n = $company->gallery->count();
    $restMobile = ($n - 1) % 2;
    $restDesktop = $n > 5 ? ($n - 5) % 4 : ($n - 1) % 2 * 0;
    $lastSpan = ['lg:col-span-1', 'lg:col-span-4', 'lg:col-span-3', 'lg:col-span-2'][$restDesktop] ?? 'lg:col-span-1';
@endphp
<section id="gallery" class="py-20 lg:py-32" x-data="lightbox">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold text-primary"><x-icon name="camera" class="size-3.5" /> Galeri</span>
            <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Momen di Balik Layar' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-lg text-slate-500">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid auto-rows-[11rem] grid-cols-2 gap-4 sm:auto-rows-[14rem] lg:grid-cols-4">
            @foreach ($company->gallery as $item)
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))"
                        class="group relative overflow-hidden rounded-brand shadow-sm {{ $loop->first ? 'col-span-2 row-span-2' : '' }} {{ ! $loop->first && $loop->last ? ($restMobile ? 'col-span-2 ' : '').$lastSpan : '' }}">
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-700 group-hover:scale-110">
                    <span class="absolute inset-0 bg-linear-to-t from-slate-900/70 via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></span>
                    <span class="absolute inset-x-3 bottom-3 flex translate-y-3 items-center justify-between gap-2 rounded-full bg-white/95 py-1.5 pr-1.5 pl-4 text-left opacity-0 shadow-lg backdrop-blur transition group-hover:translate-y-0 group-hover:opacity-100">
                        <span class="truncate text-xs font-semibold text-slate-900">{{ $item->title ?: 'Lihat foto' }}</span>
                        <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-primary text-on-primary"><x-icon name="plus" class="size-3.5" /></span>
                    </span>
                </button>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 p-6 backdrop-blur-sm">
        <button type="button" class="absolute top-5 right-5 inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white" aria-label="Tutup"><x-icon name="x" class="size-5" /></button>
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] rounded-brand object-contain shadow-2xl">
            <figcaption class="mt-4 text-center text-sm font-medium text-white/80" x-text="current?.title"></figcaption>
        </figure>
    </div>
</section>
