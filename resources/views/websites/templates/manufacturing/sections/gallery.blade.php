{{-- Manufacturing gallery: horizontal factory tour strip with lightbox. --}}
<section id="gallery" class="bg-slate-50 py-20 lg:py-28" x-data="lightbox">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="font-mono text-xs font-semibold tracking-widest text-primary uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Factory Tour</p>
                <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-4xl">{{ $section->title ?: 'Fasilitas Produksi Kami' }}</h2>
                @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
            </div>
            <div class="flex gap-2">
                <button type="button" @click="$refs.strip.scrollBy({ left: -360, behavior: 'smooth' })" class="inline-flex size-11 items-center justify-center border border-slate-300 bg-white text-slate-700 hover:border-slate-900" aria-label="Sebelumnya"><x-icon name="arrow-left" class="size-4" /></button>
                <button type="button" @click="$refs.strip.scrollBy({ left: 360, behavior: 'smooth' })" class="inline-flex size-11 items-center justify-center bg-slate-900 text-white hover:bg-primary hover:text-on-primary" aria-label="Berikutnya"><x-icon name="arrow-right" class="size-4" /></button>
            </div>
        </div>
    </div>
    <div x-ref="strip" class="mt-10 flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth px-6 pb-4 [scrollbar-width:thin] xl:px-[max(1.5rem,calc((100vw-80rem)/2+1.5rem))]">
        @foreach ($company->gallery as $item)
            <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative w-72 shrink-0 snap-start overflow-hidden rounded-brand text-left sm:w-96">
                <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                <span class="absolute top-3 left-3 bg-white/95 px-2 py-1 font-mono text-[11px] font-semibold text-slate-900">STN-{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                    <span class="block text-sm font-semibold text-white">{{ $item->title }}</span>
                    @if ($item->category)<span class="font-mono text-[11px] text-white/70 uppercase">{{ $item->category }}</span>@endif
                </span>
            </button>
        @endforeach
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/95 p-6">
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] object-contain">
            <figcaption class="mt-3 text-center font-mono text-xs tracking-wider text-white/80 uppercase" x-text="current?.title"></figcaption>
        </figure>
        <button type="button" @click="hide()" class="absolute top-5 right-5 inline-flex size-10 items-center justify-center bg-white/10 text-white" aria-label="Tutup"><x-icon name="x" class="size-5" /></button>
    </div>
</section>
