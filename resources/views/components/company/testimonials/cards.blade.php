{{-- Testimonials: Cards — 3 quote cards with rating and author. --}}
<section id="testimonials" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Testimoni', 'title' => $section->title ?: 'Dipercaya Klien Kami', 'subtitle' => $section->subtitle, 'number' => $index])
        <div class="mt-14 grid gap-5 md:grid-cols-3">
            @foreach ($company->testimonials as $testimonial)
                <figure class="{{ $ds->card('flex flex-col p-7') }}" {!! $ds->reveal($loop->index) !!}>
                    <x-site.stars :rating="$testimonial->rating" class="text-ink" />
                    <blockquote class="mt-5 flex-1 leading-relaxed text-ink">“{{ $testimonial->testimonial }}”</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3 border-t border-line pt-5">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-11 rounded-full object-cover" />
                        <span>
                            <span class="block text-sm font-semibold text-ink">{{ $testimonial->customer_name }}</span>
                            <span class="block text-xs text-muted">{{ $testimonial->company }}</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
