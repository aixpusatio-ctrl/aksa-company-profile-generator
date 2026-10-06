{{-- Modern Business products: horizontally scrollable soft cards with price pill. --}}
<section id="products" class="py-20 lg:py-32" x-data>
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold text-primary"><x-icon name="cube" class="size-3.5" /> Produk</span>
                <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Produk Pilihan untuk Anda' }}</h2>
                @if ($section->subtitle)<p class="mt-5 text-lg text-slate-500">{{ $section->subtitle }}</p>@endif
            </div>
            <div class="flex gap-3">
                <button type="button" @click="$refs.track.scrollBy({ left: -360, behavior: 'smooth' })" class="inline-flex size-12 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-900 hover:text-white" aria-label="Geser kiri"><x-icon name="arrow-left" class="size-5" /></button>
                <button type="button" @click="$refs.track.scrollBy({ left: 360, behavior: 'smooth' })" class="inline-flex size-12 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-900 hover:text-white" aria-label="Geser kanan"><x-icon name="arrow-right" class="size-5" /></button>
            </div>
        </div>
    </div>

    <div x-ref="track" class="mt-9 flex snap-x pt-3 snap-mandatory gap-6 overflow-x-auto scroll-smooth px-5 pb-8 scroll-px-5 [scrollbar-width:none] sm:px-6 sm:scroll-px-6 xl:px-[max(1.5rem,calc((100vw-80rem)/2+1.5rem))] xl:scroll-px-[max(1.5rem,calc((100vw-80rem)/2+1.5rem))] [&::-webkit-scrollbar]:hidden">
        @foreach ($company->products as $product)
            <article class="group flex w-[82%] shrink-0 snap-start flex-col overflow-hidden rounded-brand bg-white shadow-[0_8px_30px_rgb(15_23_42/0.07)] ring-1 ring-slate-100 transition hover:-translate-y-1 sm:w-[22rem]">
                <div class="relative m-2 overflow-hidden rounded-[calc(var(--brand-radius)-4px)] bg-slate-100">
                    <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105" />
                    @if ($product->category)
                        <span class="absolute top-3 left-3 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-800 backdrop-blur">{{ $product->category }}</span>
                    @endif
                </div>
                <div class="flex flex-1 flex-col px-6 pt-4 pb-6">
                    <h3 class="font-heading text-lg font-semibold text-slate-900">{{ $product->name }}</h3>
                    <p class="mt-2 line-clamp-3 flex-1 text-sm text-slate-500">{{ $product->description }}</p>
                    <div class="mt-6 flex items-center justify-between gap-3">
                        <span class="font-heading text-lg font-semibold text-slate-900">{{ $product->formattedPrice() ?: 'Hubungi kami' }}</span>
                        <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-1.5 rounded-btn bg-linear-to-r from-primary to-secondary px-4 py-2 text-xs font-semibold text-on-primary shadow-md shadow-primary/25 transition hover:opacity-90">Pesan <x-icon name="arrow-right" class="size-3.5" /></a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
