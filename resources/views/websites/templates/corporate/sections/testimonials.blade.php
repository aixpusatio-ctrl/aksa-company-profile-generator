{{-- Corporate testimonials: dark band with 3 quote cards. --}}
<section id="testimonials" class="bg-secondary py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold tracking-widest text-primary uppercase">Testimoni</p>
            <h2 class="mt-3 font-heading text-3xl font-extrabold text-on-secondary md:text-4xl">{{ $section->title ?: 'Apa Kata Klien Kami' }}</h2>
            @if ($section->subtitle)<p class="mt-4 text-on-secondary/70">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach ($company->testimonials as $testimonial)
                <figure class="flex flex-col rounded-brand bg-white/5 p-8 ring-1 ring-white/10">
                    <x-site.stars :rating="$testimonial->rating" class="text-on-secondary" />
                    <blockquote class="mt-5 flex-1 text-on-secondary/85 leading-relaxed">“{{ $testimonial->testimonial }}”</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-11 rounded-full object-cover" />
                        <div>
                            <p class="text-sm font-bold text-on-secondary">{{ $testimonial->customer_name }}</p>
                            <p class="text-xs text-on-secondary/60">{{ $testimonial->company }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
