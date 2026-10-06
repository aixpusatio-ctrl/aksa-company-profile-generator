{{-- Technology products: feature cards with gradient borders. --}}
<section id="products" class="relative py-24 lg:py-32">
    <div class="absolute top-1/3 left-0 size-96 -translate-x-1/2 rounded-full bg-secondary/15 blur-[120px]"></div>
    <div class="relative mx-auto max-w-7xl px-5 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="font-mono text-sm text-primary">// produk</p>
            <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Produk yang siap dipakai hari ini' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-slate-400">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->products as $product)
                <article class="group relative rounded-brand bg-linear-to-br from-primary/50 via-white/10 to-secondary/50 p-px transition duration-300 hover:from-primary hover:to-secondary hover:shadow-[0_0_60px_-20px_var(--brand-primary)]">
                    <div class="flex h-full flex-col overflow-hidden rounded-[calc(var(--brand-radius)-1px)] bg-slate-950">
                        <div class="relative overflow-hidden">
                            <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[16/10] w-full object-cover opacity-80 transition duration-700 group-hover:scale-105 group-hover:opacity-100" />
                            <div class="absolute inset-0 bg-linear-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                            @if ($product->category)
                                <span class="absolute top-4 left-4 rounded-md border border-white/15 bg-slate-950/70 px-2 py-1 font-mono text-[11px] text-primary backdrop-blur">{{ \Illuminate\Support\Str::slug($product->category) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-6 pt-2">
                            <h3 class="font-heading text-xl font-semibold text-white">{{ $product->name }}</h3>
                            <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-400">{{ $product->description }}</p>
                            <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-5">
                                <span class="font-mono text-sm text-white">{{ $product->formattedPrice() ?: 'custom_quote' }}</span>
                                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary">Detail <x-icon name="arrow-up-right" class="size-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" /></a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
