{{-- Consulting products: programs / packages as a refined price list. --}}
<section id="products" class="py-24 lg:py-32">
    <div class="mx-auto max-w-5xl px-6">
        <div class="text-center">
            <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Program</p>
            <h2 class="mt-6 font-heading text-4xl leading-tight text-stone-900 md:text-5xl">{{ $section->title ?: 'Program & Paket Pendampingan' }}</h2>
            @if ($section->subtitle)<p class="mx-auto mt-4 max-w-xl text-stone-500">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-16 border-t border-stone-300">
            @foreach ($company->products as $product)
                <article class="group grid gap-6 border-b border-stone-300 py-8 sm:grid-cols-[6rem_1fr_auto] sm:items-center">
                    <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="hidden aspect-square w-24 object-cover grayscale transition group-hover:grayscale-0 sm:block" />
                    <div>
                        @if ($product->category)<p class="text-[11px] tracking-[0.2em] text-primary uppercase">{{ $product->category }}</p>@endif
                        <h3 class="mt-1 font-heading text-2xl text-stone-900">{{ $product->name }}</h3>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-stone-500">{{ $product->description }}</p>
                    </div>
                    <p class="font-heading text-xl whitespace-nowrap text-stone-900 italic sm:text-right">{{ $product->formattedPrice() ?: 'Sesuai kebutuhan' }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
