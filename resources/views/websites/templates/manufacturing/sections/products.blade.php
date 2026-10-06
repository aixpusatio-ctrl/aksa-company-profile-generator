{{-- Manufacturing products: catalog with category tabs and spec rows. --}}
@php($categories = $company->products->pluck('category')->filter()->unique()->values())
<section id="products" class="bg-slate-50 py-20 lg:py-28" x-data="{ tab: 'all' }">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col justify-between gap-6 border-b-2 border-slate-900 pb-8 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="font-mono text-xs font-semibold tracking-widest text-primary uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Katalog Produk</p>
                <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-4xl">{{ $section->title ?: 'Produk & Spesifikasi' }}</h2>
                <p class="mt-4 text-slate-600">{{ $section->subtitle ?: 'Diproduksi di fasilitas kami dengan kontrol kualitas ketat di setiap tahap.' }}</p>
            </div>
            <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 font-mono text-xs font-semibold tracking-wider text-slate-900 uppercase hover:text-primary"><x-icon name="document" class="size-4" /> Minta datasheet lengkap</a>
        </div>

        @if ($categories->count() > 1)
            <div class="mt-8 flex gap-0 overflow-x-auto border border-slate-300 bg-white sm:inline-flex">
                <button type="button" @click="tab = 'all'" class="shrink-0 px-5 py-3 font-mono text-xs font-semibold tracking-wider uppercase transition" :class="tab === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-primary'">Semua <span class="opacity-60">({{ $company->products->count() }})</span></button>
                @foreach ($categories as $category)
                    <button type="button" @click="tab = @js($category)" class="shrink-0 border-l border-slate-300 px-5 py-3 font-mono text-xs font-semibold tracking-wider uppercase transition" :class="tab === @js($category) ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-primary'">{{ $category }}</button>
                @endforeach
            </div>
        @endif

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->products as $product)
                <article x-show="tab === 'all' || tab === @js($product->category)" class="group flex flex-col rounded-brand border border-slate-200 bg-white transition hover:border-slate-900 hover:shadow-[6px_6px_0_0_var(--brand-primary)]">
                    <div class="relative overflow-hidden rounded-t-brand bg-slate-100">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="cube" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" />
                        <span class="absolute top-0 left-0 bg-slate-900 px-3 py-1.5 font-mono text-[11px] font-semibold text-white">#{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="font-heading text-lg font-bold text-slate-900">{{ $product->name }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $product->description }}</p>
                        <dl class="mt-5 divide-y divide-dashed divide-slate-200 border-y border-slate-200 font-mono text-xs">
                            <div class="flex justify-between py-2"><dt class="text-slate-500 uppercase">Kategori</dt><dd class="font-semibold text-slate-800">{{ $product->category ?: 'Umum' }}</dd></div>
                            <div class="flex justify-between py-2"><dt class="text-slate-500 uppercase">Kode</dt><dd class="font-semibold text-slate-800">{{ strtoupper(\Illuminate\Support\Str::substr(\Illuminate\Support\Str::slug($product->category ?: 'PRD'), 0, 3)) }}-{{ str_pad($loop->iteration * 10, 4, '0', STR_PAD_LEFT) }}</dd></div>
                            <div class="flex justify-between py-2"><dt class="text-slate-500 uppercase">Harga</dt><dd class="font-semibold text-primary">{{ $product->formattedPrice() ?: 'By request' }}</dd></div>
                        </dl>
                        <a href="{{ $site->anchor('contact') }}" class="mt-5 inline-flex items-center justify-between gap-2 text-xs font-bold tracking-wider text-slate-900 uppercase group-hover:text-primary">Minta penawaran <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" /></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
