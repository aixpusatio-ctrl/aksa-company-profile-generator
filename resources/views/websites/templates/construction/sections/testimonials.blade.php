{{-- Construction testimonials: one big quote with client selector tabs. --}}
<section id="testimonials" class="relative overflow-hidden bg-stone-100 py-20 lg:py-32" x-data="{ active: 0 }">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 sm:px-6 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-4">
            <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Testimoni</p>
            <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight text-stone-950 uppercase sm:text-5xl">{{ $section->title ?: 'Kata Mereka Tentang Kami' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-stone-500">{{ $section->subtitle }}</p>@endif
            <div class="mt-10 space-y-2">
                @foreach ($company->testimonials as $testimonial)
                    <button type="button" @click="active = {{ $loop->index }}" class="flex w-full items-center gap-4 border-l-4 p-3 text-left transition" :class="active === {{ $loop->index }} ? 'border-primary bg-white shadow-sm' : 'border-transparent hover:bg-white/60'">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-12 shrink-0 object-cover" />
                        <span class="min-w-0">
                            <span class="block truncate font-heading text-base font-bold tracking-wide text-stone-950 uppercase">{{ $testimonial->customer_name }}</span>
                            @if ($testimonial->company)<span class="block truncate text-xs text-stone-500">{{ $testimonial->company }}</span>@endif
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
        <div class="relative lg:col-span-8">
            <div class="relative h-full bg-stone-950 p-8 text-white sm:p-12 lg:p-16">
                <span class="absolute -top-6 right-8 inline-flex size-20 items-center justify-center bg-primary text-on-primary sm:size-24"><x-icon name="quote" class="size-10 sm:size-12" /></span>
                <div class="grid">
                    @foreach ($company->testimonials as $testimonial)
                        <figure class="col-start-1 row-start-1" x-show="active === {{ $loop->index }}" x-transition:enter="transition duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" @if (! $loop->first) x-cloak @endif>
                            <x-site.stars :rating="$testimonial->rating" class="[&_svg]:size-5" />
                            <blockquote class="mt-8 font-heading text-2xl leading-snug font-medium sm:text-3xl lg:text-4xl">“{{ $testimonial->testimonial }}”</blockquote>
                            <figcaption class="mt-10 flex items-center gap-4">
                                <span class="h-1 w-12 bg-primary"></span>
                                <span class="font-heading text-sm font-bold tracking-[0.2em] uppercase">{{ $testimonial->customer_name }}</span>
                                @if ($testimonial->company)<span class="text-sm text-stone-400">— {{ $testimonial->company }}</span>@endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
                <div class="absolute inset-x-0 bottom-0 h-2 bg-[repeating-linear-gradient(-45deg,var(--brand-primary)_0_12px,transparent_12px_24px)]"></div>
            </div>
        </div>
    </div>
</section>
