{{-- Testimonials: Masonry — CSS-columns wall of quote cards of varying length with a rating summary card. --}}
@php
    $items = $company->testimonials;
    $avg = $items->count() ? round((float) ($items->avg('rating') ?: 5), 1) : 5;
@endphp
<section id="testimonials" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Testimoni', 'title' => $section->title ?: 'Kata Mereka Tentang Kami', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 columns-1 gap-5 sm:columns-2 lg:columns-3 [&>*]:mb-5 [&>*]:break-inside-avoid">
            <div class="tone-primary relative overflow-hidden rounded-brand bg-primary p-8 text-on-primary" {!! $ds->reveal() !!}>
                <div class="absolute -top-16 -right-16 size-48 rounded-full bg-white/10 blur-2xl"></div>
                <p class="relative text-xs font-semibold tracking-[0.2em] uppercase opacity-80">Rata-rata penilaian</p>
                <p class="heading relative mt-4 text-7xl">{{ number_format($avg, 1, ',', '.') }}<span class="text-2xl opacity-60">/5</span></p>
                <x-site.stars :rating="(int) round($avg)" class="relative mt-4 [&_svg]:size-5" />
                <p class="relative mt-4 text-sm opacity-80">Berdasarkan {{ $items->count() }} ulasan klien {{ $company->name }}.</p>
            </div>

            @foreach ($items as $testimonial)
                                <figure class="{{ $ds->card('p-7') }}" {!! $ds->reveal($loop->iteration) !!}>
                    <x-site.stars :rating="$testimonial->rating ?: 5" class="text-ink" />
                    <blockquote class="mt-5 text-ink {{ $loop->index % 3 === 0 && mb_strlen($testimonial->testimonial) < 180 ? 'heading text-2xl !leading-snug' : 'text-[1.05rem] leading-relaxed' }}">“{{ $testimonial->testimonial }}”</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-10 shrink-0 rounded-full object-cover" />
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-ink">{{ $testimonial->customer_name }}</span>
                            @if ($testimonial->company)<span class="block text-xs text-muted">{{ $testimonial->company }}</span>@endif
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
