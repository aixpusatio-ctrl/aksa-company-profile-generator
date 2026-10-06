{{-- Products: Showcase — alternating large rows: big product image on one side, details, price and CTA on the other. --}}
@php($products = $company->products)
<section id="products" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Produk', 'title' => $section->title ?: 'Dirancang dengan Sepenuh Hati', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-16 space-y-20 lg:mt-24 lg:space-y-32">
            @foreach ($products as $product)
                @php($flip = $loop->odd === false)
                <article class="group grid items-center gap-8 lg:grid-cols-12 lg:gap-16">
                    <div class="relative lg:col-span-7 {{ $flip ? 'lg:order-2' : '' }}" {!! $ds->reveal(0, $flip ? 'right' : 'left') !!}>
                        <div class="overflow-hidden {{ $ds->img() }}">
                            <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full object-cover transition duration-[1200ms] group-hover:scale-105 lg:aspect-[5/4]" />
                        </div>
                    </div>
                    <div class="lg:col-span-5 {{ $flip ? 'lg:order-1' : '' }}" {!! $ds->reveal(1) !!}>
                        <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase">
                            <span class="font-mono text-muted">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($products->count(), 2, '0', STR_PAD_LEFT) }}</span>
                            @if ($product->category)<span class="h-px w-8 bg-line"></span>{{ $product->category }}@endif
                        </p>
                        <h3 class="heading mt-5 text-4xl break-words sm:text-5xl">{{ $product->name }}</h3>
                        @if ($product->description)<p class="mt-6 text-lead text-muted">{{ $product->description }}</p>@endif
                        @if ($product->formattedPrice())
                            <p class="mt-8 border-t border-line pt-6"><span class="block text-xs tracking-[0.16em] text-muted uppercase">Harga</span><span class="heading mt-1 block text-2xl sm:text-3xl">{{ $product->formattedPrice() }}</span></p>
                        @endif
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Pesan sekarang', 'kind' => 'primary', 'class' => 'w-full sm:w-auto'])
                            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Tanya detail', 'kind' => 'ghost', 'class' => 'w-full sm:w-auto'])
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
