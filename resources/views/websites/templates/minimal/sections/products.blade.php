{{-- Minimal products: index-style rows, name — category — price. --}}
<section id="products" class="mx-auto max-w-4xl px-6">
    <div class="grid gap-8 border-t border-neutral-200 py-16 md:grid-cols-4 md:py-24">
        <div>
            <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
            <h2 class="mt-1 text-sm font-medium text-neutral-950">Produk</h2>
        </div>
        <div class="md:col-span-3">
            <p class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-3xl">{{ $section->title ?: 'Produk.' }}</p>
            @if ($section->subtitle)<p class="mt-4 text-neutral-500">{{ $section->subtitle }}</p>@endif
            <div class="mt-10 grid gap-x-8 gap-y-12 sm:grid-cols-2">
                @foreach ($company->products as $product)
                    <article>
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full bg-neutral-100 object-cover" />
                        <div class="mt-4 flex items-baseline justify-between gap-4 border-b border-neutral-200 pb-3">
                            <h3 class="font-heading text-lg font-medium tracking-tight text-neutral-950">{{ $product->name }}</h3>
                            @if ($product->formattedPrice())
                                <span class="shrink-0 text-sm text-neutral-950 tabular-nums">{{ $product->formattedPrice() }}</span>
                            @endif
                        </div>
                        @if ($product->category)<p class="mt-3 text-xs text-neutral-400">{{ $product->category }}</p>@endif
                        <p class="mt-1 text-[15px] leading-relaxed text-neutral-500">{{ $product->description }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
