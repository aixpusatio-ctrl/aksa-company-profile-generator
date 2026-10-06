{{-- Corporate gallery: uniform grid with lightbox. --}}
<section id="gallery" class="py-20 lg:py-28" x-data="lightbox">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold tracking-widest text-primary uppercase">Galeri</p>
            <h2 class="mt-3 font-heading text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $section->title ?: 'Galeri Perusahaan' }}</h2>
            @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-12 grid grid-cols-2 gap-4 md:grid-cols-3">
            @foreach ($company->gallery as $item)
                <button type="button" @click="show(@js($item->url('image')), @js($item->title))" class="group relative overflow-hidden rounded-brand">
                    <img src="{{ $item->url('image') }}" alt="{{ $item->title }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                    @if ($item->title)
                        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4 text-left text-sm font-semibold text-white opacity-0 transition group-hover:opacity-100">{{ $item->title }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="current" x-transition.opacity @click="hide()" @keydown.escape.window="hide()" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-6">
        <figure class="max-w-5xl" @click.stop>
            <img :src="current?.src" :alt="current?.title" class="max-h-[80vh] rounded-brand object-contain">
            <figcaption class="mt-3 text-center text-sm text-white/80" x-text="current?.title"></figcaption>
        </figure>
    </div>
</section>
