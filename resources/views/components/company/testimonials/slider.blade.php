{{-- Testimonials: Slider — snap carousel of quote cards (1/2/3 per view) with arrows and rating stars. --}}
<section id="testimonials" class="{{ $ds->section($tone, 'overflow-hidden') }}" x-data="{ go(dir) { const t = $refs.track; t.scrollBy({ left: dir * Math.max(t.clientWidth * 0.9, 280), behavior: 'smooth' }) } }">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            @include('components.company.partials.heading', ['eyebrow' => 'Testimoni', 'title' => $section->title ?: 'Cerita dari Klien Kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            @if ($company->testimonials->count() > 1)
                <div class="flex shrink-0 gap-2 {{ $company->testimonials->count() <= 3 ? 'lg:hidden' : '' }} {{ $company->testimonials->count() <= 2 ? 'sm:hidden' : '' }}" {!! $ds->reveal(1) !!}>
                    <button type="button" @click="go(-1)" class="inline-flex size-12 items-center justify-center rounded-full border border-line bg-card text-ink transition hover:border-primary hover:text-primary" aria-label="Sebelumnya"><x-icon name="arrow-left" class="size-5" /></button>
                    <button type="button" @click="go(1)" class="inline-flex size-12 items-center justify-center rounded-full bg-primary text-on-primary transition hover:brightness-110" aria-label="Berikutnya"><x-icon name="arrow-right" class="size-5" /></button>
                </div>
            @endif
        </div>

        <div x-ref="track" class="-mx-5 mt-12 flex snap-x snap-mandatory scroll-px-5 gap-5 overflow-x-auto px-5 pb-4 [scrollbar-width:none] sm:mx-0 sm:scroll-px-0 sm:px-0 [&::-webkit-scrollbar]:hidden" tabindex="0" aria-label="Daftar testimoni">
            @foreach ($company->testimonials as $testimonial)
                <figure class="{{ $ds->card('flex w-[86%] shrink-0 snap-start flex-col p-7 sm:w-[calc(50%-0.625rem)] sm:p-8 lg:w-[calc((100%-2.5rem)/3)]', false) }}" {!! $ds->reveal($loop->index) !!}>
                    <div class="flex items-center justify-between">
                        <x-site.stars :rating="$testimonial->rating ?: 5" class="text-ink" />
                        <x-icon name="quote" class="size-7 text-primary/30" />
                    </div>
                    <blockquote class="mt-6 flex-1 text-[1.05rem] leading-relaxed text-ink">“{{ $testimonial->testimonial }}”</blockquote>
                    <figcaption class="mt-8 flex items-center gap-3">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-12 shrink-0 rounded-full object-cover" />
                        <span class="min-w-0">
                            <span class="block truncate font-semibold text-ink">{{ $testimonial->customer_name }}</span>
                            @if ($testimonial->company)<span class="block truncate text-sm text-muted">{{ $testimonial->company }}</span>@endif
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
