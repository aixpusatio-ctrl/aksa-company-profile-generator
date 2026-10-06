{{-- Professional services products: service packages styled as fee cards. --}}
<section id="products" class="border-t border-slate-200 bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-8 bg-secondary"></span> Paket Layanan <span class="h-px w-8 bg-secondary"></span></p>
            <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900 md:text-4xl">{{ $section->title ?: 'Pilihan Paket & Biaya' }}</h2>
            <p class="mt-4 text-slate-600">{{ $section->subtitle ?: 'Biaya disampaikan secara transparan di awal. Paket dapat disesuaikan dengan kebutuhan Anda.' }}</p>
        </div>
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->products as $product)
                <article class="flex flex-col rounded-brand border border-slate-200 bg-white p-7 transition hover:border-primary hover:shadow-xl hover:shadow-slate-900/5">
                    <div class="flex items-start gap-4">
                        <x-site.img :src="$product->url('image')" :alt="$product->name" icon="document" class="size-16 shrink-0 rounded-brand object-cover" />
                        <div class="min-w-0">
                            @if ($product->category)
                                <p class="text-[11px] font-semibold tracking-wide text-primary uppercase">{{ $product->category }}</p>
                            @endif
                            <h3 class="mt-1 font-heading text-lg leading-snug text-slate-900">{{ $product->name }}</h3>
                        </div>
                    </div>
                    <p class="mt-5 flex-1 text-sm leading-relaxed text-slate-600">{{ $product->description }}</p>
                    <div class="mt-6 flex items-end justify-between border-t border-dashed border-slate-200 pt-5">
                        <div>
                            <p class="text-[11px] text-slate-500">Mulai dari</p>
                            <p class="font-heading text-xl text-slate-900">{{ $product->formattedPrice() ?: 'Hubungi kami' }}</p>
                        </div>
                        <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-1.5 rounded-btn border border-primary px-4 py-2 text-xs font-semibold text-primary transition hover:bg-primary hover:text-on-primary">Pilih <x-icon name="arrow-right" class="size-3.5" /></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
