{{-- Corporate products: 4-column product cards with category & price. --}}
<section id="products" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="text-sm font-bold tracking-widest text-primary uppercase">Produk</p>
                <h2 class="mt-3 font-heading text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $section->title ?: 'Produk Unggulan' }}</h2>
                @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
            </div>
            <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary">Minta penawaran <x-icon name="arrow-right" class="size-4" /></a>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->products as $product)
                <article class="group overflow-hidden rounded-brand border border-slate-200 bg-white transition hover:shadow-xl hover:shadow-slate-900/5">
                    <div class="overflow-hidden">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" />
                    </div>
                    <div class="p-5">
                        @if ($product->category)
                            <span class="text-xs font-semibold tracking-wide text-primary uppercase">{{ $product->category }}</span>
                        @endif
                        <h3 class="mt-1 font-heading text-lg font-bold text-slate-900">{{ $product->name }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $product->description }}</p>
                        @if ($product->formattedPrice())
                            <p class="mt-4 font-heading text-lg font-extrabold text-slate-900">{{ $product->formattedPrice() }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
