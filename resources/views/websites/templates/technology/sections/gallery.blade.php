{{-- Technology gallery: horizontal snap rail of framed screenshots with mono captions + lightbox. --}}
<section id="gallery" class="py-24 lg:py-32" x-data="lightbox">
    <div class="mx-auto flex max-w-7xl flex-col justify-between gap-6 px-5 sm:px-6 md:flex-row md:items-end">
        <div class="max-w-2xl">
            <p class="font-mono text-sm text-primary">// galeri</p>
            <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Di balik layar' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-slate-400">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="flex gap-2">
            <button type="button" @click="$refs.rail.scrollBy({ left: -420, behavior: 'smooth' })" class="inline-flex size-11 items-center justify-center rounded-lg border border-white/10 text-slate-300 transition hover:border-primary/50 hover:text-primary" aria-label="Geser kiri"><x-icon name="arrow-left" class="size-4" /></button>
            <button type="button" @click="$refs.rail.scrollBy({ left: 420, behavior: 'smooth' })" class="inline-flex size-11 items-center justify-center rounded-lg border border-white/10 text-slate-300 transition hover:border-primary/50 hover:text-primary" aria-label="Geser kanan"><x-icon name="arrow-right" class="size-4" /></button>
        </div>
    </div>
    <div x-ref="rail" class="mt-12 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-px-5 px-5 pb-4 [scrollbar-width:none] sm:scroll-px-6 sm:px-6 xl:scroll-px-[max(1.5rem,calc((100vw-80rem)/2+1.5rem))] xl:px-[max(1.5rem,calc((100vw-80rem)/2+1.5rem))] [&::-webkit-scrollbar]:hidden">
        @foreach ($company->gallery as $item)
            <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group w-[85%] shrink-0 snap-start text-left sm:w-[26rem]">
                <div class="relative overflow-hidden rounded-brand border border-white/10 bg-slate-900 p-1.5 transition group-hover:border-primary/40">
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="aspect-[4/3] w-full rounded-[calc(var(--brand-radius)-4px)] object-cover opacity-85 transition duration-500 group-hover:opacity-100">
                </div>
                <div class="mt-3 flex items-center justify-between gap-3 px-1 font-mono text-xs">
                    <span class="truncate text-slate-300">{{ $item->title }}</span>
                    @if ($item->category)<span class="shrink-0 text-slate-600">{{ \Illuminate\Support\Str::slug($item->category) }}</span>@endif
                </div>
            </button>
        @endforeach
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/95 p-6 backdrop-blur">
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] rounded-brand border border-white/10 object-contain">
            <figcaption class="mt-3 text-center font-mono text-xs text-slate-400" x-text="current?.title"></figcaption>
        </figure>
    </div>
</section>
