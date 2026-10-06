{{-- Testimonials: Quote — one large centered quote at a time with author, arrows and pager dots. --}}
@php($items = $company->testimonials->values())
<section id="testimonials" class="{{ $ds->section($tone, 'overflow-hidden') }}">
    <div class="{{ $ds->container('narrow') }} text-center"
        x-data="{ i: 0, n: {{ max(1, $items->count()) }}, paused: false, next() { this.i = (this.i + 1) % this.n }, prev() { this.i = (this.i - 1 + this.n) % this.n } }"
        @if ($items->count() > 1) x-init="setInterval(() => { if (! paused) next() }, 8000)" @endif
        @mouseenter="paused = true" @mouseleave="paused = false" @focusin="paused = true" @focusout="paused = false">
        <div {!! $ds->reveal() !!}>
            {!! $ds->eyebrow('Testimoni', $index) !!}
            <h2 class="heading mt-4 text-lg text-muted sm:text-xl">{{ $section->title ?: 'Apa Kata Klien Kami' }}</h2>
        </div>

        <div class="heading mt-10 h-14 text-[7rem] !leading-none text-primary sm:h-16 sm:text-[8.5rem]" aria-hidden="true" {!! $ds->reveal(1) !!}>“</div>

        <div class="mt-6 grid" {!! $ds->reveal(2) !!} aria-live="polite">
            @foreach ($items as $testimonial)
                <figure class="[grid-area:1/1] transition duration-700 ease-out"
                    @unless ($loop->first) x-cloak @endunless
                    :class="i === {{ $loop->index }} ? 'opacity-100 translate-y-0' : 'pointer-events-none opacity-0 translate-y-4'"
                    :aria-hidden="i !== {{ $loop->index }}">
                    <blockquote class="heading text-[clamp(1.45rem,3.3vw,2.6rem)] !leading-[1.25] text-ink">{{ $testimonial->testimonial }}</blockquote>
                    <figcaption class="mt-10 flex flex-col items-center gap-3">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-14 rounded-full object-cover ring-4 ring-primary/15" />
                        <span>
                            <span class="block font-semibold text-ink">{{ $testimonial->customer_name }}</span>
                            @if ($testimonial->company)<span class="block text-sm text-muted">{{ $testimonial->company }}</span>@endif
                        </span>
                        @if ($testimonial->rating)<x-site.stars :rating="$testimonial->rating" class="text-ink" />@endif
                    </figcaption>
                </figure>
            @endforeach
        </div>

        @if ($items->count() > 1)
            <div class="mt-10 flex items-center justify-center gap-4">
                <button type="button" @click="prev()" class="inline-flex size-11 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary" aria-label="Testimoni sebelumnya"><x-icon name="arrow-left" class="size-4" /></button>
                <div class="flex items-center gap-1">
                    @foreach ($items as $testimonial)
                        <button type="button" @click="i = {{ $loop->index }}" class="group inline-flex h-11 items-center px-1" aria-label="Testimoni {{ $loop->iteration }}">
                            <span class="block h-1.5 rounded-full transition-all duration-500" :class="i === {{ $loop->index }} ? 'w-8 bg-primary' : 'w-1.5 bg-ink/20 group-hover:bg-ink/40'"></span>
                        </button>
                    @endforeach
                </div>
                <button type="button" @click="next()" class="inline-flex size-11 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary" aria-label="Testimoni berikutnya"><x-icon name="arrow-right" class="size-4" /></button>
            </div>
        @endif
    </div>
</section>
