{{-- Modern Business testimonials: Alpine carousel with rating summary. --}}
@php
    $count = $company->testimonials->count();
    $avg = $count ? number_format($company->testimonials->avg('rating'), 1) : null;
@endphp
<section id="testimonials" class="relative overflow-hidden bg-slate-50 py-20 lg:py-32"
         x-data="{ i: 0, n: {{ $count }}, timer: null,
                   go(k) { this.i = (k + this.n) % this.n },
                   play() { clearInterval(this.timer); if (this.n > 1) this.timer = setInterval(() => this.go(this.i + 1), 7000) } }"
         x-init="play()">
    <div class="absolute top-0 right-0 size-[28rem] translate-x-1/3 -translate-y-1/3 rounded-full bg-secondary/15 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 size-[28rem] -translate-x-1/3 translate-y-1/3 rounded-full bg-primary/15 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-7xl gap-12 px-5 sm:px-6 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-4">
            <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold text-primary"><x-icon name="heart" class="size-3.5" /> Testimoni</span>
            <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Dicintai oleh Klien Kami' }}</h2>
            <p class="mt-5 text-slate-500">{{ $section->subtitle ?: 'Cerita dari mereka yang telah tumbuh bersama kami.' }}</p>
            @if ($avg)
                <div class="mt-8 inline-flex items-center gap-4 rounded-2xl bg-white p-4 pr-6 shadow-sm ring-1 ring-slate-100">
                    <span class="font-heading text-4xl font-semibold text-slate-900">{{ $avg }}</span>
                    <div>
                        <x-site.stars :rating="(int) round($avg)" />
                        <p class="mt-1 text-xs text-slate-500">dari {{ $count }} ulasan klien</p>
                    </div>
                </div>
            @endif
            @if ($count > 1)
                <div class="mt-8 flex gap-3">
                    <button type="button" @click="go(i - 1); play()" class="inline-flex size-12 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-900 hover:text-white" aria-label="Sebelumnya"><x-icon name="arrow-left" class="size-5" /></button>
                    <button type="button" @click="go(i + 1); play()" class="inline-flex size-12 items-center justify-center rounded-full bg-linear-to-br from-primary to-secondary text-on-primary shadow-lg shadow-primary/30 transition hover:scale-105" aria-label="Berikutnya"><x-icon name="arrow-right" class="size-5" /></button>
                </div>
            @endif
        </div>

        <div class="lg:col-span-8">
            <div class="grid">
                @foreach ($company->testimonials as $testimonial)
                    <figure class="col-start-1 row-start-1 flex flex-col rounded-brand bg-white p-8 shadow-[0_20px_60px_-15px_rgb(15_23_42/0.15)] ring-1 ring-slate-100 sm:p-12"
                            x-show="i === {{ $loop->index }}"
                            x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition duration-300 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-x-8"
                            @if (! $loop->first) x-cloak @endif>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex size-14 items-center justify-center rounded-2xl bg-linear-to-br from-primary to-secondary text-on-primary"><x-icon name="quote" class="size-7" /></span>
                            <x-site.stars :rating="$testimonial->rating" />
                        </div>
                        <blockquote class="mt-8 flex-1 font-heading text-xl leading-relaxed font-medium text-slate-800 sm:text-2xl">“{{ $testimonial->testimonial }}”</blockquote>
                        <figcaption class="mt-10 flex items-center gap-4 border-t border-slate-100 pt-6">
                            <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-14 rounded-full object-cover ring-4 ring-primary/10" />
                            <div>
                                <p class="font-semibold text-slate-900">{{ $testimonial->customer_name }}</p>
                                @if ($testimonial->company)<p class="text-sm text-slate-500">{{ $testimonial->company }}</p>@endif
                            </div>
                            <span class="ml-auto hidden font-heading text-sm text-slate-300 sm:block">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
            @if ($count > 1)
                <div class="mt-6 flex justify-center gap-2 lg:justify-start">
                    @foreach ($company->testimonials as $testimonial)
                        <button type="button" @click="go({{ $loop->index }}); play()" class="h-2 rounded-full transition-all" :class="i === {{ $loop->index }} ? 'w-8 bg-primary' : 'w-2 bg-slate-300'" aria-label="Testimoni {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
