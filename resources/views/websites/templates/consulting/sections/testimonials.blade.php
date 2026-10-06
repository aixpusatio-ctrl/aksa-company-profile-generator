{{-- Consulting testimonials: one large serif quote at a time, Alpine slider with dots. --}}
@php($count = $company->testimonials->count())
<section id="testimonials" class="border-y border-stone-200 bg-white py-24 lg:py-32"
    x-data="{ i: 0, n: {{ $count }}, timer: null, go(k) { this.i = (k + this.n) % this.n }, start() { if (this.n > 1) this.timer = setInterval(() => this.go(this.i + 1), 7000) }, stop() { clearInterval(this.timer) } }"
    x-init="start()" @mouseenter="stop()" @mouseleave="start()">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">{{ $section->title ?: 'Kata Klien' }}</p>
        @if ($section->subtitle)<p class="mt-3 text-stone-500">{{ $section->subtitle }}</p>@endif
        <span class="mt-10 block font-heading text-8xl leading-none text-primary/30" aria-hidden="true">“</span>

        <div class="grid">
            @foreach ($company->testimonials as $testimonial)
                <figure x-show="i === {{ $loop->index }}" @if (! $loop->first) x-cloak @endif
                    x-transition:enter="transition duration-700 ease-out" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
                    class="col-start-1 row-start-1">
                    <blockquote class="font-heading text-2xl leading-snug text-stone-900 md:text-4xl md:leading-snug">{{ $testimonial->testimonial }}</blockquote>
                    <figcaption class="mt-10 flex flex-col items-center gap-3">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-14 rounded-full object-cover grayscale" />
                        <div>
                            <p class="text-sm font-semibold tracking-wide text-stone-900">{{ $testimonial->customer_name }}</p>
                            @if ($testimonial->company)<p class="mt-0.5 text-xs tracking-[0.2em] text-stone-400 uppercase">{{ $testimonial->company }}</p>@endif
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        @if ($count > 1)
            <div class="mt-12 flex items-center justify-center gap-6">
                <button type="button" @click="go(i - 1)" class="text-stone-400 transition hover:text-stone-900" aria-label="Sebelumnya"><x-icon name="arrow-left" class="size-5" /></button>
                <div class="flex items-center gap-3">
                    @foreach ($company->testimonials as $testimonial)
                        <button type="button" @click="go({{ $loop->index }})" class="h-1.5 rounded-full transition-all duration-500" :class="i === {{ $loop->index }} ? 'w-8 bg-primary' : 'w-1.5 bg-stone-300 hover:bg-stone-400'" aria-label="Testimoni {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
                <button type="button" @click="go(i + 1)" class="text-stone-400 transition hover:text-stone-900" aria-label="Berikutnya"><x-icon name="arrow-right" class="size-5" /></button>
            </div>
        @endif
    </div>
</section>
