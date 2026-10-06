{{-- Products: Grid — product cards with image, category, price. --}}
<section id="products" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Produk', 'title' => $section->title ?: 'Produk Unggulan', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <div {!! $ds->reveal(1) !!}>@include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Minta penawaran', 'kind' => 'link'])</div>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->products as $product)
                <article class="{{ $ds->card('group overflow-hidden') }}" {!! $ds->reveal($loop->index) !!}>
                    <div class="overflow-hidden">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105" />
                    </div>
                    <div class="p-5">
                        @if ($product->category)<p class="text-xs font-semibold tracking-wide text-primary uppercase">{{ $product->category }}</p>@endif
                        <h3 class="heading mt-1 text-lg">{{ $product->name }}</h3>
                        @if ($product->description)<p class="mt-2 line-clamp-2 text-sm text-muted">{{ $product->description }}</p>@endif
                        @if ($product->formattedPrice())<p class="mt-4 font-semibold text-ink">{{ $product->formattedPrice() }}</p>@endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
