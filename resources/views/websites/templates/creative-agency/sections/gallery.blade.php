{{-- Creative gallery: bento grid with mixed tile sizes and lightbox. --}}
@php($tiles = ['md:col-span-2 md:row-span-2', '', 'md:row-span-2', '', 'md:col-span-2', ''])
<section id="gallery" class="py-20 lg:py-28" x-data="lightbox">
    <div class="mx-auto max-w-[90rem] px-6 sm:px-10">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <h2 class="font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter text-neutral-950 md:text-7xl lg:text-8xl">{{ $section->title ?: 'Studio life' }}<span class="text-primary">.</span></h2>
            @if ($section->subtitle)<p class="max-w-sm text-neutral-600">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-16 grid auto-rows-[11rem] grid-cols-2 gap-4 md:auto-rows-[14rem] md:grid-cols-4">
            @foreach ($company->gallery as $item)
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative overflow-hidden rounded-brand {{ $tiles[$loop->index % count($tiles)] }}">
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="size-full object-cover transition duration-700 group-hover:scale-110 group-hover:rotate-1">
                    @if ($item->title)
                        <span class="absolute bottom-3 left-3 translate-y-2 rotate-[-3deg] rounded-full bg-white px-3 py-1 text-xs font-extrabold text-neutral-950 opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100">{{ $item->title }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-secondary/95 p-6">
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] rounded-brand object-contain">
            <figcaption class="mt-4 text-center font-heading text-lg font-extrabold text-on-secondary" x-text="current?.title"></figcaption>
        </figure>
        <button type="button" @click="hide()" class="absolute top-5 right-5 inline-flex size-12 items-center justify-center rounded-full bg-primary text-on-primary" aria-label="Tutup"><x-icon name="x" class="size-6" /></button>
    </div>
</section>
