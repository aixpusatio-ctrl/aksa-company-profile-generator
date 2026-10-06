{{-- Minimal gallery: simple two-column grid with captions and lightbox. --}}
<section id="gallery" class="mx-auto max-w-4xl px-6" x-data="lightbox">
    <div class="border-t border-neutral-200 py-16 md:py-24">
        <div class="grid gap-8 md:grid-cols-4">
            <div>
                <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
                <h2 class="mt-1 text-sm font-medium text-neutral-950">Galeri</h2>
            </div>
            <div class="md:col-span-3">
                <p class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-3xl">{{ $section->title ?: 'Dokumentasi.' }}</p>
                @if ($section->subtitle)<p class="mt-4 text-neutral-500">{{ $section->subtitle }}</p>@endif
            </div>
        </div>
        <div class="mt-12 grid grid-cols-2 gap-x-4 gap-y-8 md:gap-x-6">
            @foreach ($company->gallery as $item)
                <figure>
                    <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="block w-full overflow-hidden bg-neutral-100">
                        <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="aspect-[4/3] w-full object-cover grayscale transition duration-500 hover:grayscale-0">
                    </button>
                    @if ($item->title)
                        <figcaption class="mt-2 flex justify-between gap-3 text-xs text-neutral-500"><span>{{ $item->title }}</span><span class="text-neutral-300 tabular-nums">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-white/95 p-6">
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] object-contain">
            <figcaption class="mt-3 flex justify-between text-sm text-neutral-500"><span x-text="current?.title"></span><button type="button" @click="hide()" class="text-neutral-950">Tutup ×</button></figcaption>
        </figure>
    </div>
</section>
