{{-- Construction products: heavy bordered spec cards with price block. --}}
<section id="products" class="bg-white py-20 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Produk & Sewa</p>
                <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight text-stone-950 uppercase sm:text-5xl lg:text-6xl">{{ $section->title ?: 'Material & Peralatan' }}</h2>
            </div>
            @if ($section->subtitle)<p class="max-w-lg text-stone-500 lg:justify-self-end">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->products as $product)
                <article class="group flex flex-col border-2 border-stone-950 bg-white transition hover:-translate-y-1 hover:shadow-[8px_8px_0_0_var(--brand-primary)]">
                    <div class="relative overflow-hidden border-b-2 border-stone-950">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105" />
                        <span class="absolute top-0 left-0 bg-stone-950 px-3 py-1.5 font-heading text-xs font-bold tracking-[0.2em] text-white uppercase">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}{{ $product->category ? ' / '.$product->category : '' }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="font-heading text-xl font-bold tracking-wide text-stone-950 uppercase">{{ $product->name }}</h3>
                        <p class="mt-3 flex-1 text-sm leading-relaxed text-stone-500">{{ $product->description }}</p>
                    </div>
                    <div class="flex items-stretch border-t-2 border-stone-950">
                        <span class="flex flex-1 items-center px-6 py-4 font-heading text-lg font-bold text-stone-950">{{ $product->formattedPrice() ?: 'Hubungi Kami' }}</span>
                        <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 bg-primary px-5 font-heading text-xs font-bold tracking-widest text-on-primary uppercase transition hover:brightness-110">Pesan <x-icon name="arrow-right" class="size-4" /></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
