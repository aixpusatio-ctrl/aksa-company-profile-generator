{{-- Creative products: packages as colorful cards with price stickers. --}}
<section id="products" class="px-3 sm:px-5">
    <div class="mx-auto max-w-[90rem] rounded-[2rem] bg-white px-6 py-20 sm:px-10 lg:py-28">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <h2 class="font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter text-neutral-950 md:text-7xl lg:text-8xl">{{ $section->title ?: 'Paket & produk' }}<span class="text-primary">.</span></h2>
            @if ($section->subtitle)<p class="max-w-sm text-neutral-600">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->products as $product)
                <article class="group relative flex flex-col rounded-brand p-3 transition duration-500 hover:-translate-y-2 {{ $loop->index % 3 === 1 ? 'bg-secondary text-on-secondary' : 'bg-neutral-100 text-neutral-950' }}">
                    <div class="overflow-hidden rounded-[calc(var(--brand-radius)*0.75)]">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-110" />
                    </div>
                    @if ($product->formattedPrice())
                        <span class="absolute top-6 right-6 rotate-6 rounded-full bg-primary px-4 py-2 font-heading text-sm font-extrabold text-on-primary shadow-lg transition group-hover:rotate-[-6deg]">{{ $product->formattedPrice() }}</span>
                    @endif
                    <div class="flex flex-1 flex-col p-4">
                        @if ($product->category)<p class="text-xs font-extrabold tracking-widest uppercase opacity-50">{{ $product->category }}</p>@endif
                        <h3 class="mt-2 font-heading text-2xl font-extrabold tracking-tight">{{ $product->name }}</h3>
                        <p class="mt-2 flex-1 text-sm opacity-70">{{ $product->description }}</p>
                        <a href="{{ $site->anchor('contact') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-extrabold">Pesan sekarang <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" /></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
