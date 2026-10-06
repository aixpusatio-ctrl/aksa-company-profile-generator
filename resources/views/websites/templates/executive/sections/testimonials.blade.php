{{-- Executive testimonials: one large centered italic quote with gold pager. --}}
@php($count = $company->testimonials->count())
<section id="testimonials" class="relative overflow-hidden bg-secondary py-24 lg:py-36" x-data="{ i: 0, n: {{ $count }}, timer: null }" x-init="if (n > 1) timer = setInterval(() => i = (i + 1) % n, 8000)">
    <div class="pointer-events-none absolute top-10 left-1/2 -translate-x-1/2 font-heading text-[16rem] leading-none text-primary/10 select-none md:text-[22rem]">“</div>
    <div class="relative mx-auto max-w-4xl px-6 text-center">
        <p class="flex items-center justify-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary/70"></span> {{ $section->title ?: 'Testimoni Klien' }} <span class="h-px w-10 bg-primary/70"></span></p>
        @if ($section->subtitle)<p class="mt-4 text-on-secondary/60">{{ $section->subtitle }}</p>@endif

        <div class="mt-14 grid">
            @foreach ($company->testimonials as $testimonial)
                <figure x-show="i === {{ $loop->index }}" @if (! $loop->first) x-cloak @endif x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0 translate-y-3" x-transition:leave="transition duration-300 absolute inset-x-0" x-transition:leave-end="opacity-0" class="col-start-1 row-start-1">
                    <blockquote class="font-heading text-3xl leading-snug font-medium text-on-secondary italic md:text-[2.6rem] md:leading-[1.25]">“{{ $testimonial->testimonial }}”</blockquote>
                    <figcaption class="mt-12 flex flex-col items-center">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-16 rounded-full object-cover ring-1 ring-primary ring-offset-4 ring-offset-secondary" />
                        <p class="mt-5 font-heading text-xl text-on-secondary">{{ $testimonial->customer_name }}</p>
                        @if ($testimonial->company)<p class="mt-1 text-[10px] tracking-[0.3em] text-primary uppercase">{{ $testimonial->company }}</p>@endif
                    </figcaption>
                </figure>
            @endforeach
        </div>

        @if ($count > 1)
            <div class="mt-12 flex items-center justify-center gap-6">
                <button type="button" @click="i = (i - 1 + n) % n; clearInterval(timer)" class="text-on-secondary/50 transition hover:text-primary" aria-label="Sebelumnya"><x-icon name="arrow-left" class="size-5" stroke="1" /></button>
                <div class="flex items-center gap-3">
                    @foreach ($company->testimonials as $testimonial)
                        <button type="button" @click="i = {{ $loop->index }}; clearInterval(timer)" class="h-px transition-all duration-500" :class="i === {{ $loop->index }} ? 'w-12 bg-primary' : 'w-6 bg-on-secondary/30'" aria-label="Testimoni {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
                <button type="button" @click="i = (i + 1) % n; clearInterval(timer)" class="text-on-secondary/50 transition hover:text-primary" aria-label="Berikutnya"><x-icon name="arrow-right" class="size-5" stroke="1" /></button>
            </div>
        @endif
    </div>
</section>
