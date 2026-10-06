{{-- Testimonials: Minimal — list of quotes separated by hairlines, small-caps authors, no cards. --}}
<section id="testimonials" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Testimoni', 'title' => $section->title ?: 'Dalam Kata-Kata Klien', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 border-t border-line">
            @foreach ($company->testimonials as $testimonial)
                <figure class="grid gap-5 border-b border-line py-10 md:grid-cols-12 md:gap-8 md:py-12" {!! $ds->reveal($loop->index) !!}>
                    <figcaption class="order-2 md:order-1 md:col-span-4">
                        <span class="block text-xs font-semibold tracking-[0.2em] text-ink uppercase">{{ $testimonial->customer_name }}</span>
                        @if ($testimonial->company)<span class="mt-1 block text-xs tracking-[0.14em] text-muted uppercase">{{ $testimonial->company }}</span>@endif
                    </figcaption>
                    <blockquote class="order-1 md:order-2 md:col-span-8">
                        <p class="heading text-xl !leading-snug text-ink sm:text-2xl lg:text-[1.75rem]">“{{ $testimonial->testimonial }}”</p>
                    </blockquote>
                </figure>
            @endforeach
        </div>
    </div>
</section>
