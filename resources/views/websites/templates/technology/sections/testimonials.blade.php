{{-- Technology testimonials: masonry of glass cards, first one highlighted. --}}
<section id="testimonials" class="relative overflow-hidden border-y border-white/10 bg-slate-900/40 py-24 lg:py-32">
    <div class="absolute bottom-0 left-1/2 h-72 w-[50rem] max-w-full -translate-x-1/2 translate-y-1/2 rounded-full bg-primary/15 blur-[120px]"></div>
    <div class="relative mx-auto max-w-7xl px-5 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="font-mono text-sm text-primary">// testimoni</p>
            <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Dipercaya tim yang bergerak cepat' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-slate-400">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 gap-5 md:columns-2 lg:columns-3">
            @foreach ($company->testimonials as $testimonial)
                <figure class="mb-5 break-inside-avoid rounded-brand border p-7 backdrop-blur {{ $loop->first ? 'border-primary/30 bg-primary/[0.07] shadow-[0_0_60px_-25px_var(--brand-primary)]' : 'border-white/10 bg-slate-950/60' }}">
                    <div class="flex items-center justify-between">
                        <x-site.stars :rating="$testimonial->rating" />
                        <span class="font-mono text-[11px] text-slate-600">#{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <blockquote class="mt-5 leading-relaxed {{ $loop->first ? 'text-lg text-white' : 'text-slate-300' }}">“{{ $testimonial->testimonial }}”</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3 border-t border-white/10 pt-5">
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-10 rounded-lg object-cover" />
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $testimonial->customer_name }}</p>
                            @if ($testimonial->company)<p class="font-mono text-xs text-slate-500">@ {{ $testimonial->company }}</p>@endif
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
