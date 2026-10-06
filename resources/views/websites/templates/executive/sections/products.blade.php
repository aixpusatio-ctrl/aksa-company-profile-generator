{{-- Executive products: ivory band, refined offering list with gold rules and price. --}}
<section id="products" class="bg-stone-50 py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="flex items-center justify-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary"></span> Produk <span class="h-px w-10 bg-primary"></span></p>
            <h2 class="mt-6 font-heading text-4xl leading-[1.1] font-medium text-secondary md:text-5xl">{{ $section->title ?: 'Produk & Solusi Eksklusif' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-slate-500">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-20 grid gap-x-12 gap-y-14 md:grid-cols-2">
            @foreach ($company->products as $product)
                <article class="group grid grid-cols-[7rem_1fr] gap-6 sm:grid-cols-[9rem_1fr]">
                    <div class="relative p-1.5">
                        <div class="absolute inset-0 border border-primary/40"></div>
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="relative aspect-square w-full object-cover" />
                    </div>
                    <div class="flex flex-col border-b border-slate-200 pb-6">
                        @if ($product->category)<p class="text-[10px] tracking-[0.3em] text-primary uppercase">{{ $product->category }}</p>@endif
                        <div class="mt-2 flex items-baseline justify-between gap-4">
                            <h3 class="font-heading text-2xl leading-tight font-medium text-secondary">{{ $product->name }}</h3>
                            @if ($product->formattedPrice())
                                <span class="hidden shrink-0 font-heading text-xl text-secondary italic sm:block">{{ $product->formattedPrice() }}</span>
                            @endif
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ $product->description }}</p>
                        @if ($product->formattedPrice())
                            <span class="mt-3 font-heading text-lg text-secondary italic sm:hidden">{{ $product->formattedPrice() }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
