{{-- Manufacturing testimonials: client feedback as spec-style cards with rating meter. --}}
<section id="testimonials" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="font-mono text-xs font-semibold tracking-widest text-primary uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Umpan Balik Klien</p>
                <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-4xl">{{ $section->title ?: 'Dipercaya Mitra Industri' }}</h2>
                @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
                @php($avg = round($company->testimonials->avg('rating') ?: 5, 1))
                <div class="mt-8 inline-flex items-center gap-5 border border-slate-200 p-5">
                    <span class="font-heading text-5xl font-bold text-slate-900">{{ number_format($avg, 1, ',', '.') }}</span>
                    <div>
                        <x-site.stars :rating="round($avg)" />
                        <p class="mt-1 font-mono text-[11px] text-slate-500 uppercase">Rata-rata · {{ $company->testimonials->count() }} ulasan</p>
                    </div>
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:col-span-8">
                @foreach ($company->testimonials as $testimonial)
                    <figure class="flex flex-col rounded-brand border border-slate-200 bg-white p-6 {{ $loop->first ? 'sm:col-span-2 border-l-4 border-l-primary' : '' }}">
                        <div class="flex items-center justify-between">
                            <x-icon name="quote" class="size-7 text-primary" />
                            <x-site.stars :rating="$testimonial->rating" />
                        </div>
                        <blockquote class="mt-4 flex-1 leading-relaxed text-slate-700 {{ $loop->first ? 'text-lg' : 'text-sm' }}">“{{ $testimonial->testimonial }}”</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3 border-t border-dashed border-slate-200 pt-4">
                            <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-10 rounded-brand object-cover" />
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900">{{ $testimonial->customer_name }}</p>
                                <p class="truncate font-mono text-[11px] text-slate-500 uppercase">{{ $testimonial->company }}</p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </div>
</section>
