{{-- Professional services testimonials: rating summary + client review cards. --}}
@php($avg = round($company->testimonials->avg('rating') ?: 5, 1))
<section id="testimonials" class="bg-secondary/10 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Ulasan Klien</p>
                <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900 md:text-4xl">{{ $section->title ?: 'Kepercayaan yang Kami Jaga' }}</h2>
                @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
                <div class="mt-8 inline-flex items-center gap-5 rounded-brand bg-white p-5 ring-1 ring-slate-200">
                    <span class="font-heading text-5xl text-slate-900">{{ number_format($avg, 1, ',', '') }}</span>
                    <div>
                        <x-site.stars :rating="(int) round($avg)" class="text-slate-400" />
                        <p class="mt-1 text-xs text-slate-500">Rata-rata dari {{ $company->testimonials->count() }} ulasan klien</p>
                    </div>
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:col-span-8">
                @foreach ($company->testimonials as $testimonial)
                    <figure class="flex flex-col rounded-brand bg-white p-7 ring-1 ring-slate-200 {{ $loop->first && $company->testimonials->count() % 2 === 1 ? 'sm:col-span-2' : '' }}">
                        <div class="flex items-center justify-between">
                            <x-site.stars :rating="$testimonial->rating" class="text-slate-400" />
                            <x-icon name="quote" class="size-7 text-secondary/70" />
                        </div>
                        <blockquote class="mt-5 flex-1 leading-relaxed text-slate-700">{{ $testimonial->testimonial }}</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                            <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-11 rounded-full object-cover" />
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $testimonial->customer_name }}</p>
                                @if ($testimonial->company)<p class="text-xs text-slate-500">{{ $testimonial->company }}</p>@endif
                            </div>
                            <span class="ml-auto inline-flex items-center gap-1 text-[11px] font-medium text-primary"><x-icon name="check-circle" class="size-3.5" /> Klien terverifikasi</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </div>
</section>
