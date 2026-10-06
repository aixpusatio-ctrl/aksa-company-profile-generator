{{-- Creative testimonials: dark band with a slow marquee of tilted quote cards. --}}
<section id="testimonials" class="overflow-hidden bg-secondary py-20 text-on-secondary lg:py-28">
    <div class="mx-auto max-w-[90rem] px-6 sm:px-10">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <h2 class="font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter md:text-7xl lg:text-8xl">{{ $section->title ?: 'Kata mereka' }}<span class="text-primary">!</span></h2>
            @if ($section->subtitle)<p class="max-w-sm text-on-secondary/60">{{ $section->subtitle }}</p>@endif
        </div>
    </div>
    <div class="group mt-16">
        <div class="flex w-max animate-marquee [animation-duration:60s] group-hover:[animation-play-state:paused]">
            @foreach ([1, 2] as $copy)
                <div class="flex shrink-0 gap-6 pr-6" @if ($copy === 2) aria-hidden="true" @endif>
                    @foreach ($company->testimonials->concat($company->testimonials->count() < 4 ? $company->testimonials : []) as $testimonial)
                        <figure class="flex w-80 shrink-0 flex-col rounded-3xl p-7 sm:w-[26rem] {{ $loop->index % 3 === 0 ? 'bg-primary text-on-primary -rotate-1' : ($loop->index % 3 === 1 ? 'bg-white text-neutral-900 rotate-1' : 'bg-white/10 text-on-secondary -rotate-2') }}">
                            <div class="flex items-center justify-between">
                                <span class="font-heading text-6xl leading-none font-extrabold">“</span>
                                <x-site.stars :rating="$testimonial->rating" />
                            </div>
                            <blockquote class="mt-2 flex-1 text-lg leading-relaxed font-medium">{{ $testimonial->testimonial }}</blockquote>
                            <figcaption class="mt-6 flex items-center gap-3">
                                <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-12 rounded-full object-cover" />
                                <div>
                                    <p class="font-bold">{{ $testimonial->customer_name }}</p>
                                    <p class="text-sm opacity-60">{{ $testimonial->company }}</p>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
